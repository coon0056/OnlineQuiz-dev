<?php

//check results of matching question
function matching_question_results($questionID, $question, $userAnswers){
    $question_answers = get_post_meta( $questionID, '_question_answers_meta');
    $q_answers= isset( $question_answers[0] ) ? $question_answers[0] : [];

    $question_keys = get_post_meta( $questionID, '_question_keys_meta');
    $q_key = isset( $question_keys[0] ) ? $question_keys[0] : [];
   
    $correct = 0;

    ?> <div class="row"> <?php echo $question->post_content; ?> </div><?php
    
    for($i = 0; $i < count($q_answers); $i++){
        $key_print =$q_key[$i];
        
    ?>
        </br>
        <div class="row" >   
            <label for="user_choice_answers"><?php echo $key_print; ?>:</label>
                <select style='width:25%' name="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" id="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" class="postbox">
                    <option value=''><?php echo $userAnswers[$i]?></option>               
                </select>
                <?php
                if($userAnswers[$i] == $q_answers[$i] ){
                    $correct++;
                    ?> <div class="row">Correct!</div> <?php
                }else{
                    ?> <div class="row">Incorrect. Correct Answer: <?php echo $q_answers[$i]; ?> </div> <?php
                }

                ?>
        </div>
        </br>
    <?php 
    }
    ?> 
    <?php

}


?>
