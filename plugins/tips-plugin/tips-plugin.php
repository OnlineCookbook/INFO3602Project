<?php
/*
Plugin Name: Cookbook Tips & Tricks
Description: Registers a custom post type for kitchen tips and tricks articles.
Version: 1.0
*/


function cookbook_register_tip_cpt() {
    $labels = array(
        
        'name'               => 'Tips & Tricks',
        'singular_name'      => 'Tip',
        'add_new_item'       => 'Add New Tip',
        'edit_item'          => 'Edit Tip',
        'view_item'          => 'View Tip',
        'all_items'          => 'All Tips',
        'search_items'       => 'Search Tips',
        'not_found'          => 'No tips found',
        'not_found_in_trash' => 'No tips found in Trash',
        
    );

    $args = array(
        'labels'        => $labels,
        'public'        => true,
        'has_archive'   => true,
        'rewrite'       => array('slug' => 'tips'),
        'show_in_rest'  => true,
        'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'comments', 'author'),
        'show_in_menu'  => true,
        'menu_position' => 6,
        'menu_icon'     => 'dashicons-lightbulb',
        'capability_type' => 'tip',
        'map_meta_cap'    => true,

    );

    register_post_type('tip', $args);
}
add_action('init', 'cookbook_register_tip_cpt');


function cookbook_register_tip_taxonomy() {
    $labels = array(
        'name'              => 'Tip Categories',
        'singular_name'     => 'Tip Category',
        'search_items'      => 'Search Tip Categories',
        'all_items'         => 'All Tip Categories',
        'edit_item'         => 'Edit Tip Category',
        'update_item'       => 'Update Tip Category',
        'add_new_item'      => 'Add New Tip Category',
        'new_item_name'     => 'New Tip Category Name',
        'menu_name'         => 'Tip Categories',
    );

    $args = array(
        'labels'            => $labels,
        'hierarchical'      => true,
        'public'            => true,
        'show_ui'           => true,
        'show_admin_column' => true,
        'show_in_rest'      => true,
        'rewrite'           => array('slug' => 'tip-category'),
    );

    register_taxonomy('tip_category', 'tip', $args);
}
add_action('init', 'cookbook_register_tip_taxonomy');



function cookbook_register_tip_meta() {
    register_post_meta('tip', 'tip_number', array(
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'integer',
        'auth_callback' => function() { return current_user_can('edit_posts'); },
    ));

    register_post_meta('tip', 'tip_tag', array(
        'show_in_rest'  => true,
        'single'        => true,
        'type'          => 'string',
        'auth_callback' => function() { return current_user_can('edit_posts'); },
    ));
}
add_action('init', 'cookbook_register_tip_meta');

function cookbook_add_tip_meta_box() {
    add_meta_box(
        'tip_details',
        'Tip Details',
        'cookbook_render_tip_meta_box',
        'tip',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'cookbook_add_tip_meta_box');

function cookbook_render_tip_meta_box($post) {
    wp_nonce_field('cookbook_tip_meta', 'cookbook_tip_nonce');
    $tip_number = get_post_meta($post->ID, 'tip_number', true);
    $tip_tag    = get_post_meta($post->ID, 'tip_tag', true);
    ?>
    <p>
        <label style="font-weight:700;" for="tip_number">Number of items</label><br>
        <input type="number" id="tip_number" name="tip_number"
               value="<?php echo esc_attr($tip_number); ?>"
               style="width:100%; margin-top:6px;">
    </p>
    <p>
        <label style="font-weight:700;" for="tip_tag">Topic tag</label><br>
        <input type="text" id="tip_tag" name="tip_tag"
               value="<?php echo esc_attr($tip_tag); ?>"
               style="width:100%; margin-top:6px;"
               placeholder="e.g. Storage">
    </p>
    <?php
}




function cookbook_tips_plugin_activate() {
    cookbook_register_tip_cpt();
    cookbook_register_tip_taxonomy();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'cookbook_tips_plugin_activate');

function cookbook_tips_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'cookbook_tips_plugin_deactivate');



function cookbook_display_featured_tips($atts) {
    $atts = shortcode_atts(array('count' => 5), $atts, 'featured_tips');

    $query = new WP_Query(array(
        'post_type'      => 'tip',
        'posts_per_page' => intval($atts['count']),
        'post_status'    => 'publish',
    ));

    if (!$query->have_posts()) {
        return '<p>No tips found.</p>';
    }

    $output = '<ul class="featured-tips-list">';
    while ($query->have_posts()) {
        $query->the_post();
        $output .= '<li><a href="' . esc_url(get_permalink()) . '">' . esc_html(get_the_title()) . '</a></li>';
    }
    $output .= '</ul>';
    wp_reset_postdata();

    return $output;
}
add_shortcode('featured_tips', 'cookbook_display_featured_tips');
