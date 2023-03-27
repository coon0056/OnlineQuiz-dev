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
        add_meta_box('quiz_meta', 'Quizzes for student view', array($this, 'quizzes_html'), 'sview');    
    }


    //quiz  question metabox
    function quizzes_html($post){
        wp_nonce_field('quizzes', 'quiz_nonce');
        $quizzes = get_post_meta( $post->ID, '_quiz_meta');        
        $quizzes_link = get_post_meta( $post->ID, '_quizzes_link_meta');
        
        if(count($quizzes) == 0){
            $quizzes[0] = '';           
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
            $q_value = isset( $quizzes_link[0] ) ? $quizzes_link[0] : [];
            
            //checks for empty spots in the array and re-arranges
            if(is_array($q_key) && is_array($q_value)){
                $q_key = array_values($q_key);               
                $q_value = array_values($q_value);
            }

            for($i = 0; $i < $count; $i++){
                $key_print =  isset( $q_key[$i] ) ? $q_key[$i] : '';               
                $value_print = isset( $q_value[$i] ) ? $q_value[$i] : '';
            ?>

            <li>    
            <div class="label"><label  for="quizzes<?php echo $i; ?>]">Quiz <?php echo $i + 1; ?> id: </label></div>
            <div class="fields"><input data-num="<?php echo $i;?>" style='width:50%' type='text' name="quizzes[<?php echo $i; ?>]"  value="<?php echo esc_attr($key_print); ?>" required></div>
            
           
            <div class="label"><label  for="quizzes<?php echo $i; ?>]">Quiz <?php echo $i + 1; ?> post or page link: </label></div>
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


    }

    //generates student view shortcode
    function sview_shortcode($atts){

        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);
        $question = get_post($atts['id']);
        
        $quiz_id = get_post_meta( $atts['id'], '_quiz_meta');        
        $quiz_link = get_post_meta( $atts['id'], '_quizzes_link_meta'); 
        
        $q_keyid = isset( $quiz_id[0] ) ? $quiz_id[0] : [];
        $count = count($q_keyid);

        if(is_array($q_keyid)) {
            $q_keyid = array_values($q_keyid);
        }

        for($i = 0; $i < $count; $i++){  
        $content_post = get_post($q_keyid[$i]);
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);      
        $quiz_name[$i] = $content; 
        $author_id[$i] = get_post_field ('post_author', $q_keyid[$i]);
        $quiz_author[$i] = get_the_author_meta('nickname', $author_id[$i]);
        $quiz_date[$i] = get_the_date('Y-m-d', $q_keyid[$i], false);     
        }        
       
        $q_values= isset( $quiz_link[0] ) ? $quiz_link[0] : [];                
        $count = count($q_values);
        ob_start();        
         
        //checks for empty spots in the array and re-arranges
        if(is_array($quiz_name) && is_array($q_values) && is_array($quiz_author) && is_array($quiz_date)) {
            $quiz_name = array_values($quiz_name);
            $quiz_author = array_values($quiz_author);
            $quiz_date = array_values($quiz_date);
            $q_values = array_values($q_values);
        }
        ?>
        

<table class="minimalistBlack">
  <thead>
    <tr>
      <th>Name</th>
      <th>Author</th>
      <th>Date Created</th>
      <th></th>
    </tr>
  </thead>    
        <?php         
        for($i = 0; $i < $count; $i++){
            $key_name = $quiz_name[$i];
            $key_author = $quiz_author[$i];
            $key_date = $quiz_date[$i];
            $key_link = $q_values[$i];

            //$post_link = get_permalink($key_link, false);
            ?>            
            <tbody>
            <tr>
            <td><?php echo $key_name; ?></td>
            <td><?php echo $key_author; ?></td>
            <td><?php echo $key_date; ?></td>
            <td><a href="<?php echo $key_link;?>" class="myButton">Start Quiz</a></td>
            </tr>
            </tbody>                           
            <?php
       }
        ?>            
        </table> 
        <hr class="wp-block-separator has-text-color has-css-opacity has-background is-style-dots">              
        <?php
        return ob_get_clean();                 

}
}