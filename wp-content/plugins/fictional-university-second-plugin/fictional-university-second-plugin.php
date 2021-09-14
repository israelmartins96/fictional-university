<?php

/*
Plugin Name: Fictional University Second Plugin
Description: The second plugin for <a href="../">Fictional University</a>: custom block type for border styling.
Version: 1.0
Author: Fictional University
*/

function load_custom_block_files() {
    wp_enqueue_script(
        'custom_handle',
        plugin_dir_url(__FILE__).'custom-block.js',
        array('wp-blocks', 'wp-i18n', 'wp-editor'),
        true
    );
}

add_action('enqueue_block_editor_assets', 'load_custom_block_files');

?>