<?php

function university_register_search() {
    register_rest_route('university/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'university_search_results'
    ));
}

function university_search_results($data) {
    $main_query = new WP_Query(array(
        'post_type' => array('post', 'page', 'professor', 'program', 'campus', 'event'),
        'posts_per_page' => -1,
        's' => sanitize_text_field($data['term'])
    ));

    $results = array(
        'general_info' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array(),
        'campuses' => array()
    );

    while ($main_query->have_posts()) {
        $main_query->the_post();

        // General Info
        if (get_post_type() == 'post' || get_post_type() == 'page') {
            array_push($results['general_info'], array(
                'post_type' => get_post_type(),
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'author_name' => get_the_author()
            ));
        }

        // Professors
        if (get_post_type() == 'professor') {
            array_push($results['professors'], array(
                'name' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professor-landscape')
            ));
        }

        // Programs
        if (get_post_type() == 'program') {
            $related_campuses = get_field('related_campus');
            
            if ($related_campuses) {
                foreach ($related_campuses as $campus) {
                    array_push($results['campuses'], array(
                        'title' => get_the_title($campus),
                        'permalink' => get_the_permalink($campus)
                    ));
                }
            }
            
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_ID()
            ));
        }

        // Campuses
        if (get_post_type() == 'campus') {
            array_push($results['campuses'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink()
            ));
        }

        // Events
        if (get_post_type() == 'event') {
            $event_date = new DateTime(get_field('event_date'));

            $description;

            if (has_excerpt()) $description = get_the_excerpt(); else $description = wp_trim_words(get_the_content(), 10);

            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'year' => $event_date->format('Y'),
                'month' => $event_date->format('M'),
                'day' => $event_date->format('d'),
                'description' => $description
            ));
        }
    }

    if ($results['programs']) {
        $programs_meta_query = array('relation' => 'OR');

        foreach ($results['programs'] as $item) {
            array_push($programs_meta_query, array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"'. $item['id'] .'"'
            ));
        }

        $program_relationship_query = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $programs_meta_query
        ));

        while ($program_relationship_query->have_posts()) {
            $program_relationship_query->the_post();

            if (get_post_type() == 'professor') {
                array_push($results['professors'], array(
                    'name' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professor-landscape')
                ));
            }

            if (get_post_type() == 'event') {
                $event_date = new DateTime(get_field('event_date'));
    
                $description;
    
                if (has_excerpt()) $description = get_the_excerpt(); else $description = wp_trim_words(get_the_content(), 10);
    
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'year' => $event_date->format('Y'),
                    'month' => $event_date->format('M'),
                    'day' => $event_date->format('d'),
                    'description' => $description
                ));
            }
        }

        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

    return $results;
}

add_action('rest_api_init', 'university_register_search');

?>