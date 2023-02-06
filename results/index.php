<?php

    $path = preg_replace('/wp-content.*$/','',__DIR__);

    require_once($path."wp-load.php");

    get_header();

    if(isset($_POST['user_question_submit'])){
        $questionCount = $_POST['questionTotal'];
        
        for($i=0;$i<$questionCount;$i++){
            $questionID= $_POST['questionID'.($i+1)];
            $question = get_post($questionID);
            $questionType = $question->post_type; 
            $userAnswers= $_POST['user_choice_answers'.$questionID];

                switch($questionType){
                    case 'matching_question':
                        matching_question_results($questionID, $question, $userAnswers);
                        break;
                    default:
                        break;

                }
            }
    
        }
    get_footer();

?>