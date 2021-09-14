<?php

/*
Plugin Name: Fictional University Plugin
Description: The first plugin for <a href="../">Fictional University</a>: university programs counter.
Version: 1.0
Author: Fictional University
*/

function content_edits($content) {
    $content = $content. '<p>All content belong to Fictional University.</p>';
    $content = str_replace('Lorem', 'L***m', $content);
    return $content;
}

add_filter('the_content', 'content_edits');

function program_count_function() {
    $programs = new WP_Query(array(
        'post_type' => 'program'
    ));

    return $programs->found_posts;
}

add_shortcode('program_count', 'program_count_function');

?>