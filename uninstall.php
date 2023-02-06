<?php

global $wpdb;
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'quiz'");
$wpdb->query("DELETE FROM wp_posts WHERE post_type = 'matching_question'");