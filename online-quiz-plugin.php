<?php
/**
 * Plugin Name: Online Quiz
 * Description: Online Quiz for Kentokukan
 * Plugin URI: 
 * Version: 1.0.0
 * Author: Team RoundHouse
 * Author URI: 
 * License: GPLv2 or later
 */
if(!defined('ABSPATH')){
   die;
}

if(!class_exists('OnlineQuizPlugin')){
   class OnlineQuizPlugin
   {

      //constructor
      function __construct(){
         $this->define_constants();
         $this->load_required_files();
          
         if(!isset($quiz_object)){
            $quiz_object = new Quiz_CPT();
         }
         if(!isset($sview_object)){
            $sview_object = new sview();
         }         
         if(!isset($matching_question_object)){
            $matching_question_object = new Matching_Question();
         }
         if(!isset($ordering_question_object)){
            $ordering_question_object = new Ordering_Question();
         }
         if(!isset($mc_single_question_object)){
            $mc_single_question_object = new Mc_Single_Question();
         }
         if(!isset($multiple_select_question_object)){
            $multiple_select_question_object = new Mc_Multiple_Question();
         }
         if(!isset($sa_question_object)){
            $sa_question_object = new sa_Question();
         }
        
         $this->enqueue_assets();
      }

 
     //constants for the plugin
      function define_constants(){
         if(!defined('ONLINE_QUIZ_BASE_FILE')){
            define('ONLINE_QUIZ_BASE_FILE', __FILE__);
         }
         if(!defined('ONLINE_QUIZ_BASE_DIR')){
            define('ONLINE_QUIZ_BASE_DIR', dirname(ONLINE_QUIZ_BASE_FILE));
         }
         if(!defined('ONLINE_QUIZ_PLUGIN_URL')){
            define('ONLINE_QUIZ_PLUGIN_URL', plugin_dir_url(__FILE__));
         }
         if(!defined('ONLINE_QUIZ_PLUGIN_DIR')){
            define('ONLINE_QUIZ_PLUGIN_DIR', plugin_dir_path(__FILE__));
         }
      }

      //required files for plugin
      function load_required_files(){
         require_once ONLINE_QUIZ_BASE_DIR.'/inc/generic_functions.php';
         require_once ONLINE_QUIZ_BASE_DIR.'/inc/matching_question_cpt.php';
         require_once ONLINE_QUIZ_BASE_DIR.'/inc/ordering_question_cpt.php';
         require_once ONLINE_QUIZ_BASE_DIR.'/inc/mc_single_question_cpt.php';
         require_once ONLINE_QUIZ_BASE_DIR.'/inc/mc_multiple_question_cpt.php'; 
         require_once ONLINE_QUIZ_BASE_DIR.'/inc/sa_question_cpt.php'; 
         require_once ONLINE_QUIZ_BASE_DIR.'/inc/quiz_cpt.php';
         require_once ONLINE_QUIZ_BASE_DIR.'/inc/sview.php';
      }
     
      function enqueue_assets(){
         add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
         add_action('wp_enqueue_scripts', array($this, 'frontend_scripts'));
         add_action('wp_enqueue_scripts', array($this, 'frontend_style'));
      }

      function admin_scripts(){
         wp_enqueue_script('online_quiz_admin_script', ONLINE_QUIZ_PLUGIN_URL.'js/admin.js', array('jquery'));
      }

      function frontend_scripts(){
         wp_enqueue_script('online_quiz_frontend_script', ONLINE_QUIZ_PLUGIN_URL.'js/frontend.js', array('jquery'));
         wp_enqueue_script('online_quiz_timer_script', ONLINE_QUIZ_PLUGIN_URL.'js/timer.js', array('jquery'));
         wp_enqueue_script('simplePagination-js','//cdnjs.cloudflare.com/ajax/libs/simplePagination.js/1.6/jquery.simplePagination.min.js', array('jquery'),'1.6', false);
      }

      function frontend_style(){
         wp_enqueue_style('style', ONLINE_QUIZ_PLUGIN_URL . '/assets/stylesheet.css');
         wp_enqueue_style('dashicons');
      }
   
      function activate(){
         flush_rewrite_rules();
      }
   
      function deactivate(){
         flush_rewrite_rules();
      }

   } 
}
$onlineQuizPlugin = new OnlineQuizPlugin();

//activate
register_activation_hook( __FILE__, array($onlineQuizPlugin, 'activate') );

//deactivate
register_deactivation_hook(__FILE__, array($onlineQuizPlugin, 'deactivate'));
