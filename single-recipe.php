<?php get_header(); ?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/recipe-banner.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title"><?php the_title(); ?></h1>
    <div class="page-banner__intro">
      <p>A delicious recipe shared by our community.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">

  <div class="metabox metabox--position-up metabox--with-home-link">
    <p>
      <a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('recipe'); ?>">
        <i class="fa fa-home" aria-hidden="true"></i> All Recipes
      </a>
      <span class="metabox__main">
        <?php
        $categories = get_the_terms(get_the_ID(), 'recipe_category');

        // Fallback to standard category if recipe_category unexpectedly missing
        if (empty($categories) || is_wp_error($categories)) {
          $categories = get_the_terms(get_the_ID(), 'category');
        }

        if (!empty($categories) && !is_wp_error($categories)) {
          $cat_links = array();
          foreach ($categories as $cat) {
            $cat_links[] = '<a href="' . esc_url(get_term_link($cat)) . '">' . esc_html($cat->name) . '</a>';
          }
          echo 'Posted in ' . implode(', ', $cat_links);
        } else {
          echo 'Uncategorized Recipe';
        }
        ?>
      </span>
    </p>
  </div>

  <div class="generic-content">
    <?php if (has_post_thumbnail()) : ?>
      <div style="text-align: center; margin-bottom: 20px;">
        <?php the_post_thumbnail('large'); ?>
      </div>
    <?php endif; ?>

    <div class="metabox" style="margin-bottom: 20px;">
      <p>
        Prep Time: <strong><?php echo esc_html(get_post_meta(get_the_ID(), 'prep_time', true) ?: 'N/A'); ?></strong> |
        Difficulty: <strong><?php echo esc_html(get_post_meta(get_the_ID(), 'difficulty', true) ?: 'N/A'); ?></strong>
      </p>
    </div>

    <h3>Ingredients</h3>
    <div style="margin-bottom: 30px;">
      <?php
      $ingredients = get_post_meta(get_the_ID(), 'ingredients', true);
      if ($ingredients) {
        echo wpautop(esc_html($ingredients));
      } else {
        echo '<p>No ingredients listed.</p>';
      }
      ?>
    </div>

    <h3>Instructions</h3>
    <div>
      <?php the_content(); ?>
    </div>
  </div>

  <?php
  // Comments
  if (comments_open() || get_comments_number()) {
    comments_template();
  }
  ?>

</div>

<?php get_footer(); ?>
<parameter name="filePath">c:\Users\jrond\Local Sites\online-cookbook\app\public\wp-content\themes\cookbook\single-recipe.php