<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
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
        $totalUser = User::with('address')->get();

        return response()->json([
            "User" => $totalUser,
            
        ]);
    }
}
