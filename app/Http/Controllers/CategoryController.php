<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();
        
        if ($request->has('active')) {
            $query->where('is_active', $request->boolean('active'));
        }
        
        $categories = $query->withCount('plates')->get();
        
        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:categories,name',
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::create($validator->validated());
        
        return response()->json($category, 201);
    }

    public function show($id)
    {
        $category = Category::with(['plates' => function($query) {
            $query->where('is_available', true);
        }])->findOrFail($id);
        
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:categories,name,' . $id,
            'description' => 'nullable|string',
            'color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category->update($validator->validated());
        
        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        if ($category->plates()->where('is_available', true)->exists()) {
            return response()->json([
                'message' => 'Cannot delete category with active plates'
            ], 422);
        }
        
        $category->delete();
        
        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function plates($id)
    {
        $category = Category::findOrFail($id);
        
        $plates = $category->plates()
            ->where('is_available', true)
            ->with('ingredients')
            ->get();
            
        return response()->json($plates);
    }
}
