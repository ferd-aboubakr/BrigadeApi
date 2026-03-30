<?php

namespace App\Http\Controllers;

use App\Models\Plate;
use App\Models\Recommendation;
use App\Jobs\AnalyzePlateCompatibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller
{
    public function analyze($plate_id)
    {
        $plate = Plate::find($plate_id);
        
        $recommendation = Recommendation::create([
            'user_id' => Auth::id(),
            'plate_id' => $plate_id,
            'score' => 0,
            'warning_message' => null,
            'status' => 'processing'
        ]);
        
        AnalyzePlateCompatibility::dispatch($recommendation);
        
        return response()->json([
            'message' => 'Analysis started',
            'status' => 'processing'
        ], 202);
    }
    
    public function index()
    {
        $recommendations = Recommendation::where('user_id', Auth::id())
            ->with(['plate', 'plate.category'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json($recommendations);
    }
    
    public function show($plate_id)
    {
        $recommendation = Recommendation::where('user_id', Auth::id())
            ->where('plate_id', $plate_id)
            ->with(['plate', 'plate.category'])
            ->first();
            
        if (!$recommendation) {
            return response()->json(['message' => 'Not found'], 404);
        }
        
        $response = [
            'plate_id' => (int) $recommendation->plate_id,
            'score' => $recommendation->score,
            'status' => $recommendation->status,
        ];
        
        if ($recommendation->score >= 80) {
            $response['label'] = 'Highly Recommended';
        } elseif ($recommendation->score >= 50) {
            $response['label'] = 'Recommended with notes';
        } else {
            $response['label'] = 'Not Recommended with warning message';
        }
        
        if ($recommendation->warning_message) {
            $response['warning_message'] = $recommendation->warning_message;
        }
        
        if ($recommendation->status === 'processing') {
            $response['message'] = 'Analysis in progress';
        }
        
        return response()->json($response);
    }
}
