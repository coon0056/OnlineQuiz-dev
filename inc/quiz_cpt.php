<?php
class Quiz_CPT{

    //class constructor
    function __construct(){
        $this->create_post_type();
        
    }

    //Creates the post type and it's corresponding settings
    function create_post_type(){
        add_action('init', array($this,'register_post_type'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_filter('manage_quiz_posts_columns', array($this, 'custom_column_header'));
        add_filter('manage_quiz_posts_custom_column', array($this, 'custom_column_content'), 10,2);
        add_action('save_post', array($this, 'save_quiz_post'));
        add_shortcode('quiz', array($this, 'quiz_shortcode'));
        add_action('admin_menu', array($this,'quiz_plugin_menu'), 999);
    }

    //function to setup quiz plugin menu and sub menu
    function quiz_plugin_menu(){
        add_submenu_page('edit.php?post_type=quiz', 'matching question', 'Matching Question', "manage_options", 'edit.php?post_type=matching_question');
        add_submenu_page('edit.php?post_type=quiz', 'ordering question', 'Ordering Question', "manage_options", 'edit.php?post_type=ordering_question');
        add_submenu_page('edit.php?post_type=quiz', 'mc-single question', 'MC-Single Question', "manage_options", 'edit.php?post_type=mc_single_question');
        add_submenu_page('edit.php?post_type=quiz', 'mc-multiple question', 'MC-Multiple Question', "manage_options", 'edit.php?post_type=mc_multiple_question');
        add_submenu_page('edit.php?post_type=quiz', 'short-answer question', 'Short-Answer Question', "manage_options", 'edit.php?post_type=sa_question');
        add_submenu_page('edit.php?post_type=quiz', 'sview', 'Student Quiz View', "manage_options", 'edit.php?post_type=sview');
    }

    //registers custom post type
    function register_post_type(){

        //sets custom post type labels
        $quiz_labels = array(
            'name'               => 'Quizzes',
            'singular_name'      => 'Quiz',
            'menu_name'          => 'Quizzes',
            'name_admin_bar'     => 'Quiz',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Quiz',
            'new_item'           => 'New Quiz',
            'edit_item'          => 'Edit Quiz',
            'view_item'          => 'View Quiz',
            'all_items'          => 'All Quizzes',
            'search_items'       => 'Search Quizzes',
            'parent_item_colon'  => 'Parent Quizzes:',
            'not_found'          => 'No Quizzes found.',
            'not_found_in_trash' => 'No Quizzes found in Trash.'
        );


        $capabilities = create_post_type_capabilities('quiz', 'quizzes');

        //sets custom post type settings
        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'labels'    => $quiz_labels,
            'supports'  => array('editor', 'author', 'thumbnail'),
            'capabilities'  => $capabilities,
            'map_meta_cap'  => true
        );

        register_post_type('quiz', $args);
    }

    //creates the metaboxes
    function register_meta_boxes(){
        add_meta_box('quiz_time_limit','Quiz Time Limit',array($this, 'quiz_time_limit_html'),'quiz');
        //add_meta_box('question_password', 'Quiz Password', array($this, 'quiz_password_html'), 'quiz');
        add_meta_box('questions_meta', 'Questions', array($this, 'questions_html'), 'quiz');
    }

    // quiz time limit meta box
    function quiz_time_limit_html($post){
        wp_nonce_field('quiz_time_limit_field', 'quiz_nonce');
        $time = get_post_meta( $post->ID, '_quiz_time_limit_meta_key', true );
        if($time == ''){
            $time = 60;
        }
        ?>
        <div class="row">
        <label for="quiz_time_limit_field"></label>
        <input style='width:25%' type='number' name='quiz_time_limit_field' min="10" value="<?php echo esc_attr($time); ?>">
        </div>
        <?php
    }

    // quiz password metabox
    function quiz_password_html($post){
        wp_nonce_field('quiz_password_field', 'quiz_nonce');
        $password = get_post_meta( $post->ID, '_quiz_password_meta_key', true );
        ?>
        <div class="row">
        <label for="quiz_password_field"></label>
        <input style='width:25%' type='password' name='quiz_password_field' value="<?php echo esc_attr($password); ?>">
        </div>
        <?php
    }

    //quiz  question metabox
    function questions_html($post){
        wp_nonce_field('questions', 'quiz_nonce');
        $questions = get_post_meta( $post->ID, '_quiz_questions_meta_key');
        
        if(count($questions) == 0){
            $questions[0] = '';
            $count = 1;
        }else{
            $tempArr = isset( $questions[0] ) ? $questions[0] : [];
            $count = count($tempArr);
        }

        ?>

        <span> Add New Question </span>
        <a id = "add_new_question" href="#" title="Add new question">
            <span class="dashicons dashicons-insert"></span></br>
        </a>
        
        </br>
        <div class="row">
            <ul id="question_short_code">
            <?php
            
            $q_key = isset( $questions[0] ) ? $questions[0] : [];
            
            //checks for empty spots in the array and re-arranges
            if(is_array($q_key) ){
                $q_key = array_values($q_key);
            }

            //iterate through post meta values and prints html
            for($i = 0; $i < $count; $i++){
                $key_print =  isset( $q_key[$i] ) ? $q_key[$i] : '';
            ?>

            <li>    
            <div class="label"><label  for="questions<?php echo $i; ?>]">Question <?php echo $i + 1; ?> Short Code: </label></div>
            <div class="fields">
                <input data-num="<?php echo $i;?>" style='width:50%' type='text' name="questions[<?php echo $i; ?>]"  value="<?php echo esc_attr($key_print); ?>" required>
                <input type="button" value="Delete" name="delete_answer[<?php echo $i; ?>]" class="delete_button">
            </div>
            </li>
            <?php } 
            ?>
            </ul>
        </div>  
        <?php   
    }

    //settings for the column headers
    function custom_column_header($old_column_header){
        unset($old_column_header['title']);
        unset($old_column_header['author']);
        unset($old_column_header['date']);

        $new_column_header['quiz'] = 'Quiz';
        $new_column_header['author'] = 'Author';
        $new_column_header['shortcode'] = 'Short Code';
        $new_column_header['date'] = 'Date Created';
        
        return $new_column_header;

    }

    //content shown for the summary question table
    function custom_column_content($column_name, $post_id){
        $quiz = esc_html(get_the_content($post_id));
        

        switch($column_name) {
            case 'quiz':
                echo '<strong>'.$quiz.'</strong>';
                break;
            case 'shortcode':
                echo '[quiz id= '.$post_id.']';
                break;
            default:
                break;
        }

    }

    //saves post metaboxes
    function save_quiz_post( $post_id ) {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
            return $post_id;
        }

        if (!isset($_POST['quiz_nonce'])){
            return $post_id;
        }

        $nonce = $_POST['quiz_nonce'];
        if (!wp_verify_nonce($nonce, 'questions') && (!wp_verify_nonce($nonce, 'quiz_time_limit_field')) && (!wp_verify_nonce($nonce, 'quiz_password_field'))){
            return $post_id;
        }

        if ( array_key_exists( 'quiz_time_limit_field', $_POST ) ) {
            sanitize_text_field($_POST['quiz_time_limit_field']);
            update_post_meta($post_id,'_quiz_time_limit_meta_key',$_POST['quiz_time_limit_field']);
        }

        if ( array_key_exists( 'quiz_password_field', $_POST ) ) {
            sanitize_text_field($_POST['quiz_password_field']);
            update_post_meta($post_id,'_quiz_password_meta_key',$_POST['quiz_password_field']);
        }

        if ( array_key_exists( 'questions', $_POST ) ) {
            sanitize_text_field($_POST['questions']);
            update_post_meta($post_id,'_quiz_questions_meta_key',$_POST['questions']);
        }

    }

