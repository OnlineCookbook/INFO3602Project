<?php
function onlinecookbook_files() {
  wp_enqueue_style('onlinecookbook_main_styles', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'onlinecookbook_files');