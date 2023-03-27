<?php
    //function used to calculate points 
    function calculatePoints(&$userScore, $pointWeight, $countCorrect, $correct){
        if($pointWeight == 0){
            return;
        }
        $pointsAwarded = ($correct/(float)$countCorrect) * $pointWeight;
        $pointsAwarded = round($pointsAwarded, 2);
        $userScore += $pointsAwarded;
        return $pointsAwarded;
    
    }

    //function used to create cpt capabilities
    function create_post_type_capabilities($singular = 'post', $plural = 'posts') {
        return [
            'edit_post'		 => "edit_$singular",
            'read_post'		 => "read_$singular",
            'delete_post'		 => "delete_$singular",
            'edit_posts'		 => "edit_$plural",
            'edit_others_posts'	 => "edit_others_$plural",
            'publish_posts'		 => "publish_$plural",
            'read_private_posts'	 => "read_private_$plural",
            'read'                   => "read",
            'delete_posts'           => "delete_$plural",
            'delete_private_posts'   => "delete_private_$plural",
            'delete_published_posts' => "delete_published_$plural",
            'delete_others_posts'    => "delete_others_$plural",
            'edit_private_posts'     => "edit_private_$plural",
            'edit_published_posts'   => "edit_published_$plural",
            'create_posts'           => "edit_$plural",
        ];
    }

    //function used to create Sensei capabilities
    function create_sensei_capabilities($singular = 'post', $plural = 'posts') {
        return [
            'edit_post'		 => "edit_$singular",
            'read_post'		 => "read_$singular",
            'delete_post'		 => "delete_$singular",
            'edit_posts'		 => "edit_$plural",
            'publish_posts'		 => "publish_$plural",
            'read_private_posts'	 => "read_private_$plural",
            'read'                   => "read",
            'delete_posts'           => "delete_$plural",
            'delete_private_posts'   => "delete_private_$plural",
            'delete_published_posts' => "delete_published_$plural",
            'edit_private_posts'     => "edit_private_$plural",
            'edit_published_posts'   => "edit_published_$plural",
            'create_posts'           => "edit_$plural",
        ];
    }
?>