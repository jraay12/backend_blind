<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Contacts;
use App\Models\Address;



class ContactsController extends Controller
{
    public function contacts(Request $request){

        $validator = Validator::make($request->all(), [
            "user_id" => "required",
            "contact_number" => "required|string|min:11",
            "name" => "required|string",
            "street" => "required|string",
            "city" => "required|string",
            "zipcode" => "required|string",
            "province" => "required|string"
        ]);
    
        if($validator->fails()) {
            return response(['message' => $validator->errors()->first()]);
        }
    
        // Check if the user has reached the contact limit
        $contactLimit = 3;
        $userContactsCount = Contacts::where('user_id', $request['user_id'])->count();
    
        if ($userContactsCount >= $contactLimit){
            return response()->json(['message' => 'Contact Limit Exceed']);
        }
    
        $contactAddress = Address::firstOrCreate([
            'street' => $request['street'],
            'city' => $request['city'],
            'zipcode' => $request['zipcode'],
            'province' => $request['province']
        ]);
    
        $contacts = Contacts::firstOrCreate([
            'user_id' => $request['user_id'],
            'address_id' => $contactAddress->id,
            'contact_number' => $request['contact_number'],
            'name' => $request['name']
        ]);
    
        return response()->json(['message' => 'Contact created successfully'], 201);
    }
    

    public function contactDetails($id){
        
        $contactDetails = Contacts::where('user_id', $id)->with('address')->get();
        $countContact = Contacts::where('user_id', $id)->count();

        return response()->json([
            'contacts' => $contactDetails,
            'count' => $countContact
        ]);

       
    }
}