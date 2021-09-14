<div class="post-item">
            <h2 class="headline headline--medium headline--post-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <div class="generic-content">
                <p><?php if (has_excerpt()) echo get_the_excerpt(); else echo wp_trim_words(get_the_content(), 10); ?></p>
                <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">View Campus &raquo;</a></p>
            </div>
        </div>