jQuery(document).ready(function(){

    //adding new question for quiz page
    jQuery('#add_new_question').click(function(e){
        e.preventDefault();
        num_answers = jQuery('#question_short_code li:last input:first').data('num');
        num_answers = num_answers+1;
        html_to_add=
        `
            <li>

            <div class="label"><label  for="questions[`+ num_answers +`]">Question `+ (num_answers+1) +` Short Code: </label></div>
            <div class="fields"><input data-num="`+ num_answers +`" style='width:50%' type='text' name="questions[`+ num_answers +`]"  value=""></div>
            </li> 
        `;
        jQuery('#question_short_code li:last').append(html_to_add);
        
        jQuery(".delete_button").on("click", function() {
            jQuery(this).closest('li').remove();
        });
    });

    //adding new key-value pair for matching question
    jQuery('#add_new_kv_pair').click(function(e){
        e.preventDefault();
        num_answers = jQuery('#key-value-pairs li:last input:first').data('num');
        num_answers = num_answers+1;
        html_to_add=
        `
            <li>

            <div class="label"><label  for="question_keys[`+ num_answers +`]">Key `+ (num_answers+1) +`: </label></div>
            <div class="fields"><input data-num="`+ num_answers +`" style='width:50%' type='text' name="question_keys[`+ num_answers +`]"  value=""></div>
            <div class="label"><label for="question_answers[`+ num_answers +`]">Value `+ (num_answers+1) +`:</label></div>
            <div class="fields"><input style='width:50%' type='text' name="question_answers[`+ num_answers +`]"  value=""></div>
            </li> 
        `;
        jQuery('#key-value-pairs li:last').append(html_to_add);
    });
    
    //adding new order labels for ordering question
    jQuery('#add_new_order_label').click(function(e){
        e.preventDefault();
        num_answers = jQuery('#order-labels li:last input:first').data('num');
        num_answers = num_answers+1;
        html_to_add=
        `
            <li>
            <div class="label"><label for="question_answers[`+ num_answers +`]">Order `+ (num_answers+1) +`:</label></div>
            <div class="fields"><input data-num="`+ num_answers +`" style='width:50%' type='text' name="question_answers[`+ num_answers +`]"  value=""></div>
            </li> 
        `;
        jQuery('#order-labels li:last').append(html_to_add);
    });

    //adding new order labels for mc-single answer question
    jQuery('#add_new_incorrect_choice').click(function(e){
        e.preventDefault();
        num_answers = jQuery('#multiple-choice-labels li:last input:first').data('num');
        num_answers = num_answers+1;
        html_to_add=
        `   
            <br/>
            <li>
            <div class="label"><label for="answer_wrong[`+ num_answers +`]"> </label></div>
            <div class="fields"><input data-num="`+ num_answers +`" style='width:50%' type="text"
                    name="answer_wrong[`+ num_answers +`]" value=""></div>
            </li>
        `;
        jQuery('#multiple-choice-labels li:last').append(html_to_add);
    });

    //adding new option for multiple select question
    jQuery('#add_new_ms_answer').click(function(e){
        e.preventDefault();
        num_answers = jQuery('#ms_answers li:last input:first').data('num');
        num_answers = num_answers+1;
        html_to_add=
        `
            <li id="ms_answer">
            <br>
                <input data-num="`+ num_answers +`" style='width:50%' type='text' 
                    name="answers[`+ num_answers +`]"  value="">
                <input type="checkbox" name="answers_right[`+ num_answers +`]">
                <label for="answers_right[`+ num_answers +`]">Correct Answer</label>
                <input type="button" value="Delete" id="delete_answer[`+ num_answers +`]" class="delete_button"> 
                <br>
                <br>
            </li>
        `;
        jQuery('#ms_answers li:last').append(html_to_add);
    
        jQuery(".delete_button").on("click", function() {
            jQuery(this).closest('li').remove();
        });
    });



//adding new option for short answer question 



jQuery('#add_new_correct').click(function(e){
    e.preventDefault();
    num_answers = jQuery('#correct_answers li:last input:first').data('num');
    num_answers = num_answers+1;
    console.log(num_answers);
    html_to_add=
    `    
        <br>
        <br>
        <li>
        <div class="label"><label  for="answer_right[`+ num_answers +`]">Answer  `+ (num_answers+1) +`: </label></div>
            <input data-num="`+ num_answers +`" style='width:50%' type='text' name="answer_right[`+ num_answers +`]"  value="">
            
           
        </li> 
    `;
    jQuery('#correct_answers li:last').append(html_to_add);

    
});


    
    
    jQuery(".delete_button").on("click", function() {
        jQuery(this).closest('li').remove();
    });
});