<?php
class Mc_Single_Question{

    //class constructor
    function __construct(){
        $this->create_post_type();
    }

    //Creates the post type and it's corresponding settings
    function create_post_type(){
        add_action('init', array($this,'register_post_type'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_filter('manage_mc_single_question_posts_columns', array($this, 'custom_column_header'));
        add_filter('manage_mc_single_question_posts_custom_column', array($this, 'custom_column_content'), 10,2);
        add_action('save_post', array($this, 'save_question_post'));
        add_shortcode('mc_single_question', array($this, 'mc_single_question_shortcode'));
    }

    //registers custom post type
    function register_post_type(){

        $question_labels = array(
            'name'               => 'Multiple Choice - Single Answer Questions',
            'singular_name'      => 'Multiple Choice - Single Answer Question',
            'menu_name'          => 'Multiple Choice - Single Answer Questions',
            'name_admin_bar'     => 'Multiple Choice - Single Answer Question',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Multiple Choice - Single Answer Question',
            'new_item'           => 'New Multiple Choice - Single Answer Question',
            'edit_item'          => 'Edit Multiple Choice - Single Answer Question',
            'view_item'          => 'View Multiple Choice - Single Answer Question',
            'all_items'          => 'All Multiple Choice - Single Answer Questions',
            'search_items'       => 'Search Multiple Choice - Single Answer Questions',
            'parent_item_colon'  => 'Parent Multiple Choice - Single Answer Questions:',
            'not_found'          => 'No Multiple Choice - Single Answer Questions found.',
            'not_found_in_trash' => 'No Multiple Choice - Single Answer Questions found in Trash.'
        );

        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-editor-ul',
            'labels'    => $question_labels,
            'show_in_menu' => false,
            'supports'  => array('editor', 'author', 'thumbnail')
        );

        register_post_type('mc_single_question', $args);
    }

    //creates the metaboxes 
    function register_meta_boxes(){
        add_meta_box('question_weight_meta','Multiple Choice - Single Answer Question Weight',array($this, 'question_weight_html'),'mc_single_question');
        add_meta_box('answer_meta', 'Multiple Choice - Single Answer Question', array($this, 'mc_single_question_html'), 'mc_single_question');
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
        <input style='width:25%' type='number' name='question_weight_field' min="1" value="<?php echo $value; ?>">
        </div>
	    <?php
    }

    //creates mc-single question metabox html
    function mc_single_question_html($post){
        $question_correct_answer = get_post_meta($post->ID, '_question_right_answer_meta', true);
        $question_incorrect_answers = get_post_meta($post->ID, '_question_wrong_answers_meta');

        if (count($question_incorrect_answers) == 0) {
            $question_incorrect_answers[0] = '';
            $count = 1;
        } else {
            $tempArr = isset($question_incorrect_answers[0]) ? $question_incorrect_answers[0] : [];
            $count = count($tempArr);
        }
        ?>

        <span> Add New Incorrect Choice</span>
        <a id="add_new_incorrect_choice" href="#" title="Add new incorrect choice">
            <span class="dashicons dashicons-insert"></span></br>
        </a>
        </br>
        <div class="row">
            <ul id="multiple-choice-labels">
                <li>
                    <div class="label"><label for="answer_right"> Correct Answer: </label></div>
                    <div class="fields"><input style='width:50%' , type="text" , name="answer_right"
                            value="<?php echo $question_correct_answer; ?>"></div>
                </li>
                <?php

                //checks if array is set
                $q_wrong = isset($question_incorrect_answers[0]) ? $question_incorrect_answers[0] : [];

                //checks for empty spots in the array and re-arranges
                if (is_array($q_wrong)) {
                    $q_wrong = array_values($q_wrong);
                }
                
                for ($i = 0; $i < $count; $i++) {
                    $key_wrong = isset($q_wrong[$i]) ? $q_wrong[$i] : '';
                    ?>
                    <li>
                        <div class="label"><label for="answer_wrong[<?php echo $i; ?>]"> Incorrect Answer(s): </label></div>
                        <div class="fields">
                            <input data-num="<?php echo $i; ?>" style='width:50%' type="text"
                                name="answer_wrong[<?php echo $i; ?>]" value="<?php echo $key_wrong; ?>">
                            <input type="button" value="Delete" name="delete_answer[<?php echo $i; ?>]" class="delete_button">
                        </div>
                    </li>
                    <?php
                }
    }

    //save post meta values
    function save_question_post( $post_id ) {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
            return $post_id;
        }

		if ( array_key_exists( 'question_weight_field', $_POST ) ) {
			update_post_meta($post_id,'_question_weight_meta_key',$_POST['question_weight_field']);
		}

        if ( array_key_exists( 'answer_right', $_POST ) ) {
			update_post_meta($post_id,'_question_right_answer_meta',$_POST['answer_right']);
		}

        if ( array_key_exists( 'answer_wrong', $_POST ) ) {
			update_post_meta($post_id,'_question_wrong_answers_meta',$_POST['answer_wrong']);
		}

	}

