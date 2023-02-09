<?php
class Mc_Multiple_Question{

    //class constructor
    function __construct(){
        $this->create_post_type();
    }

    //Creates the post type and it's corresponding settings
    function create_post_type(){
        add_action('init', array($this,'register_post_type'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_filter('manage_mc_multiple_question_posts_columns', array($this, 'custom_column_header'));
        add_filter('manage_mc_multiple_question_posts_custom_column', array($this, 'custom_column_content'), 10,2);
        add_action('save_post', array($this, 'save_question_post'));
        add_shortcode('mc_multiple_question', array($this, 'mc_multiple_question_shortcode'));
    }

    //registers custom post type
    function register_post_type(){

        $question_labels = array(
            'name'               => 'Multiple Choice - Multiple Answer Questions',
            'singular_name'      => 'Multiple Choice - Multiple Answer Question',
            'menu_name'          => 'Multiple Choice - Multiple Answer Questions',
            'name_admin_bar'     => 'Multiple Choice - Multiple Answer Question',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Multiple Choice - Multiple Answer Question',
            'new_item'           => 'New Multiple Choice - Multiple Answer Question',
            'edit_item'          => 'Edit Multiple Choice - Multiple Answer Question',
            'view_item'          => 'View Multiple Choice - Multiple Answer Question',
            'all_items'          => 'All Multiple Choice - Multiple Answer Questions',
            'search_items'       => 'Search Multiple Choice - Multiple Answer Questions',
            'parent_item_colon'  => 'Parent Multiple Choice - Multiple Answer Questions:',
            'not_found'          => 'No Multiple Choice - Multiple Answer Questions found.',
            'not_found_in_trash' => 'No Multiple Choice - Multiple Answer Questions found in Trash.'
        );

        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-editor-ul',
            'labels'    => $question_labels,
            'show_in_menu' => false,
            'supports'  => array('editor', 'author', 'thumbnail')
        );

        register_post_type('mc_multiple_question', $args);
    }

    //creates the metaboxes 
    function register_meta_boxes(){
        add_meta_box('question_weight_meta','Multiple Choice - multiple Answer Question Weight',array($this, 'question_weight_html'),'mc_multiple_question');
        add_meta_box('answer_meta', 'Multiple Choice - multiple Answer Question', array($this, 'mc_multiple_question_html'), 'mc_multiple_question');
    }


    //creates question weight metabox html
    function question_weight_html($post){
		$value = get_post_meta( $post->ID, '_question_weight_meta_key', true );
		?>
        <div class="row">
		<label for="question_weight_field"></label>
        <input style='width:25%' type='number' name='question_weight_field' min="0" value="<?php echo $value; ?>">
        </div>
	    <?php
    }

    //creates matching question metabox html
    function mc_multiple_question_html($post){
        $question_right_answers = get_post_meta( $post->ID, '_question_right_answers_meta');
        $question_wrong_answers = get_post_meta( $post->ID, '_question_wrong_answers_meta');

        if(count($question_right_answers) == 0){ //if there are cuurently no right answers - set an array of 2 with blanks
            $question_right_answers[0] = '';
            $count1 = 1;
        }else{ // yes there is an array of right answers
            $tempArr1 = isset( $question_right_answers[0] ) ? $question_right_answers[0] : []; // set it
            $count1 = count($tempArr1); // get the count
        }

        if(count($question_wrong_answers) == 0){ //if there are cuurently no right answers - set an array of 2 with blanks
            $question_wrong_answers[0] = '';
            $count2 = 1;
        }else{ // yes there is an array of right answers
            $tempArr2 = isset( $question_wrong_answers[0] ) ? $question_wrong_answers[0] : []; // set it
            $count2 = count($tempArr2); // get the count
        }

        ?>

        </br>
        <div class="row">
            <ul id="ms_answers">
            <?php

            for($i = 0; $i < $count2; $i++){
                $q_wrong = isset( $question_wrong_answers[0] ) ? $question_wrong_answers[0] : [];
                $key_wrong =  isset( $q_wrong[$i] ) ? $q_wrong[$i] : '';

            ?>
            
            <li>    
            <!-- <label for="answer_wrong[<?php echo $i; ?>]">Incorrect Choice <?php echo $i + 1; ?>: </label> -->
            <input data-num="<?php echo $i;?>" style='width:50%' type='text' name="answer_wrong[<?php echo $i; ?>]"  value="<?php echo $key_wrong ?>">
            <input type="checkbox" name="answer_right[<?php echo $i; ?>]" value="<?php echo $key_wrong ?>">
            <label for="answer_right[<?php echo $i; ?>]">Correct Answer</label>
            <input type="button" value="Delete" name="delete_answer[<?php echo $i; ?>]" class="delete_button"> 
            </li>
            <br>
            <br>
            <?php } //end of for loop
            ?>
            </ul>
        </div>
        </br> 
        <span> Add New Option</span>
        <a id = "add_new_ms_answer" href="#" title="Add new Option">
            <span class="dashicons dashicons-insert"></span></br>
        </a> 
        
        <?php
    }

    function save_question_post( $post_id ) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (array_key_exists('question_weight_field', $_POST)) {
            update_post_meta( $post_id, '_question_weight_meta_key', $_POST['question_weight_field']);
        }
        if (array_key_exists('answer_right', $_POST)) { //TODO: replace with answers & answers_correct
            update_post_meta( $post_id, '_question_right_answers_meta', $_POST['answer_right']);
        }
        if (array_key_exists('answer_wrong', $_POST)) {
            update_post_meta( $post_id, '_question_wrong_answers_meta', $_POST['answer_wrong']);
        }

	}

    //settings for the column headers
    function custom_column_header($old_column_header){
        unset($old_column_header['title']);
        unset($old_column_header['author']);

        $new_column_header['author'] = 'Author';
        $new_column_header['question'] = 'Multiple Choice - multiple Answer Question';
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
                echo '[mc_multiple_question id= '.$post_id.']';
                break;
            default:
                break;
        }

    }

    //generates match question short code
    function mc_multiple_question_shortcode($atts){
        $ $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        $question = get_post($atts['id']);
        $question_right_answers = get_post_meta( $atts['id'], '_question_right_answers_meta' );
        $question_wrong_answers = get_post_meta( $atts['id'], '_question_wrong_answers_meta' );

        $q_right = isset($question_right_answers[0] ) ? $question_right_answers[0] : [];
        $q_wrong = isset($question_wrong_answers[0] ) ? $question_wrong_answers[0] : [];

        $all = array_merge($q_right, $q_wrong);

        $count = count($all);

        ob_start();
        echo '<div class="row" >'. $question->post_content.'</div>';
        echo '<form method="post" action="'.MS_PLUGIN_URL.'results/">';
        echo '<input type="hidden" id="questionID" name"questionID" value"'.$atts['id'].'">';

        for ($i = 0; $i < $count; $i++) {
            $key_print = $all[$i];
            ?>
                <input type="checkbox" id="user_choice_answers" name="UserChoice" value="<?php echo $key_print ?>"/>
                <label for="user_choice_answers[<?php echo $i ?>]"><?php echo $key_print ?></label><br>
            <?php
        }
            ?>
            <div class="row" ><input type="submit" name="user_question_submit" value="Submit Answers" /></div>
            </form>

            <?php
            return ob_get_clean();
    }

    //check results of matching question
    public static function mc_multiple_question_results($questionID, $question, $userAnswers){
        //todo
    }
}
