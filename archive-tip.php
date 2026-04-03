<?php


get_header();

$paged = get_query_var('paged') ? get_query_var('paged') : 1;
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$tip_category = isset($_GET['tip_category']) ? sanitize_text_field($_GET['tip_category']) : '';

$args = array(
    'post_type' => 'tip',
    'posts_per_page' => 12,
    'paged' => $paged,
    's' => $search,
);

if (!empty($tip_category)) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'tip_category',
            'field' => 'slug',
            'terms' => $tip_category,
        ),
    );
}

$tips = new WP_Query($args);
?>

<div class="page-banner">
    <div class="page-banner__bg-image"
        style="background-image: url(<?php echo get_theme_file_uri('images/tips-banner.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title">Tips &amp; Tricks</h1>
        <div class="page-banner__intro">
            <p>The best hacks from the community! Storage hacks, flavour secrets, and cooking techniques.</p>
        </div>
    </div>
</div>

<div class="container page-section">

    <form method="get" class="tips-filter-form">
        <div class="tips-filter-inner">
            <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Search tips…">
            <input type="text" name="tip_category" value="<?php echo esc_attr($tip_category); ?>" placeholder="Category slug…">
            <button class="btn btn--blue" type="submit">Search</button>
            <a class="btn btn--yellow" href="<?php echo get_post_type_archive_link('tip'); ?>">Reset</a>
        </div>
    </form>

    <?php if ($tips->have_posts()): ?>

        <div class="tips-grid">

            <?php while ($tips->have_posts()):
                $tips->the_post();
                $tip_number = get_post_meta(get_the_ID(), 'tip_number', true);
                $tip_tag = get_post_meta(get_the_ID(), 'tip_tag', true);
                $youtube_url = get_post_meta(get_the_ID(), 'youtube_url', true);
                $cats = get_the_terms(get_the_ID(), 'tip_category');
                ?>

                <a href="<?php the_permalink(); ?>" class="tip-card">


                    <!-- Youtube video url thumbnail -->
                    <div class="tip-card__image">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('medium_large'); ?>
                        <?php elseif ($youtube_url):
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $youtube_url, $matches);
                            $video_id = $matches[1] ?? '';
                            if ($video_id): ?>
                                <img src="https://img.youtube.com/vi/<?php echo esc_attr($video_id); ?>/maxresdefault.jpg" alt="<?php the_title_attribute(); ?>">
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="tip-card__no-image">No Image</div>
                        <?php endif; ?>


                        <div class="tip-card__badges">
                            <?php if ($tip_number): ?>
                                <span class="tip-badge tip-badge--number"><?php echo esc_html($tip_number); ?> Tips</span>
                            <?php endif; ?>
                            <?php if ($tip_tag): ?>
                                <span class="tip-badge tip-badge--tag"><?php echo esc_html($tip_tag); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="tip-card__body">
                        <h2 class="tip-card__title"><?php the_title(); ?></h2>
                        <p class="tip-card__meta">
                            <?php
                            if (!empty($cats) && !is_wp_error($cats)) {
                                echo esc_html(implode(', ', wp_list_pluck($cats, 'name')));
                            } else {
                                echo 'Tips &amp; Tricks';
                            }
                            ?>
                            &mdash; <?php echo get_the_date(); ?>
                        </p>
                    </div>

                </a>

            <?php endwhile; ?>

        </div>

        <div style="margin-top:24px;">
            <?php echo paginate_links(array('total' => $tips->max_num_pages)); ?>
        </div>

        <?php wp_reset_postdata(); ?>

    <?php else: ?>
        <p>No tips found. Try a different search.</p>
    <?php endif; ?>

</div>

<?php get_footer(); ?>