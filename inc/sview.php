<?php
class sview{

    //class constructor
    function __construct(){
        $this->create_post_type();        
    }

    //Creates the post type and it's corresponding settings
    function create_post_type(){
        add_action('init', array($this,'register_post_type'));
        add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
        add_filter('manage_sview_posts_columns', array($this, 'custom_column_header'));
        add_filter('manage_sview_posts_custom_column', array($this, 'custom_column_content'), 10,2);
        add_action('save_post', array($this, 'save_sview_post'));
        add_shortcode('sview', array($this, 'sview_shortcode'));
        //add_action('admin_menu', array($this,'quiz_plugin_menu'));
    }

    

    //registers custom post type
    function register_post_type(){

        $quiz_labels = array(
            'name'               => 'Students View',
            'singular_name'      => 'Student View',
            'menu_name'          => 'Student View',
            'name_admin_bar'     => 'Student View',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Student View',
            'new_item'           => 'New Student View',
            'edit_item'          => 'Edit Student View',
            'view_item'          => 'View Student View',
            'all_items'          => 'All Student Views',
            'search_items'       => 'Search Student Views',
            'parent_item_colon'  => 'Parent Student Views:',
            'not_found'          => 'No Student Views found.',
            'not_found_in_trash' => 'No Student Views found in Trash.'
        );

        $args = array(
            'public'    => true,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'labels'    => $quiz_labels,
            'show_in_menu' => false,
            'supports'  => array('editor', 'author', 'thumbnail')
        );

        register_post_type('sview', $args);
    }

    //creates the metaboxes 
    function register_meta_boxes(){
        //add_meta_box('quiz_time_limit','Quiz Time Limit',array($this, 'quiz_time_limit_html'),'quiz');
        //add_meta_box('question_password', 'Quiz Password', array($this, 'quiz_password_html'), 'quiz');
        add_meta_box('quiz_meta', 'Quizzes for student view', array($this, 'quizzes_html'), 'sview');
        //add_meta_box('quizzes_link_meta', 'Link for the quiz', array($this, 'quizzes_html'), 'sview');
    }

    // quiz time limit meta box
/*     function quiz_time_limit_html($post){
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
    } */

    // quiz password metabox

    
   /*  function quiz_password_html($post){
        wp_nonce_field('quiz_password_field', 'quiz_nonce');
        $password = get_post_meta( $post->ID, '_quiz_password_meta_key', true );
        ?>
        <div class="row">
        <label for="quiz_password_field"></label>
        <input style='width:25%' type='password' name='quiz_password_field' value="<?php echo esc_attr($password); ?>">
        </div>
        <?php
    } */

    //quiz  question metabox
    function quizzes_html($post){
        wp_nonce_field('quizzes', 'quiz_nonce');
        $quizzes = get_post_meta( $post->ID, '_quiz_meta');
        $quiz_author = get_post_meta( $post->ID, '_quiz_author_meta');
        $quiz_date = get_post_meta( $post->ID, '_quiz_date_meta');
        $quizzes_link = get_post_meta( $post->ID, '_quizzes_link_meta');
        
        if(count($quizzes) == 0){
            $quizzes[0] = '';
            $quiz_author[0] = '';
            $quiz_date[0] = '';
            $quizzes_link[0] = '';
             $count = 1;
        }else{
            $tempArr = isset( $quizzes[0] ) ? $quizzes[0] : [];
            $count = count($tempArr);
        }

        ?>

        <span> Add New Quiz </span>
        <a id = "add_new_quiz" href="#" title="Add new Quiz">
            <span class="dashicons dashicons-insert"></span></br>
        </a>
        
        </br>
        <div class="row">
            <ul id="quiz_sview">
            <?php
            
            $q_key = isset( $quizzes[0] ) ? $quizzes[0] : [];
            $q_author = isset( $quiz_author[0] ) ? $quiz_author[0] : [];
            $q_date = isset( $quiz_date[0] ) ? $quiz_date[0] : [];
            $q_value = isset( $quizzes_link[0] ) ? $quizzes_link[0] : [];

            
            //checks for empty spots in the array and re-arranges
            if(is_array($q_key) && is_array($q_value) && is_array($q_author) && is_array($q_date)){
                $q_key = array_values($q_key);
                $q_author = array_values($q_author);
                $q_date = array_values($q_date);
                $q_value = array_values($q_value);
            }

            for($i = 0; $i < $count; $i++){
                $key_print =  isset( $q_key[$i] ) ? $q_key[$i] : '';
                $author_print =  isset( $q_author[$i] ) ? $q_author[$i] : '';
                $date_print =  isset( $q_date[$i] ) ? $q_date[$i] : '';
                $value_print = isset( $q_value[$i] ) ? $q_value[$i] : '';
            ?>

            <li>    
            <div class="label"><label  for="quizzes<?php echo $i; ?>]">Quiz <?php echo $i + 1; ?> Name: </label></div>
            <div class="fields"><input data-num="<?php echo $i;?>" style='width:50%' type='text' name="quizzes[<?php echo $i; ?>]"  value="<?php echo esc_attr($key_print); ?>" required></div>
            
            <div class="label"><label  for="quizzes<?php echo $i; ?>]">Quiz <?php echo $i + 1; ?> Author: </label></div>
            <div class="fields"><input data-num="<?php echo $i;?>" style='width:50%' type='text' name="quiz_author[<?php echo $i; ?>]"  value="<?php echo esc_attr($author_print); ?>" required></div>

            <div class="label"><label  for="quizzes<?php echo $i; ?>]">Quiz <?php echo $i + 1; ?> Date: </label></div>
            <div class="fields"><input data-num="<?php echo $i;?>" style='width:50%' type='date' name="quiz_date[<?php echo $i; ?>]"  value="<?php echo esc_attr($date_print); ?>" required></div>

            <div class="label"><label  for="quizzes<?php echo $i; ?>]">Quiz <?php echo $i + 1; ?> Link: </label></div>
            <div class="fields"><input data-num="<?php echo $i;?>" style='width:50%' type='text' name="quizzes_link[<?php echo $i; ?>]"  value="<?php echo esc_attr($value_print); ?>" required>
            
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
                echo '[sview id= '.$post_id.']';
                break;
            default:
                break;
        }

    }

