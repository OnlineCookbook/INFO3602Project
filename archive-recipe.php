<?php

get_header();

// Read filters from URL query string
$category   = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '';
$difficulty = isset($_GET['difficulty']) ? sanitize_text_field($_GET['difficulty']) : '';
$search     = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Pagination
$paged = get_query_var('paged') ? get_query_var('paged') : 1;

// Build query
$args = array(
  'post_type'      => 'recipe',
  'posts_per_page' => 6,
  'paged'          => $paged,
  's'              => $search
);

// Tax filter (only if you create recipe_category taxonomy)
if (!empty($category)) {
  $args['tax_query'] = array(
    array(
      'taxonomy' => 'recipe_category',
      'field'    => 'slug',
      'terms'    => $category
    )
  );
}

// Meta filter
if (!empty($difficulty)) {
  $args['meta_query'] = array(
    array(
      'key'     => 'difficulty',
      'value'   => $difficulty,
      'compare' => '='
    )
  );
}

$recipes = new WP_Query($args);
?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/recipes-banner.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Recipes</h1>
    <div class="page-banner__intro">
      <p>Browse recipes, filter by category, and find your next meal.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">

  <!-- Filter Form -->
  <form method="get" class="generic-content" style="margin-bottom: 25px;">
    <p>
      <label>Search:
        <input type="text" name="s" value="<?php echo esc_attr($search); ?>" placeholder="e.g. pasta, curry, cake">
      </label>
    </p>

    <p>
      <label>Category (slug):
        <input type="text" name="category" value="<?php echo esc_attr($category); ?>" placeholder="e.g. desserts">
      </label>
      <br>
      <small>Tip: once you create categories, use the slug (like “breakfast”, “desserts”).</small>
    </p>

    <p>
      <label>Difficulty:
        <select name="difficulty">
          <option value="">Any</option>
          <option value="Easy"   <?php selected($difficulty, 'Easy'); ?>>Easy</option>
          <option value="Medium" <?php selected($difficulty, 'Medium'); ?>>Medium</option>
          <option value="Hard"   <?php selected($difficulty, 'Hard'); ?>>Hard</option>
        </select>
      </label>
    </p>

    <p>
      <button class="btn btn--blue" type="submit">Apply Filters</button>
      <a class="btn btn--yellow" href="<?php echo get_post_type_archive_link('recipe'); ?>">Reset</a>
    </p>
  </form>

  <!-- Recipe Results -->
  <?php
  if ($recipes->have_posts()) {
    while ($recipes->have_posts()) {
      $recipes->the_post(); ?>

      <div class="post-item">
        <h2 class="headline headline--medium headline--post-title">
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <div class="metabox">
          <p>
            Prep: <strong><?php echo esc_html(get_post_meta(get_the_ID(), 'prep_time', true) ?: 'N/A'); ?></strong> |
            Difficulty: <strong><?php echo esc_html(get_post_meta(get_the_ID(), 'difficulty', true) ?: 'N/A'); ?></strong>
          </p>
        </div>

        <div class="generic-content">
          <?php the_excerpt(); ?>
          <p><a class="btn btn--blue" href="<?php the_permalink(); ?>">View Recipe &raquo;</a></p>
        </div>
      </div>

    <?php }
    // Pagination
    echo paginate_links(array(
      'total' => $recipes->max_num_pages
    ));
    wp_reset_postdata();
  } else {
    echo "<p>No recipes found. Try a different filter.</p>";
  }
  ?>

</div>

<?php get_footer(); ?>