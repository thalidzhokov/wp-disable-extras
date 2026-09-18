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
      'emoji' => 'Emoji (скрипты, стили, mail, RSS)',
      'generator' => 'Мета generator и версия в RSS',
      'script_versions' => 'Параметр ver= версии ядра у CSS/JS',
      'resource_hints' => 'DNS prefetch / resource hints',
      'dashicons_guests' => 'Dashicons для неавторизованных',
      'jquery_front' => 'jQuery и Thickbox на фронте',
      'rsd_wlw_shortlink' => 'RSD, WLW Manifest, shortlink',
      'xmlrpc' => 'XML-RPC',
      'oembed' => 'oEmbed (discovery и embed endpoint)',
      'rest_users' => 'REST users для гостей',
      'heartbeat_front' => 'Heartbeat на фронте',
      'self_pingbacks' => 'Self-pingbacks',
      'auto_updates' => 'Автообновления ядра, плагинов и тем',
      'application_passwords' => 'Application Passwords',
      'core_sitemaps' => 'Встроенные sitemaps ядра',
    ],
    'yoast' => [
      'premium_redirects' => 'Premium: авто-редиректы при смене slug',
      'premium_notifications' => 'Premium: уведомления trash / slug / term',
      'tracking' => 'Tracking',
      'ai_noise' => 'AI / assessment markers / schema-блоки',
      'rss_footer' => 'RSS footer',
      'adjacent_rel' => 'Adjacent rel links',
      'admin_upsells' => 'Upsell / promotions в админке',
      'integrations_ui' => 'Пункты Integrations / Workouts / Courses',
      'dashboard_widget' => 'Виджеты Yoast / Wincher на Dashboard',
    ],
    'redis' => [
      'adminbar' => 'Admin bar',
      'banners' => 'Pro / upsell баннеры',
      'dropin_banners' => 'Баннеры drop-in',
      'html_comment' => 'HTML-комментарий в footer',
      'metrics' => 'Сбор metrics',
      'dashboard_widget' => 'Dashboard widget',
    ],
    'embedpress' => [
      'assets_outside_single' => 'CSS/JS EmbedPress и Plyr вне single',
      'gallery_justify' => 'Скрипт embedpress-gallery-justify',
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
