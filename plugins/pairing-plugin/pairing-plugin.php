<?php
/*
Plugin Name: Cookbook Pairings
Description: Registers a custom post type for recipe pairings (two recipes paired together with a description).
Version: 1.0
*/

function cookbook_register_pairing_cpt() {
    $labels = array(
        'name'               => 'Pairings',
        'singular_name'      => 'Pairing',
        'add_new_item'       => 'Add New Pairing',
        'edit_item'          => 'Edit Pairing',
        'view_item'          => 'View Pairing',
        'all_items'          => 'All Pairings',
        'search_items'       => 'Search Pairings',
        'not_found'          => 'No pairings found',
        'not_found_in_trash' => 'No pairings found in Trash',
        'capability_type' => 'pairing',
        'map_meta_cap'    => true,
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'pairings'),
        'show_in_rest'  => true,
        'supports'      => array('title', 'author', 'comments'),
        'show_in_menu'  => true,
        'menu_position' => 7,
        'menu_icon'     => 'dashicons-heart',
        'capability_type' => 'pairing',
        'map_meta_cap'    => true,

    );

    register_post_type('pairing', $args);
}
add_action('init', 'cookbook_register_pairing_cpt');



function cookbook_register_pairing_meta() {
    register_post_meta('pairing', 'pairing_recipe_a', array(
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'integer',
        'auth_callback' => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('pairing', 'pairing_recipe_b', array(
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'integer',
        'auth_callback' => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('pairing', 'pairing_description', array(
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'string',
        'auth_callback' => function() { return current_user_can('edit_posts'); },
    ));
}
add_action('init', 'cookbook_register_pairing_meta');


function cookbook_add_pairing_meta_box() {
    add_meta_box(
        'pairing_details',
        'Pairing Details',
        'cookbook_render_pairing_meta_box',
        'pairing',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'cookbook_add_pairing_meta_box');

function cookbook_render_pairing_meta_box($post) {
    wp_nonce_field('cookbook_pairing_meta', 'cookbook_pairing_nonce');

    $recipe_a     = get_post_meta($post->ID, 'pairing_recipe_a', true);
    $recipe_b     = get_post_meta($post->ID, 'pairing_recipe_b', true);
    $description  = get_post_meta($post->ID, 'pairing_description', true);

    $recipes = get_posts(array(
        'post_type'      => 'recipe',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ));
    ?>
    <p>
        <label style="font-weight:700;" for="pairing_recipe_a">Recipe A:</label><br>
        <select id="pairing_recipe_a" name="pairing_recipe_a" style="width:100%; margin-top:6px;">
            <option value="">— Select a recipe —</option>
            <?php foreach ($recipes as $r) : ?>
                <option value="<?php echo esc_attr($r->ID); ?>" <?php selected($recipe_a, $r->ID); ?>>
                    <?php echo esc_html($r->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label style="font-weight:700;" for="pairing_recipe_b">Recipe B:</label><br>
        <select id="pairing_recipe_b" name="pairing_recipe_b" style="width:100%; margin-top:6px;">
            <option value="">— Select a recipe —</option>
            <?php foreach ($recipes as $r) : ?>
                <option value="<?php echo esc_attr($r->ID); ?>" <?php selected($recipe_b, $r->ID); ?>>
                    <?php echo esc_html($r->post_title); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label style="font-weight:700;" for="pairing_description">Why do these pair well?</label><br>
        <textarea id="pairing_description" name="pairing_description" rows="4"
                  style="width:100%; margin-top:6px;"><?php echo esc_textarea($description); ?></textarea>
    </p>
    <?php
}


function cookbook_pairings_plugin_activate() {
    cookbook_register_pairing_cpt();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'cookbook_pairings_plugin_activate');

function cookbook_pairings_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'cookbook_pairings_plugin_deactivate');
