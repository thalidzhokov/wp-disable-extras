<?php

defined('ABSPATH') || exit;

/**
 * Constants must exist before Redis Object Cache reads them.
 *
 * @return void
 */
function disable_extras_redis_define_constants(): void {
  $map = [
    'adminbar' => 'WP_REDIS_DISABLE_ADMINBAR',
    'banners' => 'WP_REDIS_DISABLE_BANNERS',
    'dropin_banners' => 'WP_REDIS_DISABLE_DROPIN_BANNERS',
    'html_comment' => 'WP_REDIS_DISABLE_COMMENT',
    'metrics' => 'WP_REDIS_DISABLE_METRICS',
  ];

  foreach ($map as $key => $constant) {
    if (!disable_extras_is_enabled('redis', $key)) {
      continue;
    }

    if (!defined($constant)) {
      define($constant, true);
    }
  }
}

/**
 * @return void
 */
function disable_extras_redis_boot(): void {
  if (!disable_extras_is_enabled('redis', 'dashboard_widget')) {
    return;
  }

  add_action('plugins_loaded', static function (): void {
    add_action('wp_dashboard_setup', 'disable_extras_redis_remove_dashboard_widget', 99);
    add_action('wp_network_dashboard_setup', 'disable_extras_redis_remove_dashboard_widget', 99);
  }, 20);
}

/**
 * @return void
 */
function disable_extras_redis_remove_dashboard_widget(): void {
  remove_meta_box('dashboard_rediscache', 'dashboard', 'normal');
  remove_meta_box('dashboard_rediscache', 'dashboard-network', 'normal');
}
