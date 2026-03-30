<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'color', 'is_active'];
     
    public function plates(){

    return $this->hasMany(Plate::class);
    }

     public function user (){

    return $this->belongsTo(User::class);
    }


}
