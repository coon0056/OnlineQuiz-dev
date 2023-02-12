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
            echo 'Question '.($i+1).':';
                switch($questionType){
                    case 'matching_question':
                        Matching_Question::matching_question_results($questionID, $question, $userAnswers);
                        break;
                    case 'ordering_question':
                        Ordering_Question::ordering_question_results($questionID, $question, $userAnswers);
                        break;
                    case 'mc_single_question':
                        Mc_Single_Question::mc_single_question_results($questionID, $question, $userAnswers);
                        break;
                    case 'mc_multiple_question':
                        Mc_Multiple_Question::mc_multiple_question_results($questionID, $question, $userAnswers);
                        break;
                    case 'short_answer_question':
                            sa_Question::sa_question_results($questionID, $question, $userAnswers);
                            break;
                    default:
                        break;

                }
            }
    
        }
    get_footer();

?>