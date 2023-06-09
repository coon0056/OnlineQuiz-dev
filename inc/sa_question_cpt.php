<?php
class sa_Question{
    //class constructor
    function __construct(){
        $this->create_post_type();
    }

    //Creates the post type and it's corresponding settings
    function create_post_type(){
        add_action('init', array($this,'register_post_type'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_filter('manage_sa_question_posts_columns', array($this, 'custom_column_header'));
        add_filter('manage_sa_question_posts_custom_column', array($this, 'custom_column_content'), 10,2);
        add_action('save_post', array($this, 'save_question_post'));
        add_shortcode('sa_question', array($this, 'sa_question_shortcode'));
    }

    //registers custom post type
    function register_post_type(){

        //sets custom post type labels
        $question_labels = array(
            'name'               => 'Short Answers',
            'singular_name'      => 'Short Answer',
            'menu_name'          => 'Short Answers',
            'name_admin_bar'     => 'Short Answer',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Short Answer',
            'new_item'           => 'New Short Answer',
            'edit_item'          => 'Edit Short Answer',
            'view_item'          => 'View Short Answer',
            'all_items'          => 'All Short Answers',
            'search_items'       => 'Search Short Answers',
            'parent_item_colon'  => 'Parent Short Answers:',
            'not_found'          => 'No Short Answers found.',
            'not_found_in_trash' => 'No Short Answers found in Trash.'
        );

        //sets custom post type settings
        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-editor-ul',
            'labels'    => $question_labels,
            'show_in_menu' => false,
            'supports'  => array('editor', 'author', 'thumbnail'),
            'capability_type' => array('quiz', 'quizzes'),
            'map_meta_cap'  => true
        );

        register_post_type('sa_question', $args);
    }

    //creates the metaboxes 
    function register_meta_boxes(){
        add_meta_box('question_weight_meta','Short Answer Weight',array($this, 'question_weight_html'),'sa_question');
        add_meta_box('sa_meta', 'Short Question', array($this, 'sa_question_html'), 'sa_question');
    }

    //creates question weight metabox html
    function question_weight_html($post){
		$value = get_post_meta( $post->ID, '_question_weight_meta_key', true );
        if($value == ''){
            $value = 1;
        }
		?>
        <div class="row">
		<label for="question_weight_field"></label>
        <input style='width:25%' type='number' name='question_weight_field' min="1" value="<?php echo $value; ?>" required>
        </div>
	    <?php
    }   
    
    //creates short answer question metabox html
    function sa_question_html($post){
        wp_nonce_field('answers', 'SAQuestion_nonce');
        $question_right_answers= get_post_meta( $post->ID, '_question_right_answers_meta');
       

        if(count($question_right_answers) == 0){ //if there are cuurently no right answers - set an array of 1 with blanks
            $question_right_answers[0] = '';
            $count1 = 1;
        }else{ // yes there is an array of right answers
            $tempArr1 = isset( $question_right_answers[0] ) ? $question_right_answers[0] : []; // set it
            $count1 = count($tempArr1); // get the count
        }


        ?>

        <span> Add New Correct Answer</span>
        <a id = "add_new_correct" href="#" title="Add new correct">
            <span class="dashicons dashicons-insert"></span></br>
        </a>
        </br>
        <div class="row">
            <ul id="correct_answers">                
            <?php

            $q_right = isset( $question_right_answers[0] ) ? $question_right_answers[0] : [];

            if (is_array($q_right)) {
                $q_right = array_values($q_right);
            }

            //iterate through post meta values and prints html
            for($i = 0; $i < $count1; $i++){
               
                $key_right =  isset( $q_right[$i] ) ? $q_right[$i] : '';
            ?>
            <li>    
            <div class="label"><label  for="answer_right[<?php echo $i; ?>]">Answer <?php echo $i + 1; ?>: </label></div>
            <div class="fields"><input data-num="<?php echo $i;?>" style='width:50%' type='text' name="answer_right[<?php echo $i; ?>]"  value="<?php echo esc_attr($key_right); ?>" required>
            <input type="button" value="Delete" name="delete_answer[<?php echo $i; ?>]" class="delete_button"></div>
            </li>
            <?php } 
            ?>
            </ul>
        </div>
        </br>          
        
        <?php
    }

    function save_question_post( $post_id ){
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
            return $post_id;
        }

        if (!isset($_POST['SAQuestion_nonce'])){
            return $post_id;
        }

        $nonce = $_POST['SAQuestion_nonce'];
        if (!wp_verify_nonce($nonce, 'answers') && (!wp_verify_nonce($nonce, 'question_weight_field'))){
            return $post_id;
        }

        if ( array_key_exists( 'question_weight_field', $_POST ) ) {
            sanitize_text_field($_POST['question_weight_field']);
			update_post_meta($post_id,'_question_weight_meta_key',$_POST['question_weight_field']);
		}

        if ( array_key_exists( 'answer_right', $_POST ) ) {
            sanitize_text_field($_POST['question_answers']);
			update_post_meta($post_id,'_question_right_answers_meta',$_POST['answer_right']);
		}
    }

