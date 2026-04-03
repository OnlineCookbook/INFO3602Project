<?php
function onlinecookbook_files()
{
  wp_enqueue_style('onlinecookbook_main_styles', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'onlinecookbook_files');

function cookbook_login_redirect($redirect_to, $request, $user)
{
  // Always redirect to home page after login (skip dashboard)
  return home_url();
}
add_filter('login_redirect', 'cookbook_login_redirect', 10, 3);

function redirectSubsToFrontend()
{
  $ourCurrentUser = wp_get_current_user();
  if (empty($ourCurrentUser->roles)) return;
  $userNumRoles = count($ourCurrentUser->roles);
  $userRole = $ourCurrentUser->roles[0];
  if ($userNumRoles == 1 AND $userRole == 'subscriber') {
    wp_redirect(site_url('/'));
    exit;
  }
}
add_action('admin_init', 'redirectSubsToFrontend');

add_action('wp_loaded', 'noSubsAdminBar');
function noSubsAdminBar()
{
  $ourCurrentUser = wp_get_current_user();
  if (empty($ourCurrentUser->roles)) return;
  $userNumRoles = count($ourCurrentUser->roles);
  $userRole = $ourCurrentUser->roles[0];
  if ($userNumRoles == 1 AND $userRole == 'subscriber') {
    show_admin_bar(false);
  }
}




