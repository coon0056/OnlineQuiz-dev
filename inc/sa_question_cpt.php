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

        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-editor-ul',
            'labels'    => $question_labels,
            'show_in_menu' => false,
            'supports'  => array('editor', 'author', 'thumbnail')
        );

        register_post_type('sa_question', $args);
    }

    //creates the metaboxes 
    function register_meta_boxes(){
        add_meta_box('sa_weight_meta','Short Answer Weight',array($this, 'question_weight_html'),'sa_question');
        add_meta_box('sa_meta', 'Short Question', array($this, 'sa_question_html'), 'sa_question');
    }

    //creates question weight metabox html
    function question_weight_html($post){
        $value = get_post_meta( $post->ID, '_sa_weight_meta_key', true );
		?>
        <div class='row'>
		<label for='sa_weight_field'></label>
        <input style='width:25%' type='number' name='sa_weight_field' min='1' value="<?php echo $value; ?>">
        </div>
	    <?php
    }

    

    
    //creates short answer question metabox html
    function sa_question_html($post){
        $question_right_answers= get_post_meta( $post->ID, '_question_right_answers_meta');
       

        if(count($question_right_answers) == 0){ //if there are cuurently no right answers - set an array of 2 with blanks
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
            for($i = 0; $i < $count1; $i++){
                $q_right = isset( $question_right_answers[0] ) ? $question_right_answers[0] : [];
                $key_right =  isset( $q_right[$i] ) ? $q_right[$i] : '';
            ?>
            <li>    
            <div class="label"><label  for="answer_right[<?php echo $i; ?>]">Answer <?php echo $i + 1; ?>: </label></div>
            <div class="fields"><input data-num="<?php echo $i;?>" style='width:50%' type='text' name="answer_right[<?php echo $i; ?>]"  value="<?php echo $key_right; ?>">
            <input type="button" value="Delete" name="delete_answer[<?php echo $i; ?>]" class="delete_button"></div>
            </li>
            <?php } 
            ?>
            </ul>
        </div>
        </br>          
        </br> 
        <?php
    }

    function save_question_post( $post_id ){
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
            return $post_id;
        }
        if ( array_key_exists( 'sa_weight_field', $_POST ) ) {
			update_post_meta($post_id,'_sa_weight_meta_key',$_POST['sa_weight_field']);
		}
        if ( array_key_exists( 'answer_right', $_POST ) ) {
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
            <input type="textAreaField" name="user_choice_answers<?php echo $atts['id'] ?>" id="user_choice_answers<?php echo $atts['id'] ?>">
            <br>    
        <?php 
      
        ?>            
             
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
        public static function sa_question_results($questionID, $question, $userAnswers, &$userScore){
            ?><div class="row-mc-single-qtype" ><?php
            $question_answer = get_post_meta( $questionID, '_question_right_answer_meta',false);
            
           //assigning the answers in question answer array to q choices
            $q_choices= isset($question_answer[0]) ? $question_answer[0] : [];
        
            //FOR MOHIT TO FIX AFTER
            //$pointWeight = get_post_meta( $questionID, '_sa_weight_field',true);
            $pointWeight = 10; 
            $correct = 0.0;
                  
            ?> <div class="row-title"> <?php echo $question->post_content; ?> </div><?php

                ?>
                </br>
                <div class="row">
                    <div class ="column col-mc-single">
                    <input type="textAreaField" name="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" id="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" value="<?php echo $userAnswers; ?>" disabled>
                    <br>                    
                    </div>  

            <?php        
            for ($i = 0; $i < count($q_choices); $i++) {
                $key_print = $q_choices[$i];

                if(strcasecmp(trim($userAnswers), $key_print) == 0){ 
                    $correct++;
                    ?> <div class="column"><span class="correct-ans">Correct!</span></div><br> <?php
                }
                ?></div><?php        
            }  ?>

            <?php        
            for ($i = 0; $i < count($q_choices); $i++) {
                $key_print = $q_choices[$i];
                if(strcasecmp(trim($userAnswers), $key_print) !== 0){                         
                        ?> <div class="column"><span class="incorrect-ans">In Correct!</span></div><br> 
                        <br>                       
                        
                        <?php                        
                    } 
                    ?></div><?php        
                }  ?>

                <?php        
            for ($i = 0; $i < count($q_choices); $i++) {
                $key_print = $q_choices[$i];

                if(strcasecmp(trim($userAnswers), $key_print) !== 0){                         
                        ?>  
                                                
                        <label for="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]"> <?php echo $key_print; ?>
                        <div><span class="actual-correct-ans">This is a correct answer!</span></div></label>
                        <?php                        
                    } 
                ?></div><?php        
            }  ?>

            <?php 
            ?> <div class="row-title" > <?php calculatePoints($userScore, $pointWeight, 1, $correct); ?> </div>
            <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots"> 
        </div><?php    
        }

}