    //generates match question short code
    function quiz_shortcode($atts){
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        $quiz = get_post($atts['id']);
        $questions = get_post_meta( $atts['id'], '_quiz_questions_meta_key');
        $q_shortcodes = isset( $questions[0] ) ? $questions[0] : [];

        $authorID = get_post_field('post_author', $atts['id']);
        $authorEmail = get_the_author_meta('user_email', $authorID);

        //checks for empty spots in the array and re-arranges
        if(is_array($q_shortcodes) ){
            $q_shortcodes = array_values($q_shortcodes);
        }

        $count = count($q_shortcodes);
        $time = get_post_meta( $quiz->ID, '_quiz_time_limit_meta_key', true );
        
        ob_start();
        ?>
        <div class="countdown" data-num="<?php echo $time; ?>"></div>
        <form id="quizForm" method="post" action="<?php echo ONLINE_QUIZ_PLUGIN_URL ?>results/">
            <input type="hidden" id="authorEmail" name="authorEmail" value="<?php echo $authorEmail ?>">
            <input type="hidden" id="questionTotal" name="questionTotal" value="<?php echo $count ?>">
            Name:
            <input style="width:25%" type="text" id="testTaker" name="testTaker" value="" required>

            <ul id="questions" style="list-style-type: none">
        
            <div id="modal" class="modal">
                <div class="modal-content">
                    <span id="close" class="close">&times;</span>
                    <p id="modalMessage"></p>
                </div>
            </div>

            <?php

            for($i = 0; $i < $count; $i++){
                $questionCount = ($i+1);
                $questionID = (int)filter_var($q_shortcodes[$i], FILTER_SANITIZE_NUMBER_INT);
                
                echo '<li id="question" data-num="'.$i.'">';
                    echo '<input type="hidden" id="questionID'.$questionCount.'" name="questionID'.$questionCount.'" value="'.$questionID.'">';
                    echo 'Question '.$questionCount.':';
                    echo do_shortcode($q_shortcodes[$i]);
                    echo "</br>";
                echo '</li>';
            }
            ?>
            </ul>
            <div id="pagination"></div>

            <div class="row" ><input class="submit-button" type="submit" name="user_question_submit" id="user_question_submit" value="Submit Answers" /></div>
        </form>
        <?php
        return ob_get_clean();
    }

}