    //settings for the column headers
    function custom_column_header($old_column_header){
        unset($old_column_header['title']);
        unset($old_column_header['author']);

        $new_column_header['author'] = 'Author';
        $new_column_header['question'] = 'Multiple Choice - Single Answer Question';
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
                echo '[mc_single_question id= '.$post_id.']';
                break;
            default:
                break;
        }

    }

    //generates mc-single question short code
    function mc_single_question_shortcode($atts){
        $atts = shortcode_atts(
            array(
                'id' => '',

            ), $atts);

        $question = get_post($atts['id']);
        $question_correct_answer = get_post_meta($atts['id'], '_question_right_answer_meta', true);
        $question_incorrect_answers = get_post_meta($atts['id'], '_question_wrong_answers_meta');

        $q_choices= isset($question_incorrect_answers[0]) ? $question_incorrect_answers[0] : [];
        $q_choices[]=($question_correct_answer);

        shuffle($q_choices); 
        $count = count($q_choices);

        ob_start();
        echo '<div class="row" >' . $question->post_content . '</div>';
        echo '<input type="hidden" id ="questionID" name="questionID" value"' . $atts['id'] . '">';

        //checks for empty spots in the array and re-arranges
        if (is_array($q_choices)) {
            $q_choices = array_values($q_choices);
        }

        for ($i = 0; $i < $count; $i++) {
            $key_print = $q_choices[$i];

            ?>
            </br>
            <div class="row">
                <input type="radio" name="user_choice_answers<?php echo $atts['id'] ?>" id="user_choice_answers<?php echo $atts['id'] ?>" value="<?php echo $key_print; ?>">
                <label for="user_choice_answers<?php echo $atts['id'] ?>"> <?php echo $key_print; ?></label>
            </div>

            <?php
        }
        ?>
        <?php
        ?> <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots"> <?php
        return ob_get_clean();
    }

    //check results of mc-single question
    public static function mc_single_question_results($questionID, $question, $userAnswers, &$userScore){
        ?><div class="row-mc-single-qtype" ><?php
            $question_answer = get_post_meta( $questionID, '_question_right_answer_meta',true);
            $question_incorrect_answers = get_post_meta($questionID, '_question_wrong_answers_meta');

            $q_choices= isset($question_incorrect_answers[0]) ? $question_incorrect_answers[0] : [];
            $q_choices[]=($question_answer);

            $pointWeight = get_post_meta( $questionID, '_question_weight_meta_key',true);
            $correct = 0.0;
        
            ?> <div class="row-title"> <?php echo $question->post_content; ?> </div><?php

            //checks for empty spots in the array and re-arranges
            if (is_array($q_choices)) {
                $q_choices = array_values($q_choices);
            }

            for ($i = 0; $i < count($q_choices); $i++) {
                $key_print = $q_choices[$i];

                ?>
                </br>
                <div class="row">
                    <div class ="column col-mc-single">
                        <input type="radio" <?php if($userAnswers == $key_print) echo "checked"; ?>  name="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" id="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" value="<?php echo $key_print; ?>" disabled>
                        <label for="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]"> <?php echo $key_print; ?></label>
                    </div>    
                    <?php
                    if($userAnswers == $question_answer && $question_answer == $key_print ){
                        $correct++;
                        ?> <div class="column"><span class="correct-ans">Correct!</span></div> <?php
                    }else if(($userAnswers == $key_print) || (!$question_answer == $key_print)){
                        ?> <div class="column"><span class="incorrect-ans">Incorrect.</span></div> <?php
                    }else if((!$userAnswers == $key_print) || ($question_answer == $key_print)){
                        ?> <div class="column"><span class="actual-correct-ans">This is the correct answer!</span></div> <?php
                    }
                ?></div><?php        
                }  ?>
            <?php 
            ?> <div class="row-title" > <?php calculatePoints($userScore, $pointWeight, 1, $correct); ?> </div>
            <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots"> 
        </div><?php    
    }
}
