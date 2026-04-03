<?php
/**
 * page-submit-a-tip.php
 * Front-end submission form for Tips & Tricks posts.
 */

get_header();

if (!is_user_logged_in()) { ?>

  <div class="page-banner">
    <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/tips-banner.jpg'); ?>);"></div>
    <div class="page-banner__content container container--narrow">
      <h1 class="page-banner__title">Submit a Tip</h1>
      <div class="page-banner__intro">
        <p>You must be logged in to submit a tip.</p>
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


$success_message = '';
$error_message   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['oc_submit_tip'])) {

  if (!isset($_POST['oc_tip_nonce']) || !wp_verify_nonce($_POST['oc_tip_nonce'], 'oc_submit_tip')) {
    $error_message = 'Security check failed. Please try again.';
  } else {

    $title         = sanitize_text_field($_POST['tip_title'] ?? '');
    $tip_number    = absint($_POST['tip_number'] ?? 0);
    $tip_tag       = sanitize_text_field($_POST['tip_tag'] ?? '');
    $tip_content   = wp_kses_post($_POST['tip_content'] ?? '');
    $youtube_url   = esc_url_raw(trim($_POST['youtube_url'] ?? ''));

    // Basic YouTube URL validation
    $youtube_valid = empty($youtube_url) || preg_match('/youtube\.com|youtu\.be/', $youtube_url);

    if (empty($title) || empty($tip_content)) {
      $error_message = 'Please fill in the title and tip content.';
    } elseif (!$youtube_valid) {
      $error_message = 'Please enter a valid YouTube URL, or leave the video field empty.';
    } else {

      $post_id = wp_insert_post(array(
        'post_type'    => 'tip',
        'post_title'   => $title,
        'post_status'  => 'draft',
        'post_content' => $tip_content,
        'post_author'  => get_current_user_id(),
      ));

      // Assign tip categories
      $tip_categories = isset($_POST['tip_categories']) ? array_map('intval', (array) $_POST['tip_categories']) : array();
      if ($post_id && !is_wp_error($post_id) && !empty($tip_categories)) {
        wp_set_post_terms($post_id, $tip_categories, 'tip_category');
      }

      if (is_wp_error($post_id)) {
        $error_message = 'Could not submit your tip. Please try again.';
      } else {

        // Save meta
        if ($tip_number)  update_post_meta($post_id, 'tip_number',  $tip_number);
        if ($tip_tag)     update_post_meta($post_id, 'tip_tag',     $tip_tag);
        if ($youtube_url) update_post_meta($post_id, 'youtube_url', $youtube_url);

        // Featured image upload
        if (!empty($_FILES['tip_image']['name'])) {
          require_once(ABSPATH . 'wp-admin/includes/file.php');
          require_once(ABSPATH . 'wp-admin/includes/media.php');
          require_once(ABSPATH . 'wp-admin/includes/image.php');

          $attachment_id = media_handle_upload('tip_image', $post_id);
          if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
          }
        }

        $success_message = 'Tip submitted successfully! It is saved as a draft for approval.';
      }
    }
  }
}

// -------------------------------------------------------
// Prepare form data
// -------------------------------------------------------
$available_categories = get_terms(array('taxonomy' => 'tip_category', 'hide_empty' => false));
$selected_categories  = isset($_POST['tip_categories']) ? array_map('intval', (array) $_POST['tip_categories']) : array();

$tip_title   = isset($_POST['tip_title'])   ? sanitize_text_field($_POST['tip_title'])   : '';
$tip_number  = isset($_POST['tip_number'])  ? absint($_POST['tip_number'])               : '';
$tip_tag     = isset($_POST['tip_tag'])     ? sanitize_text_field($_POST['tip_tag'])     : '';
$tip_content = isset($_POST['tip_content']) ? wp_kses_post($_POST['tip_content'])        : '';
$youtube_url = isset($_POST['youtube_url']) ? esc_url($_POST['youtube_url'])             : '';
?>

<!-- Page Banner -->
<div class="page-banner">
  <div class="page-banner__bg-image" style="background-image: url(<?php echo get_theme_file_uri('images/tips-banner.jpg'); ?>);"></div>
  <div class="page-banner__content container container--narrow">
    <h1 class="page-banner__title">Submit a Tip</h1>
    <div class="page-banner__intro">
      <p>Share a kitchen tip or trick with the community. Your post will be reviewed before publishing.</p>
    </div>
  </div>
</div>

<div class="container container--narrow page-section">

  <?php if ($success_message) : ?>
    <div class="metabox" style="padding: 15px; border-radius: 12px; margin-bottom: 20px;">
      <p><strong><?php echo esc_html($success_message); ?></strong></p>
    </div>
  <?php endif; ?>

  <?php if ($error_message) : ?>
    <div class="metabox" style="padding: 15px; border-radius: 12px; margin-bottom: 20px; background:#fef2f2; border-color:#fca5a5; color:#991b1b;">
      <p><strong><?php echo esc_html($error_message); ?></strong></p>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="generic-content">
    <?php wp_nonce_field('oc_submit_tip', 'oc_tip_nonce'); ?>
    <input type="hidden" name="oc_submit_tip" value="1">

    <!-- Title -->
    <p>
      <label>Tip Title<br>
        <input type="text" name="tip_title" required style="width:100%;"
               value="<?php echo esc_attr($tip_title); ?>">
      </label>
    </p>

    <!-- Number of items -->
    <p>
      <label>Number of Tips (optional)<br>
        <input type="number" name="tip_number" min="1" max="99" style="width:100%;"
               value="<?php echo esc_attr($tip_number); ?>">
      </label>
    </p>

    <!-- Topic tag -->
    <p>
      <label>Topic Tag (optional)<br>
        <input type="text" name="tip_tag" style="width:100%;"
               value="<?php echo esc_attr($tip_tag); ?>">
      </label>
    </p>

    <!-- Category -->
    <p>
      <label>Tip Category<br>
        <select name="tip_categories[]" multiple style="width:100%; min-height:120px;">
          <?php if (!empty($available_categories) && !is_wp_error($available_categories)) : ?>
            <?php foreach ($available_categories as $cat) : ?>
              <option value="<?php echo esc_attr($cat->term_id); ?>"
                <?php echo in_array($cat->term_id, $selected_categories) ? 'selected' : ''; ?>>
                <?php echo esc_html($cat->name); ?>
              </option>
            <?php endforeach; ?>
          <?php else : ?>
            <option value="">No categories available yet</option>
          <?php endif; ?>
        </select>
      </label>
    
    </p>

    <!-- Tip content -->
    <p>
      <label>Your Tip(s)<br>
        <textarea name="tip_content" rows="12" required style="width:100%;"
                 ><?php echo esc_textarea($tip_content); ?></textarea>
      </label>
    </p>

    <!-- Image -->
    <p>
      <label>Tip Image (optional)<br>
        <input type="file" name="tip_image" accept="image/*">
      </label>
      <small>Upload a photo to go with your tip.</small>
    </p>

    <!-- YouTube URL -->
    <p>
      <label>YouTube Video URL (optional)<br>
        <input type="url" name="youtube_url" style="width:100%;"
               value="<?php echo esc_attr($youtube_url); ?>">
      </label>
      <small>Paste a YouTube link and it will be embedded on your tip page.</small>
    </p>

    <p>
      <button type="submit" class="btn btn--blue">Submit Tip</button>
    </p>

    <p><small>Note: Submissions are saved as drafts and reviewed before publishing.</small></p>
  </form>

</div>

<?php get_footer(); ?>
