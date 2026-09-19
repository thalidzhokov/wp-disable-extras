<?php

defined('ABSPATH') || exit;

/**
 * @return void
 */
function disable_extras_wp_mail_smtp_boot(): void {
  add_action('plugins_loaded', 'disable_extras_wp_mail_smtp_apply', 30);
}

/**
 * @return void
 */
function disable_extras_wp_mail_smtp_apply(): void {
  if (!disable_extras_is_wp_mail_smtp_active()) {
    return;
  }

  if (disable_extras_is_enabled('wp_mail_smtp', 'dashboard_widget')) {
    add_filter('wp_mail_smtp_admin_dashboard_widget', '__return_false', 99);
    add_action('wp_dashboard_setup', 'disable_extras_wp_mail_smtp_remove_dashboard_widget', 99);
  }

  if (disable_extras_is_enabled('wp_mail_smtp', 'admin_bar')) {
    add_filter('wp_mail_smtp_admin_adminbarmenu_has_access', '__return_false', 99);
  }

  if (disable_extras_is_enabled('wp_mail_smtp', 'announcements')) {
    add_filter('wp_mail_smtp_admin_notifications_has_access', '__return_false', 99);
  }

  if (disable_extras_is_enabled('wp_mail_smtp', 'flyout')) {
    add_filter('wp_mail_smtp_admin_flyout_menu', '__return_false', 99);
  }

  if (disable_extras_is_enabled('wp_mail_smtp', 'menu_upsells')) {
    add_filter('wp_mail_smtp_admin_area_get_parent_pages', 'disable_extras_wp_mail_smtp_filter_parent_pages', 99);
    add_action('admin_menu', 'disable_extras_wp_mail_smtp_remove_menu_upsells', 999);
  }

  if (disable_extras_is_enabled('wp_mail_smtp', 'delivery_error_notices')) {
    add_filter('wp_mail_smtp_admin_is_error_delivery_notice_enabled', '__return_false', 99);
  }
}

/**
 * @return void
 */
function disable_extras_wp_mail_smtp_remove_dashboard_widget(): void {
  remove_meta_box('wp_mail_smtp_reports_widget_lite', 'dashboard', 'normal');
  remove_meta_box('wp_mail_smtp_reports_widget', 'dashboard', 'normal');
}

/**
 * @param array<string, mixed> $pages
 *
 * @return array<string, mixed>
 */
function disable_extras_wp_mail_smtp_filter_parent_pages(array $pages): array {
  unset($pages['about']);

  return $pages;
}

/**
 * @return void
 */
function disable_extras_wp_mail_smtp_remove_menu_upsells(): void {
  global $submenu;

  $parent = 'wp-mail-smtp';

  if (!isset($submenu[$parent]) || !is_array($submenu[$parent])) {
    return;
  }

  foreach ($submenu[$parent] as $index => $item) {
    $slug = isset($item[2]) ? (string) $item[2] : '';
    $decoded = urldecode($slug);

    if (
      $slug === 'wp-mail-smtp-about'
      || str_starts_with($slug, 'wp-mail-smtp-recommended-')
      || str_contains($slug, 'wpmailsmtp.com/lite-upgrade')
      || str_contains($decoded, 'wpmailsmtp.com/lite-upgrade')
    ) {
      unset($submenu[$parent][$index]);
    }
  }

  $submenu[$parent] = array_values($submenu[$parent]);
}
