<?php

global $wpdb;

//remove all custom post types created by the plugin from the database
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'quiz'");
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'matching_question'");
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'ordering_question'");
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'mc_multiple_question'");
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'mc_single_question'");
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'sa_question'");

//remove all post meta associated with custom post types above
$wpdb->query("DELETE wp_postmeta FROM wp_postmeta
LEFT JOIN wp_posts ON wp_posts.ID = wp_postmeta.post_id
WHERE wp_posts.ID IS NULL");