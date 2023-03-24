<?php
    //function used to calculate points 
    function calculatePoints(&$userScore, $pointWeight, $countCorrect, $correct){
        if($pointWeight == 0){
            return;
        }
        $pointsAwarded = ($correct/(float)$countCorrect) * $pointWeight;
        $pointsAwarded = round($pointsAwarded, 2);
        $userScore += $pointsAwarded;
        return $pointsAwarded;
        
    }
?>