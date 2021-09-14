<?php

    get_header();

    while (have_posts()) {
        the_post();

        page_banner();
?>

    <div class="container container--narrow page-section">
        <?php
            
            $parent_page_ID = wp_get_post_parent_id(get_the_ID());

            if ($parent_page_ID) {
        ?>
        <div class="metabox metabox--position-up metabox--with-home-link">
            <p>
                <a class="metabox__blog-home-link" href="<?php echo get_permalink($parent_page_ID); ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo get_the_title($parent_page_ID); ?></a> <span class="metabox__main"><?php the_title(); ?></span>
            </p>
        </div>
        <?php } ?>

        <?php
            
            $on_child_page = $parent_page_ID;
            $on_parent_page = get_pages(array(
                'child_of' => get_the_ID()
            ));

            if ($on_child_page || $on_parent_page) {
        ?>
        <div class="page-links">
            <h2 class="page-links__title"><a href="<?php echo get_permalink($parent_page_ID); ?>"><?php echo get_the_title($parent_page_ID); ?></a></h2>
            <ul class="min-list">
                <?php

                    if ($parent_page_ID) {
                        // Use parent page ID when it is a child page
                        $parent_page = $parent_page_ID;
                    } else {
                        // Use current page ID when it is not a child page
                        $parent_page = get_the_ID();
                    }
                    wp_list_pages(array(
                        'title_li' => NULL,
                        'child_of' => $parent_page,
                        'sort_column' => 'menu_order'
                    ));
                
                ?>
            </ul>
        </div>
        <?php } ?>

        <div class="generic-content">
            <?php the_content(); ?>
        </div>
    </div>

<?php

    }

    get_footer();

?>