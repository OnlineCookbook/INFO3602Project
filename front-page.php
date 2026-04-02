<?php
/**
 * front-page.php
 * Home Page – Featured recipes, trending dishes, navigation links
 */
get_header();
?>

<div class="page-banner">
  <div class="page-banner__bg-image"
       style="background-image: url(<?php echo get_theme_file_uri('images/hero-cookbook.jpg'); ?>);"></div>

  <div class="page-banner__content container t-center c-white">
    <h1 class="headline headline--large">OnlineCookbook</h1>
    <h2 class="headline headline--medium">Discover, cook, and share your favourite recipes.</h2>
    <h3 class="headline headline--small">Browse by <strong>category</strong> or submit your own.</h3>
    <a href="<?php echo site_url('/recipes'); ?>" class="btn btn--large btn--blue">Browse Recipes</a>
    <a href="<?php echo site_url('/submit-a-recipe'); ?>" class="btn btn--large btn--yellow">Submit a Recipe</a>
  </div>
</div>

<div class="full-width-split group">

  <!-- Featured Recipes -->
  <div class="full-width-split__one">
    <div class="full-width-split__inner">
      <h2 class="headline headline--small-plus t-center">Featured Recipes</h2>

      <?php
      $featuredRecipes = new WP_Query(array(
        'posts_per_page' => 2,
        'post_type'      => 'recipe',
        'tax_query'      => array(
          array(
            'taxonomy' => 'recipe_category',
            'field'    => 'slug',
            'terms'    => array('featured'),
          ),
        ),
      ));

      if ($featuredRecipes->have_posts()) {
        while ($featuredRecipes->have_posts()) {
          $featuredRecipes->the_post(); ?>

          <div class="event-summary">
            <a class="event-summary__date event-summary__date--beige t-center"
               href="<?php the_permalink(); ?>">
              <span class="event-summary__month"><?php echo esc_html(get_post_meta(get_the_ID(), 'prep_time', true) ?: 'Prep'); ?></span>
              <span class="event-summary__day"><?php echo esc_html(get_post_meta(get_the_ID(), 'difficulty', true) ?: 'Easy'); ?></span>
            </a>

            <div class="event-summary__content">
              <h5 class="event-summary__title headline headline--tiny">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h5>

              <p>
                <?php echo wp_trim_words(get_the_content(), 18); ?>
                <a href="<?php the_permalink(); ?>" class="nu gray">View recipe</a>
              </p>
            </div>
          </div>

        <?php }
        wp_reset_postdata();
      } else { ?>
        <p class="t-center">No featured recipes yet. Add a tag “featured” to a recipe.</p>
      <?php } ?>

      <p class="t-center no-margin">
        <a href="<?php echo get_post_type_archive_link('recipe'); ?>" class="btn btn--blue">View All Recipes</a>
      </p>
    </div>
  </div>

  <!-- Trending Recipes -->
  <div class="full-width-split__two">
    <div class="full-width-split__inner">
      <h2 class="headline headline--small-plus t-center">Trending Dishes</h2>

      <?php
      // Trending: uses a custom field 'views' (number). If you don't have it yet, this will still work,
      // but results may look random until you add views.
      $trendingRecipes = new WP_Query(array(
        'posts_per_page' => 2,
        'post_type'      => 'recipe',
        'meta_key'       => 'views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC'
      ));

      if ($trendingRecipes->have_posts()) {
        while ($trendingRecipes->have_posts()) {
          $trendingRecipes->the_post(); ?>

          <div class="event-summary">
            <a class="event-summary__date t-center" href="<?php the_permalink(); ?>">
              <span class="event-summary__month">Views</span>
              <span class="event-summary__day"><?php echo esc_html(get_post_meta(get_the_ID(), 'views', true) ?: 0); ?></span>
            </a>

            <div class="event-summary__content">
              <h5 class="event-summary__title headline headline--tiny">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
              </h5>
              <p>
                <?php echo wp_trim_words(get_the_content(), 18); ?>
                <a href="<?php the_permalink(); ?>" class="nu gray">Cook this</a>
              </p>
            </div>
          </div>

        <?php }
        wp_reset_postdata();
      } else { ?>
        <p class="t-center">No trending recipes yet.</p>
      <?php } ?>

      <p class="t-center no-margin">
        <a href="<?php echo site_url('/recipes'); ?>" class="btn btn--yellow">Explore Categories</a>
      </p>
    </div>
  </div>

</div>

<?php get_footer(); ?>