<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::all();
        return response()->json($ingredients);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:ingredients,name',
            'tags' => 'array',
            'tags.*' => 'in:contains_meat,contains_sugar,contains_cholesterol,contains_gluten,contains_lactose'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ingredient = Ingredient::create($validator->validated());
        
        return response()->json($ingredient, 201);
    }

    public function update(Request $request, $id)
    {
        $ingredient = Ingredient::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:ingredients,name,' . $id,
            'tags' => 'array',
            'tags.*' => 'in:contains_meat,contains_sugar,contains_cholesterol,contains_gluten,contains_lactose'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ingredient->update($validator->validated());
        
        return response()->json($ingredient);
    }

    public function destroy($id)
    {
        $ingredient = Ingredient::findOrFail($id);
        $ingredient->delete();
        
        return response()->json(['message' => 'Ingredient deleted successfully']);
    }
}
