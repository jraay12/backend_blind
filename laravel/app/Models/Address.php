<?php

namespace App\Models;
use App\Models\User;
use App\Models\Address;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'street',
        'zipcode',
        'province',
        'city'
    ];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function contacts() {
        return $this->hasMany(Contact::class);
    }
}
