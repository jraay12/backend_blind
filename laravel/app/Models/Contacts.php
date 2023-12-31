<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'address_id',
    'name',
    'contact_number'
    ];
    

    protected $primaryKey = 'id';

    public function address(){
        return $this->belongsTo(Address::class);
    }
}
