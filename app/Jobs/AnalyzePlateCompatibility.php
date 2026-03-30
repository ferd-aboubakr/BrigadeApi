<?php

namespace App\Jobs;

use App\Models\Recommendation;
use App\Models\Plate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AnalyzePlateCompatibility implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected $recommendation;

    public function __construct(Recommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    public function handle(): void
    {
        $user = User::find($this->recommendation->user_id);
        $plate = Plate::find($this->recommendation->plate_id);
        
        if (!$user || !$plate) {
            $this->recommendation->update([
                'status' => 'failed',
                'warning_message' => 'User or plate not found'
            ]);
            return;
        }
        
        $score = $this->calculateScore($user, $plate);
        
        $this->recommendation->update([
            'score' => $score,
            'status' => 'ready',
            'warning_message' => $score < 50 ? $this->getWarningMessage($user, $plate) : null
        ]);
    }
    
    private function calculateScore($user, $plate): int
    {
        $userTags = $user->dietary_tags ?? [];
        $conflicts = 0;
        
        foreach ($plate->ingredients as $ingredient) {
            $tags = $ingredient->tags ?? [];
            
            if (in_array('vegan', $userTags) && in_array('contains_meat', $tags)) $conflicts++;
            if (in_array('no_sugar', $userTags) && in_array('contains_sugar', $tags)) $conflicts++;
            if (in_array('no_cholesterol', $userTags) && in_array('contains_cholesterol', $tags)) $conflicts++;
            if (in_array('gluten_free', $userTags) && in_array('contains_gluten', $tags)) $conflicts++;
            if (in_array('no_lactose', $userTags) && in_array('contains_lactose', $tags)) $conflicts++;
        }
        
        return max(0, 100 - ($conflicts * 25));
    }
    
    private function getWarningMessage($user, $plate): string
    {
        $userTags = $user->dietary_tags ?? [];
        $conflicts = [];
        
        foreach ($plate->ingredients as $ingredient) {
            $tags = $ingredient->tags ?? [];
            
            if (in_array('vegan', $userTags) && in_array('contains_meat', $tags)) $conflicts[] = 'meat';
            if (in_array('no_sugar', $userTags) && in_array('contains_sugar', $tags)) $conflicts[] = 'sugar';
            if (in_array('no_cholesterol', $userTags) && in_array('contains_cholesterol', $tags)) $conflicts[] = 'cholesterol';
            if (in_array('gluten_free', $userTags) && in_array('contains_gluten', $tags)) $conflicts[] = 'gluten';
            if (in_array('no_lactose', $userTags) && in_array('contains_lactose', $tags)) $conflicts[] = 'lactose';
        }
        
        $unique = array_unique($conflicts);
        
        if (empty($unique)) {
            return 'This plate may not be suitable for your dietary restrictions.';
        }
        
        return 'Warning: Contains ' . implode(', ', $unique) . ' which conflicts with your diet.';
    }
}
