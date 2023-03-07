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
        add_meta_box('question_weight_meta','Multiple Choice - Multiple Answer Question Weight',array($this, 'question_weight_html'),'mc_multiple_question');
        add_meta_box('answer_meta', 'Multiple Choice - Multiple Answer Question', array($this, 'mc_multiple_question_html'), 'mc_multiple_question');
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

    //creates mc-multiple question metabox html
    function mc_multiple_question_html($post){
        $question_right_answers = get_post_meta( $post->ID, '_question_right_answers_meta', true);
        $answers = get_post_meta( $post->ID, '_answers_meta');

        if(count($answers) == 0){ //if there are cuurently no right answers - set an array of 2 with blanks
            $answers[0] = '';
            $answers[1] = '';
            $count2 = 2;
        }else{ // yes there is an array of right answers
            $tempArr2 = isset( $answers[0] ) ? $answers[0] : []; // set it
            $count2 = count($tempArr2); // get the count
        }

        ?>
        <span> Add New Option</span>
        <a id = "add_new_ms_answer" href="#" title="Add new Option">
            <span class="dashicons dashicons-insert"></span></br>
        </a>
        </br>
        <div class="row">
            <ul id="ms_answers">
            <?php
            $q_answers = isset( $answers[0] ) ? $answers[0] : [];

            if (is_array($q_answers)) {
                $q_answers = array_values($q_answers);
            }
            
            for($i = 0; $i < $count2; $i++){

                $answer_key =  isset( $q_answers[$i] ) ? $q_answers[$i] : '';

                if (is_array($question_right_answers)) {
                    $j = array_search($answer_key, $answers[0]);
                }

                $checked = (is_array($question_right_answers) && array_key_exists($j, $question_right_answers)
                && $question_right_answers[$j] == 'on') ? 'checked' : '';
            ?>
            <li id="ms_answer">
                <div class="label"><label for="answers[<?php echo $i; ?>]"> Answer(s): </label></div>
                <input data-num="<?php echo $i;?>" style='width:50%' type='text' name="answers[<?php echo $i; ?>]"  value="<?php echo $answer_key ?>">
                <input type="checkbox" name="answers_right[<?php echo $i; ?>]" <?php echo $checked ?>>
                <label for="answers_right[<?php echo $i; ?>]">Correct Answer</label>
                <input type="button" value="Delete" name="delete_answer[<?php echo $i; ?>]" class="delete_button"> 
                <br>
                <br>
            </li>
            <?php } //end of for loop
            ?>
            </ul>
        </div>
        </br>
        <?php
    }

    //save post meta values
    function save_question_post( $post_id ) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if (array_key_exists('question_weight_field', $_POST)) {
            update_post_meta( $post_id, '_question_weight_meta_key', $_POST['question_weight_field']);
        }
        if (array_key_exists('answers_right', $_POST)) {
            update_post_meta( $post_id, '_question_right_answers_meta', $_POST['answers_right']);
        }
        if (array_key_exists('answers', $_POST)) {
            update_post_meta( $post_id, '_answers_meta', $_POST['answers']);
        }

	}

    //settings for the column headers
    function custom_column_header($old_column_header){
        unset($old_column_header['title']);
        unset($old_column_header['author']);

        $new_column_header['author'] = 'Author';
        $new_column_header['question'] = 'Multiple Choice - Multiple Answer Question';
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

    //generates mc-multiple question short code
    function mc_multiple_question_shortcode($atts){
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        $question = get_post($atts['id']);
        $question_right_answers = get_post_meta( $atts['id'], '_question_right_answers_meta', true );
        $answers = get_post_meta( $atts['id'], '_answers_meta' );

        $q_answers = isset($answers[0] ) ? $answers[0] : [];

        shuffle($q_answers);

        $count = count($q_answers);

        ob_start();
        echo '<div class="row" >'. $question->post_content.'</div>';
        echo '<input type="hidden" id="questionID" name"questionID" value"'.$atts['id'].'">';

        for ($i = 0; $i < $count; $i++) {
            $key_print = $q_answers[$i];
                $key_index = array_search($key_print, $answers[0]);
            ?>
            </br>
            <div class="row">
                <input type="checkbox" id="user_choice_answers<?php echo $atts['id']; ?>[<?php echo $key_index ?>]" name="user_choice_answers<?php echo $atts['id']; ?>[<?php echo $key_index ?>]" value="<?php echo $key_print ?>" />
                <label for="user_choice_answers<?php echo $atts['id']; ?>[<?php echo $key_index ?>]"><?php echo $key_print ?></label><br>
            </div>    
            <?php
        }
            ?>

            <?php
            ?> <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots"> <?php
            return ob_get_clean();
    }

    //check results of mc-multiple question
    public static function mc_multiple_question_results($questionID, $question, $userAnswers, &$userScore) {
        ?><div class="row-mc-multiple-qtype" ><?php
        $question_right_answers = get_post_meta( $questionID, '_question_right_answers_meta', true );
        $question_answers = get_post_meta( $questionID, '_answers_meta' );
        $q_answers = isset($question_answers[0]) ? $question_answers[0] : [];

            $pointWeight = get_post_meta( $questionID, '_question_weight_meta_key',true);
            $countCorrect = count($q_answers);
        $correct = 0;

        ?>
            <div class="row-title">
            <?php echo $question->post_content; ?>
        </div>
        <?php
        for ($i = 0; $i < count($q_answers); $i++) {
            $key_print = $q_answers[$i];
            $user_answer = array_key_exists($i, $userAnswers);
            $answer_exists = array_key_exists($i, $question_right_answers);
            $checked = ($user_answer) ? 'checked' : '';
        ?>
            </br>
            <div class="row">
                    <div class ="column col-mc-single">
                        <input type="checkbox" id="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" value="<?php echo $key_print ?>" disabled <?php echo $checked;?>/>
                <label for="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]"><?php echo $key_print ?></label><br>
                    </div>
                    <?php
                    if (($user_answer && $answer_exists) || 
                        (!$answer_exists && !$user_answer)
                    ) {
                            $correct++;
                            ?> <div class="column"><span class="correct-ans">Correct!</span></div> <?php
                    } else if ( $user_answer && !$answer_exists ) {
                            ?> <div class="column"><span class="incorrect-ans">Incorrect.</span></div> <?php
                    } else if (!$user_answer && $answer_exists) {
                            ?> <div class="column"><span class="actual-correct-ans">This is a correct answer!</span></div> <?php
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
