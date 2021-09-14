<?php

require get_theme_file_path('/includes/like-route.php');

require get_theme_file_path('/includes/search-route.php');

function university_custom_rest() {
    register_rest_field('post', 'author_name', array(
        'get_callback' => function() { return get_the_author(); }
    ));

    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function() { return count_user_posts(get_current_user_id()); }
    ));
}

add_action('rest_api_init', 'university_custom_rest');

function page_banner($args = NULL) {

    if (!$args['title']) {
        $args['title'] = get_the_title();
    }
    
    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }

    if (!$args['photo']) {
        if (get_field('page_banner_background_image') && !is_archive() && !is_home()) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['page-banner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
?>
<div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(<?php
        
        echo $args['photo'];
        
        ?>)"></div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title']; ?></h1>
            <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div>
<?php
}

function metadata() {
?>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<?php
}

// Add metadata in the website head
add_action('wp_head', 'metadata');

// Fictional University website stylesheet(s) & script(s)
function fictional_university_files() {
    // Google Map
    wp_enqueue_script('googleMap', '//maps.googleapis.com/maps/api/js?key=AIzaSyBgknsgMmmMCzY7vwQdsGkbg3Xpg7ed7qE', NULL, '1.0', true);
    // JavaScript
    wp_enqueue_script('main-university-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('search-js', get_theme_file_uri('/src/modules/search.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('like-js', get_theme_file_uri('/src/modules/like.js'), array('jquery'), '1.0', true);
    wp_enqueue_script('my-notes-js', get_theme_file_uri('/src/modules/my-notes.js'), array('jquery'), '1.0', true);
    // Google Font
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    // Font Awesome 4.7.0
    wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    // Main vendor stylesheet
    wp_enqueue_style('our-main-styles-vendor', get_theme_file_uri('/build/index.css'));
    // Main website CSS stylesheet
    wp_enqueue_style('fu-main-stylesheet', get_theme_file_uri('/build/style-index.css'));

    wp_localize_script('main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}

// Load Fictional University website stylesheet(s) & script(s)
add_action('wp_enqueue_scripts', 'fictional_university_files');

function university_features() {
    register_nav_menu('header-menu-one', 'Header Menu One');
    register_nav_menu('footer-menu-one', 'Footer Menu One');
    register_nav_menu('footer-menu-two', 'Footer Menu Two');
    // Enable Title tag support
    add_theme_support('title-tag');
    // Enable support for post thumbnail
    add_theme_support('post-thumbnails');
    // Create custom image sizes
    add_image_size('professor-landscape', 400, 260, true);
    add_image_size('professor-portrait', 480, 650, true);
    add_image_size('page-banner', 1500, 350, true);
}

add_action('after_setup_theme', 'university_features');

function university_adjust_queries($query) {
    if (!is_admin() && is_post_type_archive('campus') && $query->is_main_query()) {
        $query->set('posts_per_page', -1);
    }
    
    if (!is_admin() && is_post_type_archive('program') && $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
    
    if (!is_admin() && is_post_type_archive('event') && $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
                    array(
                    'key' => 'event_date',
                    'compare' => '>=',
                    'value' => $today,
                    'type' => 'numeric'
                    )
                ));
    }
}

add_action('pre_get_posts', 'university_adjust_queries');

function university_map_key($api) {
    $api['key'] = 'AIzaSyBgknsgMmmMCzY7vwQdsGkbg3Xpg7ed7qE';
    return $api;
}

add_filter('acf/fields/google_map/api', 'university_map_key');

// Remove admin bar for subscriber accounts
function remove_subscriber_admin_bar() {
    $current_user = wp_get_current_user();

    if (count($current_user->roles) == 1 && $current_user->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}

add_action('wp_loaded', 'remove_subscriber_admin_bar');

// Redirect subscriber accounts from dashboard to homepage
function redirect_subscriber_to_frontend() {
    $current_user = wp_get_current_user();

    if (count($current_user->roles) == 1 && $current_user->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}

add_action('admin_init', 'redirect_subscriber_to_frontend');

// Customise Log In screen
function custom_header_url() {
    return esc_url(site_url('/'));
}

add_filter('login_headerurl', 'custom_header_url');

function custom_login_css() {
    wp_enqueue_style('fu-main-stylesheet', get_theme_file_uri('/build/style-index.css'));
}

add_action('login_enqueue_scripts', 'custom_login_css');

function custom_login_header_title() {
    return get_bloginfo('name');
}

add_filter('login_headertitle', 'custom_login_header_title');

// Force Note posts to be private
function make_note_private($data, $postarr) {
    if ($data['post_type'] == 'note') {
        if (count_user_posts(get_current_user_id(), 'note') > 0 && !$postarr['ID']) {
            die('You have reached your note limit.');
        }

        $data['post_title'] = sanitize_text_field($data['post_title']);
        $data['post_content'] = sanitize_textarea_field($data['post_content']);
    }

    if ($data['post_type'] == 'note' && $data['post_status'] != 'trash') {
        $data['post_status'] = 'private';
    }
    
    return $data;
}

add_filter('wp_insert_post_data', 'make_note_private', 10, 2);

function ignore_certain_files($exclude_filters) {
    $exclude_filters[] = 'themes/fictional-university-theme/node_modules';
    return $exclude_filters;
}

add_filter('ai1wm_exclude_content_from_export', 'ignore_certain_files');

?>