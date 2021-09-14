<?php

function create_like($data) {
    if (is_user_logged_in()) {
        $professor = sanitize_text_field($data['professorId']);

        $exist_query = new WP_Query(array(
            'author' => get_current_user_id(),
            'post_type' => 'like',
            'meta_query' => array(
                array(
                    'key' => 'liked_professor_id',
                    'compare' => '=',
                    'value' => $professor
                )
            )
        ));

        if ($exist_query->found_posts == 0 && get_post_type($professor) == 'professor') {
            return wp_insert_post(array(
                'post_type' => 'like',
                'post_status' => 'publish',
                'post_title' => 'Test',
                'meta_input' => array(
                    'liked_professor_id' => $professor
                )
            ));
        } else {
            die('Invalid professor ID');
        }
    } else {
        die('Only logged in users can like.');
    }
}

function delete_like($data) {
    $like_id = sanitize_text_field($data['like']);
    if (get_current_user_id() == get_post_field('post_author', $like_id) && get_post_type($like_id) == 'like') {
        wp_delete_post($like_id, true);
        return 'Disliked.';
    } else {
        die('You do not have permission to dislike that.');
    }
}

function university_like_route() {
    register_rest_route('university/v1', 'manage-like', array(
        'methods' => 'POST',
        'callback' => 'create_like'
    ));

    register_rest_route('university/v1', 'manage-like', array(
        'methods' => 'DELETE',
        'callback' => 'delete_like'
    ));
}

add_action('rest_api_init', 'university_like_route');

?>