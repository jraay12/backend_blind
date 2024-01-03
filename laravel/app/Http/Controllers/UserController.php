<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordLink;
use App\Models\User;
use App\Models\Address;
use App\Models\Contacts;
use Illuminate\Support\Facades\DB;



class UserController extends Controller
{
    public function create(Request $request){

        $validator=Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|email|unique:users",
            "password" => "required|string|min:6",
            "street" => "required|string",
            "city" => "required|string",
            "zipcode" => "required|string",
            'contact_number' => "required|string",
            "province" => "required|string"

        ]);

        if($validator->fails()) {
          
            return response(['message' => $validator->errors()->first()]);
        }

        
        $address = Address::firstOrCreate([
            'street' => $request['street'],
            'city' => $request['city'],
            'zipcode' => $request['zipcode'],
            'province' => $request['province']
        ]);

        

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'address_id' => $address->id,
            'contact_number' => $request['contact_number'],
            'role' => 'user'
        ]);

        return response()->json(['message' => 'successfully created'], 201);

    }

    public function userDetails() {
        $totalUser = User::with('address')->where('id', '!=', 1)->get();
        $totalCount = User::where('id', '!=', 1)->count();
        return response()->json([
            "User" => $totalUser,
            "Count" => $totalCount
        ]);
    }

    public function login(Request $request)
    {
        $responseData = [
            'status' => 'fail',
            'message' => 'Authentication Failed',
            'data' => null
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        
        if ($validator->fails()) {
            $responseData['message'] = $validator->errors()->first();
            return response($responseData, 400);
        }

    
        $credentials = request(['email', 'password']);

        try {

            if (Auth::attempt($credentials)) {

                $user = $request->user();
    
                $user->tokens()->delete();
                
                $responseData = [
                    'status' => 'success',
                    'message' => 'Successful Login',
                    'data' => [
                        'token' => $user->createToken(Auth::user())->plainTextToken,
                        'user' => $user
                    ]
                ];
                return response($responseData, 200);
            }

            return response($responseData, 400);
        }
        catch(\Throwable $th) {
            throw $th;
        }
    }

   public function update(Request $request, $id){
        
    $user = User::find($id);

    if(!$user){
        return response()->json(["message" => "User not Found"], 404);
    }
    $validator = Validator::make($request->all(), [
        
        'name' => 'nullable|string',
        'email' => 'nullable|email|unique:users,email,'.$user->id, // Ensure unique email excluding current user
        'password' => 'nullable|string|min:6',
        'address_id' => 'nullable|integer|exists:addresses,id', // Add validations for other address fields if updating them individually
        'contact_number' => 'nullable|string',
        'role' => 'nullable|string', // Add validation logic for role updates if desired
    ]);

    if ($validator->fails()) {
        return response()->json([
            "message" => $validator->errors()->first(),
        ], 400);
    }


    $user->update(array_intersect(
        $request->all()
    ));

    // Save updated user
    $user->save();

    // Return updated user data
    return response()->json([
        "message" => "User updated successfully",
        "user" => $user,
    ], 200);
   }

    public function destroy($id)
    {
        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User Not Found"], 404);
        }

        if ($user) {
            $user->delete();
            return response()->json(["message" => "successfully deleted"], 200);
        } else {
            return response()->json(["message" => "failed to delete"], 400);;
        }

    }

    public function userContactsDetails($userId) {
        $userContactDetails = Contacts::with('address')->where('user_id', '=', $userId)->get();
    
        return response()->json([
            'User' => $userContactDetails
        ]);
    }

    public function sendResetLinkEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    // Retrieve the user based on the email
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['message' => 'Email not found'], 404);
    }

    // Create a password reset token and store it in the database
    $token = $this->createAndStoreToken($user->email);

    // Send password reset link via email
    Mail::to($user->email)->send(new ResetPasswordLink($token));

    return response()->json(['message' => 'Password reset link sent']);
}

protected function createAndStoreToken($email)
{
    // Generate a unique token
    $token = sha1(uniqid($email, true));

    // Store the token in the database
    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $email],
        ['token' => $token, 'created_at' => now()]
    );

    return $token;
}

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $token = $request->input('token');
        info("Received Token: $token");

        // Retrieve the user by token
        $user = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->first();

        info("Database Result: " . json_encode($user));

        if (!$user) {
            // Handle invalid token
            return Response::json(['error' => 'Invalid token'], 422);
        }

        // Update the user's password
        DB::table('users')
            ->where('email', $user->email)
            ->update(['password' => Hash::make($request->password)]);

        // Delete the password reset token
        DB::table('password_reset_tokens')->where('email', $user->email)->delete();

        // Return success JSON response
        return Response::json(['message' => 'Password has been reset successfully'], 200);
    }
}
