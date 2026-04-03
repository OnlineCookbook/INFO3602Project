<?php

get_header();

if (!is_user_logged_in()) { ?>

  <div class="page-banner">
    <div class="page-banner__bg-image"
         style="background-image: url(<?php echo get_theme_file_uri('images/submit-banner.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title">Submit a Pairing</h1>
      <div class="page-banner__intro">
        <p>You must be logged in to submit a pairing.</p>
      </div>
    </div>
  </div>

  <div class="container container--narrow page-section">
    <p><a class="btn btn--blue"   href="<?php echo wp_login_url(get_permalink()); ?>">Log in</a></p>
    <p><a class="btn btn--yellow" href="<?php echo wp_registration_url(); ?>">Register</a></p>
  </div>

<?php
  get_footer();
  exit;
}

$success_message = '';
$error_message   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['oc_submit_pairing'])) {

  if (!isset($_POST['oc_pairing_nonce']) ||
      !wp_verify_nonce($_POST['oc_pairing_nonce'], 'oc_submit_pairing')) {
    $error_message = 'Security check failed. Please try again.';
  } else {

    $recipe_a    = absint($_POST['pairing_recipe_a'] ?? 0);
    $recipe_b    = absint($_POST['pairing_recipe_b'] ?? 0);
    $description = sanitize_textarea_field($_POST['pairing_description'] ?? '');

    if (!$recipe_a || !$recipe_b) {
      $error_message = 'Please select two recipes to pair together.';
    } elseif ($recipe_a === $recipe_b) {
      $error_message = 'Please select two different recipes.';
    } elseif (empty($description)) {
      $error_message = 'Please add a description for this pairing.';
    } else {

      $name_a = get_the_title($recipe_a);
      $name_b = get_the_title($recipe_b);

      $post_id = wp_insert_post(array(
        'post_type'    => 'pairing',
        'post_title'   => $name_a . ' + ' . $name_b,
        'post_status'  => 'draft',
        'post_author'  => get_current_user_id(),
      ));

      if (is_wp_error($post_id)) {
        $error_message = 'Could not submit your pairing. Please try again.';
      } else {
        update_post_meta($post_id, 'pairing_recipe_a',    $recipe_a);
        update_post_meta($post_id, 'pairing_recipe_b',    $recipe_b);
        update_post_meta($post_id, 'pairing_description', $description);

        $success_message = 'Pairing submitted! It will appear once reviewed by our team.';
        // Clear form values on success
        $recipe_a = $recipe_b = 0;
        $description = '';
      }
    }
  }
}

// Fetch all published recipes for the dropdowns
$all_recipes = get_posts(array(
  'post_type'      => 'recipe',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'title',
  'order'          => 'ASC',
));

$sel_a   = isset($_POST['pairing_recipe_a'])   ? absint($_POST['pairing_recipe_a'])               : 0;
$sel_b   = isset($_POST['pairing_recipe_b'])   ? absint($_POST['pairing_recipe_b'])               : 0;
$desc    = isset($_POST['pairing_description']) ? sanitize_textarea_field($_POST['pairing_description']) : '';
if (!empty($success_message)) { $sel_a = $sel_b = 0; $desc = ''; }
?>








<div class="page-banner">
  <div class="page-banner__bg-image"
       style="background-image: url(<?php echo get_theme_file_uri('images/submit-banner.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Submit a Pairing</h1>
    <div class="page-banner__intro">
      <p>Pick two recipes that go great together and tell us why. Pairings are reviewed before publishing.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">

  <?php if ($success_message) : ?>
    <div class="metabox" style="padding:15px; border-radius:12px; margin-bottom:20px; display:block;">
      <p><strong><?php echo esc_html($success_message); ?></strong></p>
    </div>
  <?php endif; ?>

  <?php if ($error_message) : ?>
    <div class="metabox" style="padding:15px; border-radius:12px; margin-bottom:20px; display:block; background:#fef2f2; border-color:#fca5a5; color:#991b1b;">
      <p><strong><?php echo esc_html($error_message); ?></strong></p>
    </div>
  <?php endif; ?>

  <form method="post" class="generic-content" id="pairingForm">
    <?php wp_nonce_field('oc_submit_pairing', 'oc_pairing_nonce'); ?>
    <input type="hidden" name="oc_submit_pairing" value="1">

    <!-- Recipe selectors -->
    <div class="pairing-select-row">

      <div class="pairing-select-card">
        <label for="pairing_recipe_a">First Recipe</label>
        <select name="pairing_recipe_a" id="pairing_recipe_a" required>
          <option value=""> Choose a recipe </option>
          <?php foreach ($all_recipes as $r) : ?>
            <option value="<?php echo esc_attr($r->ID); ?>"
              <?php selected($sel_a, $r->ID); ?>>
              <?php echo esc_html($r->post_title); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="pairing-plus-divider">+</div>

      <div class="pairing-select-card">
        <label for="pairing_recipe_b"> Second Recipe</label>
        <select name="pairing_recipe_b" id="pairing_recipe_b" required>
          <option value=""> Choose a recipe </option>
          <?php foreach ($all_recipes as $r) : ?>
            <option value="<?php echo esc_attr($r->ID); ?>"
              <?php selected($sel_b, $r->ID); ?>>
              <?php echo esc_html($r->post_title); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

    </div>

    

    <!-- Description -->
    <p style="margin-top:22px;">
      <label>Why do these pair well together?<br>
        <textarea name="pairing_description" rows="5" required
                  style="width:100%;"><?php echo esc_textarea($desc); ?></textarea>
      </label>
    </p>

    <p>
      <button type="submit" class="btn btn--blue">Submit Pairing</button>
    </p>

    <p><small>Note: Submissions are saved as drafts and reviewed before publishing.</small></p>
  </form>

</div>

<?php

$recipe_map = array();
foreach ($all_recipes as $r) {
    $recipe_map[$r->ID] = array('title' => $r->post_title);
}
?>


<?php get_footer(); ?>
