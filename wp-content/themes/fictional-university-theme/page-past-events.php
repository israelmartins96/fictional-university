<?php

    get_header();

    page_banner(array(
        'title' => 'Past Events',
        'subtitle' => 'View a recap of our past events.'
    ));
?>

    <div class="container container--narrow page-section">
        <?php

            $today = date('Ymd');

            $past_events = new WP_Query(array(
                'post_type' => 'event',
                'paged' => get_query_var('paged', 1),
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'meta_key' => 'event_date',
                'meta_query' => array(
                    array(
                    'key' => 'event_date',
                    'compare' => '<',
                    'value' => $today,
                    'type' => 'numeric'
                    )
                )
            ));
        
            while ($past_events->have_posts()) {
                $past_events->the_post();
                get_template_part('template-parts/content', get_post_type());
            }

            echo paginate_links(array(
                'total' =>$past_events->max_num_pages
            ));
            
        ?>
    </div>

<?php

    get_footer();

?>