<?php

    get_header();

    while (have_posts()) {
        the_post();

        page_banner();
?>

    <div class="container container--narrow page-section">
        <div class="generic-content">
            <div class="row group">
                <div class="one-third">
                    <?php the_post_thumbnail('professor-portrait-half'); ?>
                </div>
                <div class="two-third">
                    <?php

                        $like_count = new WP_Query(array(
                            'post_type' => 'like',
                            'meta_query' => array(
                                array(
                                    'key' => 'liked_professor_id',
                                    'compare' => '=',
                                    'value' => get_the_ID()
                                )
                            )
                        ));
                        
                        $exist_status = 'no';

                        if (is_user_logged_in()) {
                            $exist_query = new WP_Query(array(
                                'author' => get_current_user_id(),
                                'post_type' => 'like',
                                'meta_query' => array(
                                    array(
                                        'key' => 'liked_professor_id',
                                        'compare' => '=',
                                        'value' => get_the_ID()
                                    )
                                )
                            ));
    
                            if ($exist_query->found_posts) {
                                $exist_status = 'yes';
                            }
                        }

                    ?>

                    <button class="like-box" data-like="<?php echo $exist_query->posts[0]->ID; ?>" data-professor="<?php the_ID(); ?>" data-exists="<?php echo $exist_status; ?>">
                        <i class="fa fa-heart-o" aria-hidden="true"></i>
                        <i class="fa fa-heart" aria-hidden="true"></i>
                        <span class="like-count"><?php echo $like_count->found_posts; ?></span>
                    </button>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>
        
        <?php

            $related_programs = get_field('related_programs');

            if ($related_programs) {
                echo '<hr class="section-break">';
                echo '<h2 class="headline headline--medium">Subject(s) Taught</h2>';
                echo '<ul class="link-list min-list">';
                foreach ($related_programs as $program) {
        ?>

        <li><a href="<?php echo get_the_permalink($program); ?>"><?php echo get_the_title($program); ?></a></li>
        
        <?php
                }
                echo '</ul>';
            }
        
        ?>
        
    </div>

<?php

    }

    get_footer();

?>