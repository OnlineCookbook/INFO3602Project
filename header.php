<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php bloginfo('name'); ?></title>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<header class="site-header">
  <div class="container site-header__inner">

    <a class="site-header__logo" href="<?php echo esc_url(site_url('/')); ?>">
      <span class="site-header__logo-mark">🍯</span>
      <span class="site-header__logo-text"><?php bloginfo('name'); ?></span>
    </a>

    <button class="site-header__toggle" type="button" aria-label="Toggle menu">
      ☰
    </button>

    <nav class="site-header__nav">
      <ul class="site-header__menu">
        <li><a href="<?php echo esc_url(site_url('/')); ?>">Home</a></li>
        <li class="dropdown">
          <a href="<?php echo esc_url(site_url('/recipes')); ?>" class="dropdown-toggle">Recipes</a>
          <ul class="dropdown-menu">
            <?php
            $categories = get_terms(array(
              'taxonomy' => 'recipe_category',
              'hide_empty' => false,
            ));
            foreach ($categories as $category) {
              echo '<li><a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a></li>';
            }
            ?>
          </ul>
        </li>
        <li><a href="<?php echo esc_url(site_url('/submit-a-recipe')); ?>">Submit</a></li>

        <?php if (is_user_logged_in()) { ?>
          <li><a href="<?php echo esc_url(wp_logout_url(site_url('/'))); ?>">Logout</a></li>
        <?php } else { ?>
          <li><a href="<?php echo esc_url(wp_login_url()); ?>">Login</a></li>
        <?php } ?>
      </ul>
    </nav>

  </div>
</header>

<?php if (function_exists('yoast_breadcrumb')) { ?>
  <div class="breadcrumbs">
    <?php yoast_breadcrumb(); ?>
  </div>
<?php } ?>

<main class="main-content">