    function sa_question_shortcode($atts){

        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);
        $question = get_post($atts['id']);
        $question_right_answers = get_post_meta( $atts['id'], '_question_right_answers_meta');      

        $q_right = isset( $question_right_answers[0] ) ? $question_right_answers[0] : [];        
        $all = $q_right;
              
        $count = count($all);

        ob_start();
         echo '<div class="row" >'. $question->post_content.'</div>';        
         echo '<input type="hidden" id="questionID" name="questionID" value="'.$atts['id'].'">';       
       
        ?>            
            <input type="textarea" name="user_choice_answers<?php echo $atts['id'] ?>" id="user_choice_answers<?php echo $atts['id'] ?>" required>
    </br>    
        <?php 

        return ob_get_clean();
    }

    //settings for the column headers
    function custom_column_header($old_column_header){
        unset($old_column_header['title']);
        unset($old_column_header['author']);

        $new_column_header['author'] = 'Author';
        $new_column_header['question'] = 'Short Answer Question';
        $new_column_header['points'] = 'Points';
        $new_column_header['shortcode'] = 'Short Code';

        return $new_column_header;

    }
    
    //content shown for the summary question table
    function custom_column_content($column_name, $post_id){
        $question = esc_html(get_the_content($post_id));
        $weight = get_post_meta( $post_id, '_question_weight_meta_key', true );
        

        switch($column_name) {
            case 'question':
                echo '<strong>'.$question.'</strong>';
                break;
            case 'points':
                echo $weight;
                break;
            case 'shortcode':
                echo '[sa_question id= '.$post_id.']';
                break;
            default:
                break;
        }

    }

    //TODO Mohit
    public static function sa_question_results($questionID, $question, $userAnswers, &$userScore, &$body, $answered){
        ?><div class="row-match-qtype" ><?php
            $question_answer = get_post_meta( $questionID, '_question_right_answers_meta',false);
            
        //assigning the answers in question answer array to q choices
            $q_choices= isset($question_answer[0]) ? $question_answer[0] : [];        
          
            $pointWeight = get_post_meta( $questionID, '_question_weight_meta_key',true);           
            $correct = 0.0;
        
            ?> <div class="row-title"> <?php echo $question->post_content; ?> </div>
            <div class="row">
                <div class="column col-sa"> 
                    <input type="textarea" name="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" id="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" value="<?php echo $userAnswers; ?>" disabled></input>    
                </div>
            <?php for ($i = 0; $i < count($q_choices); $i++) {
                $key_print = $q_choices[$i]; 
            ?>
                       
                    <?php if(strcasecmp(trim($userAnswers), trim($key_print)) == 0){ 
                            $correct++;
                            ?> 
                            <div class="column col-sa-actual-ans">
                            <div class="column"><span class="correct-ans">Correct!</span></div><br> 
                            </div>
                            <?php
                        }
                    ?>
                
            <?php       
            } 
            if ($correct == 0){
                ?> <div class="column"><span class="incorrect-ans">Incorrect. Correct Answers: </span></div> <?php
                for ($i = 0; $i < count($q_choices); $i++) {
					$key_print = $q_choices[$i];
					if((strcasecmp(trim($userAnswers), trim($key_print)) !== 0) && ($i !== (count($q_choices)-1) )){                 
                        ?> 
                        <div class="column"><span class="incorrect-ans-sa-choices"><?php echo $q_choices[$i]; ?>, </span></div>    
                        <?php                        
					} else if (strcasecmp(trim($userAnswers), trim($key_print)) !== 0) {                               
                        ?> 
                        <div class="column"><span class="incorrect-ans-sa-choices"><?php echo $q_choices[$i]; ?></span></div>    
                        <?php            
                    }            
					   
				}
            }
             
        ?>  
        </div>
        <?php
            //check if question was answered
            if($answered){
                $pointsAwarded = calculatePoints($userScore, $pointWeight, 1, $correct);
            }else{
                $pointsAwarded = calculatePoints($userScore, $pointWeight, 1, 0);
                $userAnswers = '';
                echo "</br>Time Limit Reached: Question unanswered.";
                $body = $body."</br>Time Limit Reached - Question unanswered </br> ";
            }
            ?> <div class="row-title" > <?php echo "<br> Points Awarded:  $pointsAwarded  / $pointWeight <br>"; ?> </div>
        <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots"> 
    </div><?php   
    
        //sets email formatting
        $body = $body."</br>Correct Answer: "; 
        for ($i = 0; $i < count($q_choices); $i++) {
            $body = $body."</br>".$q_choices[$i];
        }
        $body = $body."</br></br> User Answer(s): </br> $userAnswers";
        $body = $body."</br></br> Points Awarded: $pointsAwarded / $pointWeight </br></br>";
    }



}

