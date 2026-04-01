<?php
function onlinecookbook_files() {
  wp_enqueue_style('onlinecookbook_main_styles', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'onlinecookbook_files');

function cookbook_login_redirect($redirect_to, $request, $user) {
    // Always redirect to home page after login (skip dashboard)
    return home_url();
}
add_filter('login_redirect', 'cookbook_login_redirect', 10, 3);

function cookbook_register_recipe_cpt() {
  $labels = array(
    'name'               => 'Recipes',
    'singular_name'      => 'Recipe',
    'add_new_item'       => 'Add New Recipe',
    'edit_item'          => 'Edit Recipe',
    'view_item'          => 'View Recipe',
    'all_items'          => 'All Recipes',
    'search_items'       => 'Search Recipes',
    'not_found'          => 'No recipes found',
    'not_found_in_trash' => 'No recipes found in Trash',
  );

  $args = array(
    'labels'             => $labels,
    'public'             => true,
    'has_archive'        => true,
    'rewrite'            => array('slug' => 'recipes'),
    'show_in_rest'       => true,
    'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
    'show_in_menu'       => true,
    'menu_position'      => 5,
    'menu_icon'          => 'dashicons-carrot',
  );

  register_post_type('recipe', $args);
}

add_action('init', 'cookbook_register_recipe_cpt');

function cookbook_register_recipe_taxonomy() {
  $labels = array(
    'name'              => 'Recipe Categories',
    'singular_name'     => 'Recipe Category',
    'search_items'      => 'Search Recipe Categories',
    'all_items'         => 'All Recipe Categories',
    'parent_item'       => 'Parent Recipe Category',
    'parent_item_colon' => 'Parent Recipe Category:',
    'edit_item'         => 'Edit Recipe Category',
    'update_item'       => 'Update Recipe Category',
    'add_new_item'      => 'Add New Recipe Category',
    'new_item_name'     => 'New Recipe Category Name',
    'menu_name'         => 'Recipe Categories',
  );

  $args = array(
    'labels'            => $labels,
    'hierarchical'      => true,
    'public'            => true,
    'show_ui'           => true,
    'show_admin_column' => true,
    'show_in_rest'      => true,
    'rewrite'           => array('slug' => 'recipe-category'),
  );

  register_taxonomy('recipe_category', array('recipe'), $args);
}
add_action('init', 'cookbook_register_recipe_taxonomy');