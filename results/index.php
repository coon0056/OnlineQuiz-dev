<?php

    $path = preg_replace('/wp-content.*$/','',__DIR__);

    require_once($path."wp-load.php");

    get_header();

    if(isset($_POST['user_question_submit'])){
        $questionCount = $_POST['questionTotal'];

        //setup points 
        $totalScore = 0.0;
        $userScore = 0.0;
        for($i=0;$i<$questionCount;$i++){
            $questionID= $_POST['questionID'.($i+1)];
            $question = get_post($questionID);
            $points = get_post_meta( $questionID, '_question_weight_meta_key',true);
            $totalScore += (float) $points;
        }
        
        for($i=0;$i<$questionCount;$i++){
            $questionID= $_POST['questionID'.($i+1)];
            $question = get_post($questionID);
            $questionType = $question->post_type; 
            $userAnswers= $_POST['user_choice_answers'.$questionID];
            echo '<br>Question '.($i+1).':';
                switch($questionType){
                    case 'matching_question':
                        Matching_Question::matching_question_results($questionID, $question, $userAnswers, $userScore);
                        break;
                    case 'ordering_question':
                        Ordering_Question::ordering_question_results($questionID, $question, $userAnswers, $userScore);
                        break;
                    case 'mc_single_question':
                        Mc_Single_Question::mc_single_question_results($questionID, $question, $userAnswers, $userScore);
                        break;
                    case 'mc_multiple_question':
                        Mc_Multiple_Question::mc_multiple_question_results($questionID, $question, $userAnswers, $userScore);
                        break;
                    default:
                        break;

                }
            }
            echo "<br> Attempt Score : $userScore / $totalScore"; 
    
        }
    get_footer();

?>