    //saves post metaboxes
    function save_sview_post( $post_id ) {
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
            return $post_id;
        }

        if (!isset($_POST['quiz_nonce'])){
            return $post_id;
        }

        $nonce = $_POST['quiz_nonce'];
        if (!wp_verify_nonce($nonce, 'quizzes')){
            return $post_id;
        }

        if ( array_key_exists( 'quizzes', $_POST ) ) {
            sanitize_text_field($_POST['quiz']);
            update_post_meta($post_id,'_quiz_meta',$_POST['quizzes']);
        }
        if ( array_key_exists( 'quizzes_link', $_POST ) ) {
            sanitize_text_field($_POST['quizzes_link']);
            update_post_meta($post_id,'_quizzes_link_meta',$_POST['quizzes_link']);
        }

        if ( array_key_exists( 'quiz_author', $_POST ) ) {
            sanitize_text_field($_POST['quiz_author']);
            update_post_meta($post_id,'_quiz_author_meta',$_POST['quiz_author']);
        }

        if ( array_key_exists( 'quiz_date', $_POST ) ) {
            sanitize_text_field($_POST['quiz_date']);
            update_post_meta($post_id,'_quiz_date_meta',$_POST['quiz_date']);
        }

    }

    //generates student view shortcode
    function sview_shortcode($atts){

        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);
        $question = get_post($atts['id']);
        $quiz_name = get_post_meta( $atts['id'], '_quiz_meta'); 
        $quiz_author = get_post_meta( $atts['id'], '_quiz_author_meta'); 
        $quiz_date = get_post_meta( $atts['id'], '_quiz_date_meta'); 
        $quiz_link = get_post_meta( $atts['id'], '_quizzes_link_meta');  
        
        $test = $quiz_link[0];

        //$test = get_post_meta( $atts['0'], '_quiz_meta'); 

        $q_key = isset( $quiz_name[0] ) ? $quiz_name[0] : [];
        $q_author = isset( $quiz_author[0] ) ? $quiz_author[0] : [];
        $q_date = isset( $quiz_date[0] ) ? $quiz_date[0] : [];
        $q_values= isset( $quiz_link[0] ) ? $quiz_link[0] : [];
                
        $count = count($q_values);

        ob_start();
        echo '<div class="row" >'. $question->post_content.'</div>';     
         
        //checks for empty spots in the array and re-arranges
        if(is_array($q_key) && is_array($q_values)) {
            $q_key = array_values($q_key);
            $q_author = array_values($q_author);
            $q_date = array_values($q_date);
            $q_values = array_values($q_values);
        }

        ?>
        


<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Author</th>
      <th>Date Available </th>
      <th>Start Quiz</th>
    </tr>
  </thead>  
</table>

        <?php
         
        for($i = 0; $i < $count; $i++){
            $key_name =$q_key[$i];
            $key_author =$q_author[$i];
            $key_date =$q_date[$i];
            $key_link = $q_values[$i];
            ?>
            <table>  
            <tbody>
            <tr>
            <td><?php echo $key_name; ?></td>
            <td><?php echo $key_author; ?></td>
            <td><?php echo $key_date; ?></td>
            <td><a href="<?php echo $key_link;?>"><button>Start Quiz</button></a></td>
            </tr>
            </tbody>
            </table>
                
                <?php

       }
        ?>            
        
        <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots">              
        <?php
        return ob_get_clean();
       
           

}
}