<form class="search-form" action="<?php echo esc_url(site_url('/')); ?>" method="get">
                <label class="headline headline--medium" for="search-field">Looking for something?</label>
                <div class="search-form-row">
                    <?php if (get_post_type() == 'page') { ?>
                    <input class="s" type="search" name="s" id="search-field" placeholder="Search here" autofocus>
                    <?php
                    } else {
                    ?>
                    <input class="s" type="search" name="s" id="search-field" placeholder="Search here">
                    <?php } ?>
                    <button class="search-submit" type="submit">Search</button>
                </div>
            </form>