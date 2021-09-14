<?php

    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url());
        exit;
    }

    get_header();

    while (have_posts()) {
        the_post();

        page_banner();
?>

    <div class="container container--narrow page-section">
        <div class="create-note">
            <h2 class="headline headline--medium">Create New Note</h2>
            <input type="text" class="new-note-title" placeholder="Title">
            <textarea name="" id="" cols="30" rows="10" class="new-note-body" placeholder="Your note here"></textarea>
            <button class="submit-note">Create Note</button><span class="note-limit-message">Note limit reached. Delete an existing note to create a new one.</span>
        </div>
        <ul class="min-list link-list" id="my-notes">
        <?php
            $user_notes = new WP_Query(array(
                'post_type' => 'note',
                'posts_per_page' => -1,
                'author' => get_current_user_id()
            ));

            while ($user_notes->have_posts()) {
                $user_notes->the_post();
        ?>
            <li data-id="<?php the_id(); ?>">
                <input readonly class="note-title-field" value="<?php echo str_replace('Private: ', '', esc_attr(get_the_title())); ?>">
                <button class="edit-note"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</button>
                <button class="delete-note"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                <textarea readonly class="note-body-field"><?php echo esc_textarea(wp_strip_all_tags(get_the_content())); ?></textarea>
                <button class="update-note btn btn--blue btn--small"><i class="fa fa-arrow-right" aria-hidden="true"></i> Save</button>
            </li>
        <?php } ?>
        </ul>
    </div>

<?php

    }

    get_footer();

?>