<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'image', 'is_available', 'category_id'];


    public function ingredients (){
        return $this->belongsToMany(Ingredient::class, 'plate_ingredient');
    }

    public function recommendations (){
        return $this->hasMany(Recommendation::class);
    }

     public function category (){

    return $this->belongsTo(Category::class);
    }

     public function users (){

    return $this->hasMany(USER::class);
    }


}
