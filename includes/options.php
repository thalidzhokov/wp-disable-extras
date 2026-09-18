<?php

defined('ABSPATH') || exit;

/**
 * @return array{
 *   wp: array<string, bool>,
 *   yoast: array<string, bool>,
 *   redis: array<string, bool>,
 *   embedpress: array<string, bool>
 * }
 */
function disable_extras_default_options(): array {
  return [
    'wp' => [
      'emoji' => true,
      'generator' => true,
      'script_versions' => true,
      'resource_hints' => true,
      'dashicons_guests' => true,
      'jquery_front' => true,
      'rsd_wlw_shortlink' => false,
      'xmlrpc' => false,
      'oembed' => false,
      'rest_users' => false,
      'heartbeat_front' => false,
      'self_pingbacks' => false,
      'auto_updates' => false,
      'application_passwords' => false,
      'core_sitemaps' => false,
    ],
    'yoast' => [
      'premium_redirects' => true,
      'premium_notifications' => true,
      'tracking' => false,
      'ai_noise' => false,
      'rss_footer' => false,
      'adjacent_rel' => false,
      'admin_upsells' => false,
      'integrations_ui' => false,
      'dashboard_widget' => false,
    ],
    'redis' => [
      'adminbar' => false,
      'banners' => false,
      'dropin_banners' => false,
      'html_comment' => false,
      'metrics' => false,
      'dashboard_widget' => false,
    ],
    'embedpress' => [
      'assets_outside_single' => true,
      'gallery_justify' => true,
    ],
  ];
}

/**
 * @return array{
 *   wp: array<string, bool>,
 *   yoast: array<string, bool>,
 *   redis: array<string, bool>,
 *   embedpress: array<string, bool>
 * }
 */
function disable_extras_get_options(): array {
  $defaults = disable_extras_default_options();
  $stored = get_option(DISABLE_EXTRAS_OPTION, []);

  if (!is_array($stored)) {
    return $defaults;
  }

  foreach ($defaults as $group => $items) {
    if (!isset($stored[$group]) || !is_array($stored[$group])) {
      $stored[$group] = $items;
      continue;
    }

    foreach ($items as $key => $default) {
      $stored[$group][$key] = !empty($stored[$group][$key]);
    }

    foreach (array_keys($stored[$group]) as $key) {
      if (!array_key_exists($key, $items)) {
        unset($stored[$group][$key]);
      }
    }
  }

  return $stored;
}

/**
 * @param string $group
 * @param string $key
 *
 * @return bool
 */
function disable_extras_is_enabled(string $group, string $key): bool {
  $options = disable_extras_get_options();

  return !empty($options[$group][$key]);
}

/**
 * @return array<string, array<string, string>>
 */
function disable_extras_option_labels(): array {
  return [
    'wp' => [
      'emoji' => __('Emoji (scripts, styles, mail, RSS)', 'disable-extras'),
      'generator' => __('Generator meta tag and RSS version', 'disable-extras'),
      'script_versions' => __('Core ver= query arg on CSS/JS', 'disable-extras'),
      'resource_hints' => __('DNS prefetch / resource hints', 'disable-extras'),
      'dashicons_guests' => __('Dashicons for logged-out users', 'disable-extras'),
      'jquery_front' => __('jQuery and Thickbox on the front end', 'disable-extras'),
      'rsd_wlw_shortlink' => __('RSD, WLW Manifest, shortlink', 'disable-extras'),
      'xmlrpc' => __('XML-RPC', 'disable-extras'),
      'oembed' => __('oEmbed (discovery and embed endpoint)', 'disable-extras'),
      'rest_users' => __('REST users endpoint for guests', 'disable-extras'),
      'heartbeat_front' => __('Heartbeat on the front end', 'disable-extras'),
      'self_pingbacks' => __('Self-pingbacks', 'disable-extras'),
      'auto_updates' => __('Automatic updates for core, plugins, and themes', 'disable-extras'),
      'application_passwords' => __('Application Passwords', 'disable-extras'),
      'core_sitemaps' => __('Core sitemaps', 'disable-extras'),
    ],
    'yoast' => [
      'premium_redirects' => __('Premium: auto-redirects on slug change', 'disable-extras'),
      'premium_notifications' => __('Premium: trash / slug / term notifications', 'disable-extras'),
      'tracking' => __('Tracking', 'disable-extras'),
      'ai_noise' => __('AI / assessment markers / schema blocks', 'disable-extras'),
      'rss_footer' => __('RSS footer', 'disable-extras'),
      'adjacent_rel' => __('Adjacent rel links', 'disable-extras'),
      'admin_upsells' => __('Admin upsells / promotions', 'disable-extras'),
      'integrations_ui' => __('Integrations / Workouts / Courses menu items', 'disable-extras'),
      'dashboard_widget' => __('Yoast / Wincher dashboard widgets', 'disable-extras'),
    ],
    'redis' => [
      'adminbar' => __('Admin bar', 'disable-extras'),
      'banners' => __('Pro / upsell banners', 'disable-extras'),
      'dropin_banners' => __('Drop-in banners', 'disable-extras'),
      'html_comment' => __('HTML comment in footer', 'disable-extras'),
      'metrics' => __('Metrics collection', 'disable-extras'),
      'dashboard_widget' => __('Dashboard widget', 'disable-extras'),
    ],
    'embedpress' => [
      'assets_outside_single' => __('EmbedPress and Plyr CSS/JS outside single', 'disable-extras'),
      'gallery_justify' => __('embedpress-gallery-justify script', 'disable-extras'),
    ],
  ];
}

/**
 * @param mixed $input
 *
 * @return array{
 *   wp: array<string, bool>,
 *   yoast: array<string, bool>,
 *   redis: array<string, bool>,
 *   embedpress: array<string, bool>
 * }
 */
function disable_extras_sanitize_options($input): array {
  $defaults = disable_extras_default_options();
  $current = disable_extras_get_options();
  $output = $defaults;

  if (!is_array($input)) {
    return $current;
  }

  foreach ($defaults as $group => $items) {
    if (!isset($input[$group]) || !is_array($input[$group])) {
      $output[$group] = $current[$group];
      continue;
    }

    foreach ($items as $key => $default) {
      $output[$group][$key] = !empty($input[$group][$key]);
    }
  }

  return $output;
}

/**
 * @return bool
 */
function disable_extras_is_yoast_active(): bool {
  if (defined('WPSEO_VERSION')) {
    return true;
  }

  if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  return is_plugin_active('wordpress-seo/wp-seo.php');
}

/**
 * @return bool
 */
function disable_extras_is_redis_active(): bool {
  if (defined('WP_REDIS_VERSION') || class_exists('\Rhubarb\RedisCache\Plugin', false)) {
    return true;
  }

  if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  return is_plugin_active('redis-cache/redis-cache.php');
}

/**
 * @return bool
 */
function disable_extras_is_embedpress_active(): bool {
  if (defined('EMBEDPRESS_PLUGIN_BASENAME') || defined('EMBEDPRESS_FILE')) {
    return true;
  }

  if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  return is_plugin_active('embedpress/embedpress.php');
}
