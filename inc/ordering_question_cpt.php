<?php
class Ordering_Question{

    //class constructor
    function __construct(){
        $this->create_post_type();
    }

    //Creates the post type and it's corresponding settings
    function create_post_type(){
        add_action('init', array($this,'register_post_type'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_filter('manage_ordering_question_posts_columns', array($this, 'custom_column_header'));
        add_filter('manage_ordering_question_posts_custom_column', array($this, 'custom_column_content'), 10,2);
        add_action('save_post', array($this, 'save_question_post'));
        add_shortcode('ordering_question', array($this, 'order_question_shortcode'));
    }

    //registers custom post type
    function register_post_type(){

        $question_labels = array(
            'name'               => 'Ordering Questions',
            'singular_name'      => 'Ordering Question',
            'menu_name'          => 'Ordering Questions',
            'name_admin_bar'     => 'Ordering Question',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Ordering Question',
            'new_item'           => 'New Ordering Question',
            'edit_item'          => 'Edit Ordering Question',
            'view_item'          => 'View Ordering Question',
            'all_items'          => 'All Ordering Questions',
            'search_items'       => 'Search Ordering Questions',
            'parent_item_colon'  => 'Parent Ordering Questions:',
            'not_found'          => 'No Ordering Questions found.',
            'not_found_in_trash' => 'No Ordering Questions found in Trash.'
        );

        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-editor-ul',
            'labels'    => $question_labels,
            'show_in_menu' => false,
            'supports'  => array('editor', 'author', 'thumbnail')
        );

        register_post_type('ordering_question', $args);
    }

    //creates the metaboxes 
    function register_meta_boxes(){
        add_meta_box('question_weight_meta','Ordering Question Weight',array($this, 'question_weight_html'),'ordering_question');
        add_meta_box('answer_meta', 'Ordering Question', array($this, 'ordering_question_html'), 'ordering_question');
    }


    //creates question weight metabox html
    function question_weight_html($post){
        wp_nonce_field('question_weight_field', 'orderingQuestion_nonce');
		$value = get_post_meta( $post->ID, '_question_weight_meta_key', true );
        if($value == ''){
            $value = 1;
        }
		?>
        <div class="row">
		<label for="question_weight_field"></label>
        <input style='width:25%' type='number' name='question_weight_field' min="1" value="<?php echo esc_attr($value); ?>" required>
        </div>
	    <?php
    }

    //creates ordering question metabox html
    function ordering_question_html($post){
        wp_nonce_field('answers', 'orderingQuestion_nonce');
        $question_answers = get_post_meta( $post->ID, '_question_answers_meta');

        if(count($question_answers) == 0){
            $question_answers[0] = '';
            $question_answers[1] = '';
            $count = 2;
        }else{
            $tempArr = isset( $question_answers[0] ) ? $question_answers[0] : [];
            $count = count($tempArr);
        }

        ?>

        <span> Add New Order Label</span>
        <a id = "add_new_order_label" href="#" title="Add new order label">
            <span class="dashicons dashicons-insert"></span></br>
        </a>
        
        </br>
        <div class="row">
            <ul id="order-labels">
            <?php

            //checks if array is set
            $q_value = isset( $question_answers[0] ) ? $question_answers[0] : [];
           
            //checks for empty spots in the array and re-arranges
            if(is_array($q_value) ){
                $q_value = array_values($q_value);
            }

            for($i = 0; $i < $count; $i++){
                $value_print = isset( $q_value[$i] ) ? $q_value[$i] : '';
            ?>

            <li>    
            <div class="label"><label for="question_answers[<?php echo $i; ?>]">Order <?php echo  $i + 1; ?>:</label></div>
            <div class="fields">
                <input data-num="<?php echo $i;?>" style='width:50%' type='text' name="question_answers[<?php echo $i; ?>]"  value="<?php echo esc_attr($value_print); ?>">
                <input type="button" value="Delete" name="delete_answer[<?php echo $i; ?>]" class="delete_button">
            </div>
            </li>
            <?php } 
            ?>
            </ul>
        </div>  
        <?php   
    }

    //save post meta values
    function save_question_post( $post_id ) {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
            return $post_id;
        }
        
        if (!isset($_POST['orderingQuestion_nonce'])){
            return $post_id;
        }

        $nonce = $_POST['orderingQuestion_nonce'];
        if (!wp_verify_nonce($nonce, 'answers') && (!wp_verify_nonce($nonce, 'question_weight_field'))){
            return $post_id;
        }

		if ( array_key_exists( 'question_weight_field', $_POST ) ) {
            sanitize_text_field($_POST['question_weight_field']);
			update_post_meta($post_id,'_question_weight_meta_key',$_POST['question_weight_field']);
		}

        if ( array_key_exists( 'question_answers', $_POST ) ) {
            sanitize_text_field($_POST['question_answers']);
			update_post_meta($post_id,'_question_answers_meta',$_POST['question_answers']);
		}

	}

