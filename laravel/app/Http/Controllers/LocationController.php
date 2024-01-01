<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class LocationController extends Controller
{
    public function create(Request $request, $id){


        $user = User::find($id);

        if(!$user){
            return response()->json(["message" => "User not Found"], 404);
        }

        $validator=Validator::make($request->all(), [
            "latitude" => "required|string",
            "longitude" => "required|string",

        ]);

        if($validator->fails()) {
          
            return response(['message' => $validator->errors()->first()]);
        }

        

        $location = Location::create([
            'user_id' => $id,
            'latitude' => $request['latitude'],
            'longitude' => $request['longitude'],
            
        ]);

        return response()->json(['message' => 'successfully created'], 200);

    }

    public function getLocation($id)
{
    $locations = Location::where('user_id', $id)->get();

    if ($locations->isNotEmpty()) {
        $data = $locations->map(function ($location) {
            return [
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ];
        });

        return response()->json($data);
    } else {
        return response()->json(['error' => 'Locations not found']);
    }
}
}
