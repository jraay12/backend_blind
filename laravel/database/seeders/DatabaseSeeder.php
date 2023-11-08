<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Address;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        


        Address::factory()->create([
            'id' => 1,
            'street' => '12-28 Nazareth',
            'city' => "Davao",
            'zipcode' => "9000",
            "province" => "misamis oriental"
        ]);
        
        User::factory()->create([
            'id' => 1,
            'name' => 'John Ray',
            'email' => 'johnray@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt("qwerty"),
            'address_id' => 1,
            'contact_number' => '09619400079',
            'role' => 'admin',

        ]);

        
        
        

        
    }
}