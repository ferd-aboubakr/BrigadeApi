<?php

namespace App\Http\Controllers;

use App\Models\Plate;
use App\Models\Category;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlateController extends Controller
{
    public function index(Request $request)
    {
        $plates = Plate::where('is_available', true)
            ->with(['category', 'ingredients'])
            ->get();
            
        return response()->json($plates);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'is_available' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'ingredient_ids' => 'array',
            'ingredient_ids.*' => 'exists:ingredients,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $plate = Plate::create($validator->validated());
        
        if ($request->has('ingredient_ids')) {
            $plate->ingredients()->attach($request->ingredient_ids);
        }
        
        return response()->json($plate->load(['category', 'ingredients']), 201);
    }

    public function show($id)
    {
        $plate = Plate::with(['category', 'ingredients'])
            ->findOrFail($id);
            
        return response()->json($plate);
    }

    public function update(Request $request, $id)
    {
        $plate = Plate::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string',
            'is_available' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'ingredient_ids' => 'array',
            'ingredient_ids.*' => 'exists:ingredients,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $plate->update($validator->validated());
        
        if ($request->has('ingredient_ids')) {
            $plate->ingredients()->sync($request->ingredient_ids);
        }
        
        return response()->json($plate->load(['category', 'ingredients']));
    }

    public function destroy($id)
    {
        $plate = Plate::findOrFail($id);
        $plate->delete();
        
        return response()->json(['message' => 'Plate deleted successfully']);
    }
}
