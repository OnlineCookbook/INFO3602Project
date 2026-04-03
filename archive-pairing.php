<?php


get_header();

$paged  = get_query_var('paged') ? get_query_var('paged') : 1;
$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';




$args = array(
    'post_type'      => 'pairing',
    'posts_per_page' => 12,
    'paged'          => $paged,
    's'              => $search,
    'post_status'    => 'publish',
);

$pairings = new WP_Query($args);
?>


<div class="page-banner">
    <div class="page-banner__bg-image"
         style="background-image: url(<?php echo get_theme_file_uri('images/recipes-banner.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title">Recipe Pairings</h1>
        <div class="page-banner__intro">
            <p>Two recipes that our community thinks just belong together.</p>
        </div>
    </div>
</div>

<div class="container page-section">


    <form method="get" class="pairings-filter-form">
        <div class="pairings-filter-inner">
            <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="Search pairings…">
            <button class="btn btn--blue" type="submit">Search</button>
            <a class="btn btn--yellow" href="<?php echo get_post_type_archive_link('pairing'); ?>">Reset</a>
            <a class="btn btn--blue" href="<?php echo esc_url(site_url('/submit-a-pairing')); ?>" style="margin-left:auto;">+ Submit a Pairing</a>
        </div>
    </form>

    <?php if ($pairings->have_posts()) : ?>

        <div class="pairings-grid">

        <?php while ($pairings->have_posts()) : $pairings->the_post();

            $recipe_a_id = get_post_meta(get_the_ID(), 'pairing_recipe_a', true);
            $recipe_b_id = get_post_meta(get_the_ID(), 'pairing_recipe_b', true);
            $description = get_post_meta(get_the_ID(), 'pairing_description', true);

            $recipe_a = $recipe_a_id ? get_post($recipe_a_id) : null;
            $recipe_b = $recipe_b_id ? get_post($recipe_b_id) : null;

            $title_a = $recipe_a ? $recipe_a->post_title : 'Recipe A';
            $title_b = $recipe_b ? $recipe_b->post_title : 'Recipe B';
        ?>

            <a href="<?php the_permalink(); ?>" class="pairing-card">

                <!-- Dual thumbnails -->
                <div class="pairing-card__images">

                    <div class="pairing-card__img-wrap">
                        <?php if ($recipe_a && has_post_thumbnail($recipe_a->ID)) : ?>
                            <?php echo get_the_post_thumbnail($recipe_a->ID, 'medium'); ?>
                        <?php else : ?>
                            <div class="pairing-card__img-placeholder">🍽</div>
                        <?php endif; ?>
                    </div>

                    <div class="pairing-card__divider">
                        <div class="pairing-card__divider-circle">+</div>
                    </div>

                    <div class="pairing-card__img-wrap">
                        <?php if ($recipe_b && has_post_thumbnail($recipe_b->ID)) : ?>
                            <?php echo get_the_post_thumbnail($recipe_b->ID, 'medium'); ?>
                        <?php else : ?>
                            <div class="pairing-card__img-placeholder">🍽</div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Card body -->
                <div class="pairing-card__body">
                    <p class="pairing-card__label">
                       Pairing
                    </p>
                    <h2 class="pairing-card__title">
                        <?php echo esc_html($title_a); ?>
                        &nbsp;+&nbsp;
                        <?php echo esc_html($title_b); ?>
                    </h2>
                    <?php if ($description) : ?>
                    <p class="pairing-card__desc"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>
                    <p class="pairing-card__meta">
                        By <strong><?php the_author(); ?></strong>
                        &mdash; <?php echo get_the_date(); ?>
                    </p>
                </div>

            </a>

        <?php endwhile; ?>

        </div><!-- /.pairings-grid -->

        <div style="margin-top:28px;">
            <?php echo paginate_links(array('total' => $pairings->max_num_pages)); ?>
        </div>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>

        <div style="text-align:center; padding:60px 0;">
            <p style="font-size:48px; margin-bottom:12px;">🤝</p>
            <p style="font-size:18px; font-weight:700; margin-bottom:8px;">No pairings yet</p>
            <p style="color:#6b7280; margin-bottom:24px;">Make a pairing</p>
            <a href="<?php echo esc_url(site_url('/submit-a-pairing')); ?>" class="btn btn--blue">Submit a Pairing</a>
        </div>

    <?php endif; ?>

</div>

<?php get_footer(); ?>