    //settings for the column headers
    function custom_column_header($old_column_header){
        unset($old_column_header['title']);
        unset($old_column_header['author']);

        $new_column_header['author'] = 'Author';
        $new_column_header['question'] = 'Ordering Question';
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
                echo '[ordering_question id= '.$post_id.']';
                break;
            default:
                break;
        }

    }

    //generates ordering question short code
    function order_question_shortcode($atts){
        ?> <?php
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        $question = get_post($atts['id']);
        $question_answers = get_post_meta( $atts['id'], '_question_answers_meta');
    
        $q_values= isset( $question_answers[0] ) ? $question_answers[0] : [];
        shuffle($q_values);
        
        $count = count($q_values);

        ob_start();
        ?> <div class="LOOKHERE"> <?php
        echo '<div class="row" >'. $question->post_content.'</div>';
        
        //checks for empty spots in the array and re-arranges
        if(is_array($q_values) ){
            $q_values = array_values($q_values);
        }

        for($i = 0; $i < $count; $i++){
            $key_print =$q_values[$i];
        ?>
            </br>
            <div class="row">
                <select style="width:50%" name="user_choice_answers<?php echo $atts['id']; ?>[<?php echo $i ?>]" id="user_choice_answers<?php echo $atts['id']; ?>[<?php echo $i ?>]" class="postbox" required>
                     <option value=''> Put the following in order </option>
                        <?php
                        foreach($q_values as $item){
                            echo "<option value='$item'>$item</option>";
                        }
                        ?>
                </select>
            </div> 
        <?php 
        }
        
        return ob_get_clean();
    }

    //check results of ordering question
    public static function ordering_question_results($questionID, $question, $userAnswers, &$userScore){
        ?><div class="row-order-qtype" ><?php
            $question_answers = get_post_meta( $questionID, '_question_answers_meta');
            $q_answers= isset( $question_answers[0] ) ? $question_answers[0] : [];
            
            $countCorrect = count($q_answers);
            $pointWeight = get_post_meta( $questionID, '_question_weight_meta_key',true);
            $correct = 0.0;   
        
            ?> <div class="row-title"> <?php echo $question->post_content; ?> </div><?php
            
            //checks for empty spots in the array and re-arranges
            if(is_array($q_answers) ){
                $q_answers = array_values($q_answers);
            }

            for($i = 0; $i < count($q_answers); $i++){
                $key_print =$q_answers[$i];
                
            ?>
                <div class="row" >   
                    <label for="user_choice_answers"></label>
                        <div class ="column col-dropdown">
                            <select name="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" id="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" class="postbox" disabled>
                                <option value=''><?php echo $userAnswers[$i]?></option>               
                            </select>
                        </div>
                        <?php
                            if($userAnswers[$i] == $q_answers[$i] ){
                                $correct++;
                                ?> <div class="column"><span class="correct-ans">Correct!</span> </div>  <?php
                            }else{
                                ?> <div class="column"><span class="incorrect-ans">Incorrect. Correct Order: <?php echo $q_answers[$i]; ?> </span> </div> <?php
                            }
                        ?>
                </div>
            <?php 
            }
        ?> <div class="row-title" > <?php calculatePoints($userScore, $pointWeight, $countCorrect, $correct); ?> </div>
        <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots"> 
    </div><?php  
    }

}
