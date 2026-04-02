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