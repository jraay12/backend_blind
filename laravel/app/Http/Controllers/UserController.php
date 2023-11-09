<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Address;

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


//     public function update(Request $request, $id)
// {
//     $user = User::find($id);

//     $updated = false;

//     if ($request->filled('name')) {
//         $user->name = $request->input('name');
//         $updated = true;
//     }

//     if ($request->filled('email')) {
//         $user->email = $request->input('email');
//         $updated = true;
//     }

//     if ($updated) {
//         $user->save();
//         return response()->json(['message' => 'User updated successfully'], 200);
//     } else {
//         return response()->json(['message' => 'No updates made'], 200);
//     }
// }

}
