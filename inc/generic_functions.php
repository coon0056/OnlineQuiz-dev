<?php
    //function used to calculate points 
    function calculatePoints(&$userScore, $pointWeight, $countCorrect, $correct){
        if($pointWeight == 0){
            return;
        }
        $pointsAwarded = ($correct/(float)$countCorrect) * $pointWeight;
        $userScore += $pointsAwarded;
        echo "<br> Points Awarded:  $pointsAwarded  / $pointWeight <br>";
    }
?>
