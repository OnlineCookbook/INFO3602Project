<?php

get_header();

if (!is_user_logged_in()) { ?>

  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/submit-banner.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title">Submit a Recipe</h1>
      <div class="page-banner__intro">
        <p>You must be logged in to submit a recipe.</p>
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">
    <p><a class="btn btn--blue" href="<?php echo wp_login_url(get_permalink()); ?>">Log in</a></p>
    <p><a class="btn btn--yellow" href="<?php echo wp_registration_url(); ?>">Register</a></p>
  </div>

<?php
  get_footer();
  exit;
}

// Handle form submission
$success_message = '';
$error_message   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['oc_submit_recipe'])) {

  // Basic nonce security
  if (!isset($_POST['oc_nonce']) || !wp_verify_nonce($_POST['oc_nonce'], 'oc_submit_recipe')) {
    $error_message = 'Security check failed. Please try again.';
  } else {

    $title        = sanitize_text_field($_POST['recipe_title'] ?? '');
    $prep_time    = sanitize_text_field($_POST['prep_time'] ?? '');
    $difficulty   = sanitize_text_field($_POST['difficulty'] ?? '');
    $ingredients  = wp_kses_post($_POST['ingredients'] ?? '');
    $instructions = wp_kses_post($_POST['instructions'] ?? '');

    if (empty($title) || empty($ingredients) || empty($instructions)) {
      $error_message = 'Please fill in the title, ingredients, and instructions.';
    } else {

      // Create recipe post (set to draft so an editor/admin can approve)
      $post_id = wp_insert_post(array(
        'post_type'   => 'recipe',
        'post_title'  => $title,
        'post_status' => 'draft',
        'post_content'=> $instructions,
        'post_author' => get_current_user_id()
      ));

      if (is_wp_error($post_id)) {
        $error_message = 'Could not create recipe. Please try again.';
      } else {

        // Save meta fields
        update_post_meta($post_id, 'prep_time', $prep_time);
        update_post_meta($post_id, 'difficulty', $difficulty);
        update_post_meta($post_id, 'ingredients', $ingredients);

        // Handle image upload (featured image)
        if (!empty($_FILES['recipe_image']['name'])) {
          require_once(ABSPATH . 'wp-admin/includes/file.php');
          require_once(ABSPATH . 'wp-admin/includes/media.php');
          require_once(ABSPATH . 'wp-admin/includes/image.php');

          $attachment_id = media_handle_upload('recipe_image', $post_id);

          if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
          }
        }

        $success_message = 'Recipe submitted successfully! It is saved as a draft for approval.';
      }
    }
  }
}
?>

<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/submit-banner.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Submit a Recipe</h1>
    <div class="page-banner__intro">
      <p>Share your recipe with the community. Your post will be reviewed before publishing.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">

  <?php if ($success_message) : ?>
    <div class="metabox" style="padding: 15px;">
      <p><strong><?php echo esc_html($success_message); ?></strong></p>
    </div>
  <?php endif; ?>

  <?php if ($error_message) : ?>
    <div class="metabox" style="padding: 15px;">
      <p><strong><?php echo esc_html($error_message); ?></strong></p>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="generic-content">
    <?php wp_nonce_field('oc_submit_recipe', 'oc_nonce'); ?>
    <input type="hidden" name="oc_submit_recipe" value="1">

    <p>
      <label>Recipe Title<br>
        <input type="text" name="recipe_title" required style="width: 100%;">
      </label>
    </p>

    <p>
      <label>Prep Time (e.g. 30 mins)<br>
        <input type="text" name="prep_time" style="width: 100%;">
      </label>
    </p>

    <p>
      <label>Difficulty<br>
        <select name="difficulty" style="width: 100%;">
          <option value="">Select</option>
          <option value="Easy">Easy</option>
          <option value="Medium">Medium</option>
          <option value="Hard">Hard</option>
        </select>
      </label>
    </p>

    <p>
      <label>Ingredients (one per line)<br>
        <textarea name="ingredients" rows="8" required style="width: 100%;"></textarea>
      </label>
    </p>

    <p>
      <label>Instructions<br>
        <textarea name="instructions" rows="10" required style="width: 100%;"></textarea>
      </label>
    </p>

    <p>
      <label>Recipe Image<br>
        <input type="file" name="recipe_image" accept="image/*">
      </label>
    </p>

    <p>
      <button type="submit" class="btn btn--blue">Submit Recipe</button>
    </p>

    <p><small>Note: Submissions are saved as drafts for approval.</small></p>
  </form>
</div>

<?php get_footer(); ?>