<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['name','tags'];

    protected function casts(): array {
        return ['tags' => 'array']; 
    }

    public function plates (){
        return $this->belongsToMany(plate::class, 'plate_ingredient');
    }



}
