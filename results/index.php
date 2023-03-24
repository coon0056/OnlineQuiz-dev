<?php

    $path = preg_replace('/wp-content.*$/','',__DIR__);

    require_once($path."wp-load.php");

    get_header();
    ?> <div style="width:800px; margin:0 auto;"> <?php
    if(isset($_POST['user_question_submit'])){
        $questionCount = $_POST['questionTotal'];
        $authorEmail = $_POST['authorEmail'];
        $testTaker = $_POST['testTaker'];

        //setup points 
        $totalScore = 0.0;
        $userScore = 0.0;
        for($i=0;$i<$questionCount;$i++){
            $questionID= $_POST['questionID'.($i+1)];
            $question = get_post($questionID);
            $points = get_post_meta( $questionID, '_question_weight_meta_key',true);
            $totalScore += (float) $points;
        }
        
        $body = "Quiz Results: </br> "; //begins email body formatting
        for($i=0;$i<$questionCount;$i++){
            $questionID= $_POST['questionID'.($i+1)];
            $question = get_post($questionID);
            $questionType = $question->post_type;
            $body = $body."Question ".($i+1).") "; 
            $userAnswers= $_POST['user_choice_answers'.$questionID];
            ?> <div class="row-question"><?php echo 'Question '.($i+1).':'; ?> </div> <?php
                switch($questionType){
                    case 'matching_question':
                        Matching_Question::matching_question_results($questionID, $question, $userAnswers, $userScore, $body);
                        break;
                    case 'ordering_question':
                        Ordering_Question::ordering_question_results($questionID, $question, $userAnswers, $userScore, $body);
                        break;
                    case 'mc_single_question':
                        Mc_Single_Question::mc_single_question_results($questionID, $question, $userAnswers, $userScore, $body);
                        break;
                    case 'mc_multiple_question':
                        Mc_Multiple_Question::mc_multiple_question_results($questionID, $question, $userAnswers, $userScore, $body);
                        break;
                    case 'sa_question':
                        sa_Question::sa_question_results($questionID, $question, $userAnswers, $userScore, $body);
                        break;
                    default:
                        break;

                }
            }
            ?> <div class="row-title" > <?php echo "<br> Attempt Score : $userScore / $totalScore"; ?> </div>
            <?php
        }
    ?> </div> <?php

    //emails results to author of the quiz
    $to = $authorEmail; 
    $subject = "Quiz Results for $testTaker";
    $body =$body."</br></br>  Attempt Score : $userScore / $totalScore";
    $headers = array('Content-Type: text/html; charset=UTF-8');

    wp_mail($to, $subject, $body, $headers);
    //get_footer();

?>