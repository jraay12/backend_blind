<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Validator;


class LocationController extends Controller
{
    public function create(Request $request){

        $validator=Validator::make($request->all(), [
            "user_id" => "required",
            "latitude" => "required|string",
            "longitude" => "required|string",

        ]);

        if($validator->fails()) {
          
            return response(['message' => $validator->errors()->first()]);
        }

        

        $location = Location::create([
            'user_id' => $request['user_id'],
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            
        ]);

        return response()->json(['message' => 'successfully created'], 200);

    }
}
