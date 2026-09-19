<?php

defined('ABSPATH') || exit;

/**
 * @return void
 */
function disable_extras_wpforms_boot(): void {
  add_action('plugins_loaded', 'disable_extras_wpforms_apply', 30);
}

/**
 * @return void
 */
function disable_extras_wpforms_apply(): void {
  if (!disable_extras_is_wpforms_active()) {
    return;
  }

  if (disable_extras_is_enabled('wpforms', 'global_assets')) {
    add_filter('wpforms_global_assets', '__return_false', 99);
  }

  if (disable_extras_is_enabled('wpforms', 'assets_without_form')) {
    add_action('wp_enqueue_scripts', 'disable_extras_wpforms_dequeue_assets', 999);
    add_action('wp_print_scripts', 'disable_extras_wpforms_dequeue_assets', 999);
    add_action('wp_print_styles', 'disable_extras_wpforms_dequeue_assets', 999);
  }

  if (disable_extras_is_enabled('wpforms', 'admin_bar')) {
    add_filter('wpforms_admin_adminbarmenu_has_access', '__return_false', 99);
  }

  if (disable_extras_is_enabled('wpforms', 'announcements')) {
    add_filter('wpforms_admin_notifications_has_access', '__return_false', 99);
  }

  if (disable_extras_is_enabled('wpforms', 'dashboard_widget')) {
    add_action('wp_dashboard_setup', 'disable_extras_wpforms_remove_dashboard_widget', 99);
  }

  if (disable_extras_is_enabled('wpforms', 'menu_upsells')) {
    add_action('admin_menu', 'disable_extras_wpforms_remove_menu_upsells', 999);
  }
}

/**
 * @return bool
 */
function disable_extras_wpforms_page_has_form(): bool {
  if (!is_singular()) {
    return false;
  }

  $post = get_post();

  if (!$post instanceof WP_Post) {
    return false;
  }

  if (has_shortcode($post->post_content, 'wpforms')) {
    return true;
  }

  if (function_exists('has_block') && has_block('wpforms/form-selector', $post)) {
    return true;
  }

  return false;
}

/**
 * @return void
 */
function disable_extras_wpforms_dequeue_assets(): void {
  if (is_admin() || disable_extras_wpforms_page_has_form()) {
    return;
  }

  global $wp_scripts, $wp_styles;

  if ($wp_scripts instanceof WP_Scripts) {
    foreach (array_keys($wp_scripts->registered) as $handle) {
      if (str_starts_with((string) $handle, 'wpforms')) {
        wp_dequeue_script($handle);
        wp_deregister_script($handle);
      }
    }
  }

  if ($wp_styles instanceof WP_Styles) {
    foreach (array_keys($wp_styles->registered) as $handle) {
      if (str_starts_with((string) $handle, 'wpforms')) {
        wp_dequeue_style($handle);
        wp_deregister_style($handle);
      }
    }
  }
}

/**
 * @return void
 */
function disable_extras_wpforms_remove_dashboard_widget(): void {
  remove_meta_box('wpforms_reports_widget_lite', 'dashboard', 'normal');
  remove_meta_box('wpforms_reports_widget', 'dashboard', 'normal');
}

/**
 * @return void
 */
function disable_extras_wpforms_remove_menu_upsells(): void {
  $parent = 'wpforms-overview';

  foreach ([
    'wpforms-analytics',
    'wpforms-smtp',
    'wpforms-about',
    'wpforms-community',
    'wpforms-wpconsent',
    'wpforms-addons',
  ] as $slug) {
    remove_submenu_page($parent, $slug);
  }
}
