<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Plate;
use App\Models\Recommendation;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function index()
    {
        $totalPlates = Plate::count();
        $totalCategories = Category::count();
        $totalIngredients = Ingredient::count();
        $totalRecommendations = Recommendation::count();
        
        $mostRecommended = Recommendation::selectRaw('plate_id, AVG(score) as avg_score')
            ->groupBy('plate_id')
            ->orderBy('avg_score', 'desc')
            ->with('plate')
            ->first();
            
        $leastRecommended = Recommendation::selectRaw('plate_id, AVG(score) as avg_score')
            ->groupBy('plate_id')
            ->orderBy('avg_score', 'asc')
            ->with('plate')
            ->first();
            
        $categoryWithMostPlates = Category::withCount('plates')
            ->orderBy('plates_count', 'desc')
            ->first();
        
        return response()->json([
            'total_plates' => $totalPlates,
            'total_categories' => $totalCategories,
            'total_ingredients' => $totalIngredients,
            'total_recommendations' => $totalRecommendations,
            'most_recommended_plate' => $mostRecommended ? [
                'id' => $mostRecommended->plate_id,
                'name' => $mostRecommended->plate->name,
                'average_score' => round($mostRecommended->avg_score, 2)
            ] : null,
            'least_recommended_plate' => $leastRecommended ? [
                'id' => $leastRecommended->plate_id,
                'name' => $leastRecommended->plate->name,
                'average_score' => round($leastRecommended->avg_score, 2)
            ] : null,
            'category_with_most_plates' => $categoryWithMostPlates ? [
                'id' => $categoryWithMostPlates->id,
                'name' => $categoryWithMostPlates->name,
                'plates_count' => $categoryWithMostPlates->plates_count
            ] : null
        ]);
    }
}
