<?php
/**
 * single-pairing.php
 * Single pairing view — two recipe cards side by side with description.
 */

get_header();
the_post();

$recipe_a_id  = get_post_meta(get_the_ID(), 'pairing_recipe_a', true);
$recipe_b_id  = get_post_meta(get_the_ID(), 'pairing_recipe_b', true);
$description  = get_post_meta(get_the_ID(), 'pairing_description', true);

$recipe_a = $recipe_a_id ? get_post($recipe_a_id) : null;
$recipe_b = $recipe_b_id ? get_post($recipe_b_id) : null;
?>


<div class="page-banner">
    <div class="page-banner__bg-image"
         style="background-image: url(<?php echo get_theme_file_uri('images/recipes-banner.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
        <h1 class="page-banner__title"><?php the_title(); ?></h1>
        <div class="page-banner__intro">
            <p>A community recipe pairing.</p>
        </div>
    </div>
</div>

<div class="container container--narrow page-section">

    <div class="metabox metabox--position-up metabox--with-home-link" style="margin-bottom:28px;">
        <p>
            <a class="metabox__blog-home-link"
               href="<?php echo get_post_type_archive_link('pairing'); ?>"> All Pairings</a>
            
        </p>
    </div>


    <div class="pairing-hero">

        <?php if ($recipe_a) : ?>
        <a href="<?php echo esc_url(get_permalink($recipe_a->ID)); ?>" class="pairing-recipe-card">
            <div class="pairing-recipe-card__image">
                <?php if (has_post_thumbnail($recipe_a->ID)) : ?>
                    <?php echo get_the_post_thumbnail($recipe_a->ID, 'medium_large'); ?>
                <?php else : ?>
                    <div class="pairing-recipe-card__no-image">🍽</div>
                <?php endif; ?>
            </div>
            <div class="pairing-recipe-card__body">
                <h2 class="pairing-recipe-card__title"><?php echo esc_html($recipe_a->post_title); ?></h2>
                <?php
                $cats_a = get_the_terms($recipe_a->ID, 'recipe_category');
                if (!empty($cats_a) && !is_wp_error($cats_a)) :
                ?>
                <p class="pairing-recipe-card__meta">
                    <?php echo esc_html(implode(', ', wp_list_pluck($cats_a, 'name'))); ?>
                </p>
                <?php endif; ?>
                <?php
                $prep_a = get_post_meta($recipe_a->ID, 'prep_time', true);
                $diff_a = get_post_meta($recipe_a->ID, 'difficulty', true);
                if ($prep_a || $diff_a) : ?>
                <p class="pairing-recipe-card__meta">
                    <?php if ($prep_a) echo 'Prep: <strong>' . esc_html($prep_a) . '</strong>'; ?>
                    <?php if ($prep_a && $diff_a) echo ' &nbsp;|&nbsp; '; ?>
                    <?php if ($diff_a) echo 'Difficulty: <strong>' . esc_html($diff_a) . '</strong>'; ?>
                </p>
                <?php endif; ?>
                <div class="pairing-recipe-card__view">
                    <span class="btn btn--blue" style="font-size:13px; padding:8px 16px;">View Recipe →</span>
                </div>
            </div>
        </a>
        <?php else : ?>
        <div class="pairing-recipe-card">
            <div class="pairing-recipe-card__no-image"></div>
            <div class="pairing-recipe-card__body">
                <h2 class="pairing-recipe-card__title">Recipe not found</h2>
            </div>
        </div>
        <?php endif; ?>

        <!-- Connector -->
        <div class="pairing-connector">
            <div class="pairing-connector__line"></div>
            <div class="pairing-connector__plus">+</div>
            <div class="pairing-connector__line"></div>
        </div>

        <?php if ($recipe_b) : ?>
        <a href="<?php echo esc_url(get_permalink($recipe_b->ID)); ?>" class="pairing-recipe-card">
            <div class="pairing-recipe-card__image">
                <?php if (has_post_thumbnail($recipe_b->ID)) : ?>
                    <?php echo get_the_post_thumbnail($recipe_b->ID, 'medium_large'); ?>
                <?php else : ?>
                    <div class="pairing-recipe-card__no-image">🍽</div>
                <?php endif; ?>
            </div>
            <div class="pairing-recipe-card__body">
                <h2 class="pairing-recipe-card__title"><?php echo esc_html($recipe_b->post_title); ?></h2>
                <?php
                $cats_b = get_the_terms($recipe_b->ID, 'recipe_category');
                if (!empty($cats_b) && !is_wp_error($cats_b)) :
                ?>
                <p class="pairing-recipe-card__meta">
                    <?php echo esc_html(implode(', ', wp_list_pluck($cats_b, 'name'))); ?>
                </p>
                <?php endif; ?>
                <?php
                $prep_b = get_post_meta($recipe_b->ID, 'prep_time', true);
                $diff_b = get_post_meta($recipe_b->ID, 'difficulty', true);
                if ($prep_b || $diff_b) : ?>
                <p class="pairing-recipe-card__meta">
                    <?php if ($prep_b) echo 'Prep: <strong>' . esc_html($prep_b) . '</strong>'; ?>
                    <?php if ($prep_b && $diff_b) echo ' &nbsp;|&nbsp; '; ?>
                    <?php if ($diff_b) echo 'Difficulty: <strong>' . esc_html($diff_b) . '</strong>'; ?>
                </p>
                <?php endif; ?>
                <div class="pairing-recipe-card__view">
                    <span class="btn btn--blue" style="font-size:13px; padding:8px 16px;">View Recipe</span>
                </div>
            </div>
        </a>
        <?php else : ?>
        <div class="pairing-recipe-card">
            <div class="pairing-recipe-card__no-image"></div>
            <div class="pairing-recipe-card__body">
                <h2 class="pairing-recipe-card__title">Recipe not found</h2>
            </div>
        </div>
        <?php endif; ?>

    </div><!-- /.pairing-hero -->

    <!-- Pairing description -->
    <?php if ($description) : ?>
    <div class="pairing-description-box">
        <h2>Why they pair well</h2>
        <p><?php echo nl2br(esc_html($description)); ?></p>
    </div>
    <?php endif; ?>

    <!-- Comments -->
    <?php if (comments_open() || get_comments_number()) : ?>
        <div style="margin-top:40px;">
            <h3 style="margin-bottom:16px;">Have a thought on this pairing?</h3>
            <?php comments_template(); ?>
        </div>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
