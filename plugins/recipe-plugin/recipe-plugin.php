<?php
/*
Plugin Name: Recipe Display Plugin
Description: Displays featured recipes using a shortcode
Version: 1.0
*/

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
    'capability_type' => 'recipe',
    'map_meta_cap'    => true,
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

  register_taxonomy('recipe_category', 'recipe', $args);
}
add_action('init', 'cookbook_register_recipe_taxonomy');

function cookbook_recipe_plugin_activate() {
  cookbook_register_recipe_cpt();
  cookbook_register_recipe_taxonomy();
  flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'cookbook_recipe_plugin_activate');

function cookbook_recipe_plugin_deactivate() {
  flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'cookbook_recipe_plugin_deactivate');

function display_featured_recipes() {
    $args = array(
        'post_type' => 'recipe',
        'posts_per_page' => 5
    );

    $query = new WP_Query($args);

    $output = '<h3>Featured Recipes</h3>';

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $output .= '<p>' . get_the_title() . '</p>';
        }
    } else {
        $output .= '<p>No recipes found</p>';
    }

    wp_reset_postdata();

    return $output;
}

add_shortcode('featured_recipes', 'display_featured_recipes');