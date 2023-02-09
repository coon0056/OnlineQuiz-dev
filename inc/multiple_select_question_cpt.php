<?php

class multipleSelectObject {
    function __construct() {
        $this->create_post_type();
    }

    function create_post_type() {
        add_action('init', array($this,'register_post_type'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_action('save_post', array($this, 'save_question_post') );
        add_shortcode('multiple_select_question', array($this, 'multiple_select_question_shortcode'));

    }

    function register_post_type() {
        $question_labels = array(
            'name'               => 'MultipleSelects',
            'singular_name'      => 'MultipleSelect',
            'menu_name'          => 'MultipleSelects',
            'name_admin_bar'     => 'MultipleSelect',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New MultipleSelect',
            'new_item'           => 'New MultipleSelect',
            'edit_item'          => 'Edit MultipleSelect',
            'view_item'          => 'View MultipleSelect',
            'all_items'          => 'All MultipleSelects',
            'search_items'       => 'Search MultipleSelects',
            'parent_item_colon'  => 'Parent MultipleSelects:',
            'not_found'          => 'No MultipleSelects found.',
            'not_found_in_trash' => 'No MultipleSelects found in Trash.'
        );

        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-palmtree',
            'labels'    => $question_labels,
            'supports'  => array('editor', 'author', 'thumbnail')
        );

        register_post_type('MultipleSelect', $args);
    }

    function register_meta_boxes() {
        add_meta_box('ms_weight_meta', 'Multiple Select Weight', array($this, 'ms_weight_html'), 'MultipleSelect');
        add_meta_box('multiple_select_meta', 'Multiple Select Question', array($this, 'multiple_select_html'), 'MultipleSelect');
    }

    function ms_weight_html($post) {
        $value = get_post_meta( $post->ID, '_ms_weight_meta_key', true );
        ?>
        <div class='row'>
            <label for='ms_weight_field'></label>
            <input style='width:25%' type='number' name='ms_weight_field' min='0' value="<?php echo $value; ?>">
        </div>
        <?php
    }

    function multiple_select_html($post) {
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
            <ul id="incorrect_answers">
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
        <span> Add New Incorrect</span>
        <a id = "add_new_incorrect" href="#" title="Add new incorrect">
            <span class="dashicons dashicons-insert"></span></br>
        </a> 
        
        <?php
    }

    function save_question_post($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (array_key_exists('ms_weight_field', $_POST)) {
            update_post_meta( $post_id, '_ms_weight_meta_key', $_POST['ms_weight_field']);
        }
        if (array_key_exists('answer_right', $_POST)) { //TODO: replace with answers & answers_correct
            update_post_meta( $post_id, '_question_right_answers_meta', $_POST['answer_right']);
        }
        if (array_key_exists('answer_wrong', $_POST)) {
            update_post_meta( $post_id, '_question_wrong_answers_meta', $_POST['answer_wrong']);
        }
    }

    function multiple_select_question_shortcode($atts) {
        $atts = shortcode_atts(array(
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
    

}