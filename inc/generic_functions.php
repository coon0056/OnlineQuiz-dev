<?php
    //function used to calculate points 
    function calculatePoints(&$userScore, $pointWeight, $countCorrect, $correct){
        $pointsAwarded = ($correct/(float)$countCorrect) * $pointWeight;
        $userScore += $pointsAwarded;
        echo "<br> Points Awarded:  $pointsAwarded  / $pointWeight <br>";
    }
?>
