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

  if (disable_extras_is_enabled('yoast', 'ai_noise')) {
    add_filter('wpseo_enable_structured_data_blocks', '__return_false');
    add_filter('wpseo_enable_assessment_markers', '__return_false');
    add_filter('wpseo_enable_ai_content_planner_inline_banner', '__return_false');
  }

  if (disable_extras_is_enabled('yoast', 'rss_footer')) {
    add_filter('wpseo_include_rss_footer', '__return_false');
  }

  if (disable_extras_is_enabled('yoast', 'adjacent_rel')) {
    add_filter('wpseo_disable_adjacent_rel_links', '__return_true');
  }

  if (disable_extras_is_enabled('yoast', 'admin_upsells')) {
    add_filter('wpseo_enable_tracking', '__return_false');
    add_action('admin_init', 'disable_extras_yoast_hide_upsells', 20);
  }

  if (disable_extras_is_enabled('yoast', 'integrations_ui')) {
    add_filter('wpseo_submenu_pages', 'disable_extras_yoast_filter_submenu_pages', 99);
  }

  if (disable_extras_is_enabled('yoast', 'dashboard_widget')) {
    add_action('wp_dashboard_setup', 'disable_extras_yoast_remove_dashboard_widgets', 99);
    add_action('wp_network_dashboard_setup', 'disable_extras_yoast_remove_dashboard_widgets', 99);
  }
}

/**
 * @return void
 */
function disable_extras_yoast_hide_upsells(): void {
  remove_action('admin_notices', 'wpseo_admin_notices');

  if (class_exists('WPSEO_Admin', false)) {
    remove_all_actions('wpseo_admin_footer');
  }
}

/**
 * @param array<int, mixed> $pages
 *
 * @return array<int, mixed>
 */
function disable_extras_yoast_filter_submenu_pages(array $pages): array {
  $blocked = [
    'wpseo_integrations',
    'wpseo_workouts',
    'wpseo_courses',
    'wpseo_page_academy',
    'wpseo_licenses',
    'wpseo_installation_successful',
  ];

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
function disable_extras_yoast_remove_dashboard_widgets(): void {
  remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
  remove_meta_box('wpseo-wincher-dashboard-overview', 'dashboard', 'normal');
}
