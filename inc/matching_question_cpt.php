<?php
class Matching_Question{

    //class constructor
    function __construct(){
        $this->create_post_type();
    }

    //Creates the post type and it's corresponding settings
    function create_post_type(){
        add_action('init', array($this,'register_post_type'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_filter('manage_matching_question_posts_columns', array($this, 'custom_column_header'));
        add_filter('manage_matching_question_posts_custom_column', array($this, 'custom_column_content'), 10,2);
        add_action('save_post', array($this, 'save_question_post'));
        add_shortcode('matching_question', array($this, 'match_question_shortcode'));
    }

    //registers custom post type
    function register_post_type(){

        $question_labels = array(
            'name'               => 'Matching Questions',
            'singular_name'      => 'Matching Question',
            'menu_name'          => 'Matching Questions',
            'name_admin_bar'     => 'Matching Question',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Matching Question',
            'new_item'           => 'New Matching Question',
            'edit_item'          => 'Edit Matching Question',
            'view_item'          => 'View Matching Question',
            'all_items'          => 'All Matching Questions',
            'search_items'       => 'Search Matching Questions',
            'parent_item_colon'  => 'Parent Matching Questions:',
            'not_found'          => 'No Matching Questions found.',
            'not_found_in_trash' => 'No Matching Questions found in Trash.'
        );

        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-editor-ul',
            'labels'    => $question_labels,
            'show_in_menu' => false,
            'supports'  => array('editor', 'author', 'thumbnail')
        );

        register_post_type('matching_question', $args);
    }

    //creates the metaboxes 
    function register_meta_boxes(){
        add_meta_box('question_weight_meta','Matching Question Weight',array($this, 'question_weight_html'),'matching_question');
        add_meta_box('answer_meta', 'Matching Matching Question', array($this, 'matching_question_html'), 'matching_question');
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

    //creates matching question metabox html
    function matching_question_html($post){
        $question_keys = get_post_meta( $post->ID, '_question_keys_meta');
        $question_answers = get_post_meta( $post->ID, '_question_answers_meta');

        if(count($question_keys) == 0 && count($question_answers) == 0){
            $question_keys[0] = '';
            $question_keys[1] = '';
            $question_answers[0] = '';
            $question_answers[1] = '';
            $count = 2;
        }else{
            $tempArr = isset( $question_keys[0] ) ? $question_keys[0] : [];
            $count = count($tempArr);
        }

        ?>

        <span> Add Key Value Pair</span>
        <a id = "add_new_kv_pair" href="#" title="Add new key-value pair">
            <span class="dashicons dashicons-insert"></span></br>
        </a>
        
        </br>
        <div class="row">
            <ul id="key-value-pairs">
            <?php
            //checks if array is set
            $q_key = isset( $question_keys[0] ) ? $question_keys[0] : [];
            $q_value = isset( $question_answers[0] ) ? $question_answers[0] : [];

            //checks for empty spots in the array and re-arranges
            if(is_array($q_key) && is_array($q_value) ){
                $q_key = array_values($q_key);
                $q_value = array_values($q_value);
            }

            for($i = 0; $i < $count; $i++){
                $key_print =  isset( $q_key[$i] ) ? $q_key[$i] : '';
                $value_print = isset( $q_value[$i] ) ? $q_value[$i] : '';
            ?>

            <li>    
                <div class="label"><label  for="question_keys[<?php echo $i; ?>]">Key <?php echo $i + 1; ?>: </label></div>
                <div class="fields"><input data-num="<?php echo $i;?>" style='width:50%' type='text' name="question_keys[<?php echo $i; ?>]"  value="<?php echo $key_print; ?>"></div>
                <div class="label"><label for="question_answers[<?php echo $i; ?>]">Value <?php echo  $i + 1; ?>:</label></div>
                <div class="fields">
                    <input style='width:50%' type='text' name="question_answers[<?php echo $i; ?>]"  value="<?php echo  $value_print; ?>">
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

		if ( array_key_exists( 'question_weight_field', $_POST ) ) {
			update_post_meta($post_id,'_question_weight_meta_key',$_POST['question_weight_field']);
		}

        if ( array_key_exists( 'question_keys', $_POST ) ) {
			update_post_meta($post_id,'_question_keys_meta',$_POST['question_keys']);
		}

        if ( array_key_exists( 'question_answers', $_POST ) ) {
			update_post_meta($post_id,'_question_answers_meta',$_POST['question_answers']);
		}

	}

    //settings for the column headers
    function custom_column_header($old_column_header){
        unset($old_column_header['title']);
        unset($old_column_header['author']);

        $new_column_header['author'] = 'Author';
        $new_column_header['question'] = 'Matching Question';
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
                echo '[matching_question id= '.$post_id.']';
                break;
            default:
                break;
        }

    }

    //generates match question short code
    function match_question_shortcode($atts){
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        $question = get_post($atts['id']);
        $question_keys = get_post_meta( $atts['id'], '_question_keys_meta');
        $question_answers = get_post_meta( $atts['id'], '_question_answers_meta');
    
        $q_key = isset( $question_keys[0] ) ? $question_keys[0] : [];
        $q_values= isset( $question_answers[0] ) ? $question_answers[0] : [];
        shuffle($q_values);
        
        $count = count($q_values);

        ob_start();
        echo '<div class="row" >'. $question->post_content.'</div>';
        
        for($i = 0; $i < $count; $i++){
            $key_print =$q_key[$i];
        ?>
            </br>
            <div class="row" >   
                <label for="user_choice_answers<?php echo $atts['id'] ?>"><?php echo $key_print; ?>:</label>
                    <select style='width:50%' name="user_choice_answers<?php echo $atts['id'] ?>[<?php echo $i ?>]" id="user_choice_answers<?php echo $atts['id'] ?>[<?php echo $i ?>]" class="postbox">
                        <option value=''>Select Matching Value</option>
                                <?php
                                foreach($q_values as $item){
                                    echo "<option value='$item'>$item</option>";
                                }
                                ?>
                    </select>
                </div>
    
        <?php 
        }
        ?> <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots"> <?php
        return ob_get_clean();
    }

    //check results of matching question
    public static function matching_question_results($questionID, $question, $userAnswers, &$userScore){
        ?><div class="row-match-qtype" ><?php
            $question_answers = get_post_meta( $questionID, '_question_answers_meta');
            $q_answers= isset( $question_answers[0] ) ? $question_answers[0] : [];

            $question_keys = get_post_meta( $questionID, '_question_keys_meta');
            $q_key = isset( $question_keys[0] ) ? $question_keys[0] : [];
        
            $pointWeight = get_post_meta( $questionID, '_question_weight_meta_key',true);
            $countCorrect = count($q_answers);
            $correct = 0;

            ?> <div class="row-title"> <?php echo $question->post_content; ?> </div><?php
            
            for($i = 0; $i < count($q_answers); $i++){
                $key_print =$q_key[$i];
                
            ?>
                <div class="row" > 
                    <div class ="column col-dropdown">  
                        <label for="user_choice_answers"><?php echo $key_print; ?>:</label>
                        <div class="row" > 
                            <div class ="column col-match">
                                <select name="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" id="user_choice_answers<?php echo $questionID; ?>[<?php echo $i ?>]" class="postbox">  
                                <option value=''><?php echo $userAnswers[$i]?></option>               
                                </select>
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="row" > 
                        <?php
                        if($userAnswers[$i] == $q_answers[$i] ){
                            $correct++;
                            ?> <div class="column"><span class="correct-ans">Correct!</span></div> <?php
                        }else{
                            ?> <div class="column"><span class="incorrect-ans">Incorrect. Correct Answer: <?php echo $q_answers[$i]; ?> </span></div> <?php
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
