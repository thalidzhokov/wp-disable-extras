<?php

defined('ABSPATH') || exit;

/**
 * @return void
 */
function disable_extras_yoast_boot(): void {
  add_action('plugins_loaded', 'disable_extras_yoast_apply', 30);
}

/**
 * @return void
 */
function disable_extras_yoast_apply(): void {
  if (!defined('WPSEO_VERSION')) {
    return;
  }

  if (disable_extras_is_enabled('yoast', 'premium_redirects')) {
    add_filter('Yoast\WP\SEO\post_redirect_slug_change', '__return_true');
    add_filter('Yoast\WP\SEO\term_redirect_slug_change', '__return_true');
  }

  if (disable_extras_is_enabled('yoast', 'premium_notifications')) {
    add_filter('Yoast\WP\SEO\enable_notification_post_trash', '__return_false');
    add_filter('Yoast\WP\SEO\enable_notification_post_slug_change', '__return_false');
    add_filter('Yoast\WP\SEO\enable_notification_term_delete', '__return_false');
    add_filter('Yoast\WP\SEO\enable_notification_term_slug_change', '__return_false');
  }

  if (disable_extras_is_enabled('yoast', 'tracking')) {
    add_filter('wpseo_enable_tracking', '__return_false');
  }

  if (disable_extras_is_enabled('yoast', 'schema_blocks')) {
    add_filter('wpseo_enable_structured_data_blocks', '__return_false');
  }

  if (disable_extras_is_enabled('yoast', 'assessment_markers')) {
    add_filter('wpseo_enable_assessment_markers', '__return_false');
  }

  if (disable_extras_is_enabled('yoast', 'ai_banner')) {
    add_filter('wpseo_enable_ai_content_planner_inline_banner', '__return_false');
  }

  if (disable_extras_is_enabled('yoast', 'rss_footer')) {
    add_filter('wpseo_include_rss_footer', '__return_false');
  }

  if (disable_extras_is_enabled('yoast', 'adjacent_rel')) {
    add_filter('wpseo_disable_adjacent_rel_links', '__return_true');
  }

  if (disable_extras_is_enabled('yoast', 'admin_notices')) {
    add_action('admin_init', 'disable_extras_yoast_hide_admin_notices', 20);
  }

  if (disable_extras_is_enabled('yoast', 'admin_footer')) {
    add_action('admin_init', 'disable_extras_yoast_hide_admin_footer', 20);
  }

  if (
    disable_extras_is_enabled('yoast', 'menu_integrations')
    || disable_extras_is_enabled('yoast', 'menu_workouts')
    || disable_extras_is_enabled('yoast', 'menu_courses')
    || disable_extras_is_enabled('yoast', 'menu_licenses')
    || disable_extras_is_enabled('yoast', 'menu_redirects')
    || disable_extras_is_enabled('yoast', 'menu_upgrade')
    || disable_extras_is_enabled('yoast', 'menu_brand_insights')
  ) {
    add_action('init', 'disable_extras_yoast_register_submenu_filters', 20);
    add_action('admin_menu', 'disable_extras_yoast_remove_admin_submenu_pages', 999);
    add_action('network_admin_menu', 'disable_extras_yoast_remove_admin_submenu_pages', 999);
  }

  if (
    disable_extras_is_enabled('yoast', 'dashboard_yoast')
    || disable_extras_is_enabled('yoast', 'dashboard_wincher')
  ) {
    add_action('wp_dashboard_setup', 'disable_extras_yoast_remove_dashboard_widgets', 99);
    add_action('wp_network_dashboard_setup', 'disable_extras_yoast_remove_dashboard_widgets', 99);
  }
}

/**
 * @return void
 */
function disable_extras_yoast_hide_admin_notices(): void {
  remove_action('admin_notices', 'wpseo_admin_notices');
}

/**
 * @return void
 */
function disable_extras_yoast_hide_admin_footer(): void {
  if (class_exists('WPSEO_Admin', false)) {
    remove_all_actions('wpseo_admin_footer');
  }
}

/**
 * @return void
 */
function disable_extras_yoast_register_submenu_filters(): void {
  add_filter('wpseo_submenu_pages', 'disable_extras_yoast_filter_submenu_pages', PHP_INT_MAX);
  add_filter('wpseo_network_submenu_pages', 'disable_extras_yoast_filter_submenu_pages', PHP_INT_MAX);
}

/**
 * @return list<string>
 */
function disable_extras_yoast_blocked_menu_slugs(): array {
  $blocked = [];

  if (disable_extras_is_enabled('yoast', 'menu_integrations')) {
    $blocked[] = 'wpseo_integrations';
  }

  if (disable_extras_is_enabled('yoast', 'menu_workouts')) {
    $blocked[] = 'wpseo_workouts';
  }

  if (disable_extras_is_enabled('yoast', 'menu_courses')) {
    $blocked[] = 'wpseo_courses';
    $blocked[] = 'wpseo_page_academy';
  }

  if (disable_extras_is_enabled('yoast', 'menu_licenses')) {
    $blocked[] = 'wpseo_licenses';
    $blocked[] = 'wpseo_installation_successful';
  }

  if (disable_extras_is_enabled('yoast', 'menu_redirects')) {
    $blocked[] = 'wpseo_redirects';
  }

  if (disable_extras_is_enabled('yoast', 'menu_upgrade')) {
    $blocked[] = 'wpseo_upgrade_sidebar';
  }

  if (disable_extras_is_enabled('yoast', 'menu_brand_insights')) {
    $blocked[] = 'wpseo_brand_insights';
    $blocked[] = 'wpseo_brand_insights_premium';
  }

  return $blocked;
}

/**
 * @param array<int, mixed> $pages
 *
 * @return array<int, mixed>
 */
function disable_extras_yoast_filter_submenu_pages(array $pages): array {
  $blocked = disable_extras_yoast_blocked_menu_slugs();

  if ($blocked === []) {
    return $pages;
  }

  return array_values(array_filter(
    $pages,
    static function ($page) use ($blocked): bool {
      if (!is_array($page)) {
        return true;
      }

      $slug = $page[4] ?? '';

      return !in_array($slug, $blocked, true);
    }
  ));
}

/**
 * @return void
 */
function disable_extras_yoast_remove_admin_submenu_pages(): void {
  global $submenu;

  if (!is_array($submenu)) {
    return;
  }

  $blocked = disable_extras_yoast_blocked_menu_slugs();

  if ($blocked === []) {
    return;
  }

  foreach ($submenu as $parent => $items) {
    if (!is_array($items)) {
      continue;
    }

    foreach ($items as $index => $item) {
      $slug = $item[2] ?? '';

      if (in_array($slug, $blocked, true)) {
        unset($submenu[$parent][$index]);
      }
    }

    $submenu[$parent] = array_values($submenu[$parent]);
  }
}

/**
 * @return void
 */
function disable_extras_yoast_remove_dashboard_widgets(): void {
  if (disable_extras_is_enabled('yoast', 'dashboard_yoast')) {
    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
  }

  if (disable_extras_is_enabled('yoast', 'dashboard_wincher')) {
    remove_meta_box('wpseo-wincher-dashboard-overview', 'dashboard', 'normal');
  }
}
