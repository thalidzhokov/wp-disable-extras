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
      'thickbox_front' => true,
      'rsd' => false,
      'wlwmanifest' => false,
      'shortlink' => false,
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
      if (!array_key_exists($key, $stored[$group])) {
        $stored[$group][$key] = $default;
        continue;
      }

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
      'jquery_front' => __('jQuery on the front end', 'disable-extras'),
      'thickbox_front' => __('Thickbox on the front end', 'disable-extras'),
      'rsd' => __('RSD link', 'disable-extras'),
      'wlwmanifest' => __('WLW Manifest link', 'disable-extras'),
      'shortlink' => __('Shortlink', 'disable-extras'),
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
 * Examples of output / features removed when the option is enabled.
 *
 * @return array<string, array<string, string>>
 */
function disable_extras_option_code_examples(): array {
  return [
    'wp' => [
      'emoji' => <<<'TXT'
<script src="…/wp-emoji-release.min.js"></script>
<link rel="stylesheet" href="…/emoji.css">
<!-- emoji images instead of native characters in feeds/mail -->
TXT,
      'generator' => <<<'TXT'
<meta name="generator" content="WordPress 6.7.1">
<!-- also in RSS: <generator>https://wordpress.org/?v=6.7.1</generator> -->
TXT,
      'script_versions' => <<<'TXT'
/wp-includes/css/dashicons.min.css?ver=6.7.1
/wp-includes/js/jquery/jquery.min.js?ver=3.7.1
TXT,
      'resource_hints' => <<<'TXT'
<link rel="dns-prefetch" href="//s.w.org">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
TXT,
      'dashicons_guests' => <<<'TXT'
<link rel="stylesheet" href="/wp-includes/css/dashicons.min.css">
<!-- loaded for logged-out visitors on the front end -->
TXT,
      'jquery_front' => <<<'TXT'
<script src="/wp-includes/js/jquery/jquery.min.js"></script>
<script src="/wp-includes/js/jquery/jquery-migrate.min.js"></script>
TXT,
      'thickbox_front' => <<<'TXT'
<script src="/wp-includes/js/thickbox/thickbox.js"></script>
<link rel="stylesheet" href="/wp-includes/js/thickbox/thickbox.css">
TXT,
      'rsd' => <<<'TXT'
<link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://example.com/xmlrpc.php?rsd">
TXT,
      'wlwmanifest' => <<<'TXT'
<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="https://example.com/wp-includes/wlwmanifest.xml">
TXT,
      'shortlink' => <<<'TXT'
<link rel="shortlink" href="https://example.com/?p=123">
<!-- also Link: <https://example.com/?p=123>; rel=shortlink in HTTP headers -->
TXT,
      'xmlrpc' => <<<'TXT'
POST /xmlrpc.php  (pingbacks, remote publishing, app auth)
<link rel="EditURI" ... href=".../xmlrpc.php?rsd">
TXT,
      'oembed' => <<<'TXT'
<link rel="alternate" type="application/json+oembed" href="…/wp-json/oembed/1.0/embed?url=…">
<link rel="alternate" type="text/xml+oembed" href="…">
<!-- /wp-json/oembed/1.0/embed endpoint -->
TXT,
      'rest_users' => <<<'TXT'
GET /wp-json/wp/v2/users
GET /wp-json/wp/v2/users/1
<!-- user enumeration for guests -->
TXT,
      'heartbeat_front' => <<<'TXT'
<script src="/wp-includes/js/heartbeat.min.js"></script>
<!-- admin-ajax.php?action=heartbeat on the front end -->
TXT,
      'self_pingbacks' => <<<'TXT'
<!-- pingback to your own posts when you link them -->
…/xmlrpc.php pingback.ping → https://yoursite.com/…
TXT,
      'auto_updates' => <<<'TXT'
Automatic background updates for:
- WordPress core
- plugins
- themes
- translations
TXT,
      'application_passwords' => <<<'TXT'
Users → Profile → Application Passwords
Authorization: Basic … (REST / XML-RPC app auth)
TXT,
      'core_sitemaps' => <<<'TXT'
/wp-sitemap.xml
/wp-sitemap-posts-post-1.xml
/wp-sitemap-users-1.xml
TXT,
    ],
    'yoast' => [
      'premium_redirects' => <<<'TXT'
Yoast Premium automatic redirect when a post/term slug changes
(e.g. /old-slug/ → /new-slug/)
TXT,
      'premium_notifications' => <<<'TXT'
Admin notices when:
- a post is trashed
- a post/term slug changes
- a term is deleted
TXT,
      'tracking' => <<<'TXT'
Yoast anonymous usage / telemetry data collection
TXT,
      'ai_noise' => <<<'TXT'
Yoast schema blocks in the editor
assessment markers in content
AI Content Planner inline banner
TXT,
      'rss_footer' => <<<'TXT'
The feed for this site by Yoast SEO
<!-- credit line appended to RSS items -->
TXT,
      'adjacent_rel' => <<<'TXT'
<link rel="prev" href="https://example.com/post-1/">
<link rel="next" href="https://example.com/post-3/">
TXT,
      'admin_upsells' => <<<'TXT'
Yoast Premium / upsell admin notices
promotional footer and marketing UI in SEO screens
TXT,
      'integrations_ui' => <<<'TXT'
SEO → Integrations
SEO → Workouts
SEO → Courses / Academy
SEO → Premium licenses screens
TXT,
      'dashboard_widget' => <<<'TXT'
Dashboard widget: “Yoast SEO”
Dashboard widget: “Wincher”
TXT,
    ],
    'redis' => [
      'adminbar' => <<<'TXT'
Admin bar item: Redis / cache flush & metrics
TXT,
      'banners' => <<<'TXT'
Object Cache Pro / Redis Cache upsell banners in Settings
TXT,
      'dropin_banners' => <<<'TXT'
Admin notices about object-cache.php drop-in updates
TXT,
      'html_comment' => <<<'TXT'
<!-- Performance optimized by Redis Object Cache. Learn more: … -->
TXT,
      'metrics' => <<<'TXT'
Redis hit/miss metrics charts and recorded timings
TXT,
      'dashboard_widget' => <<<'TXT'
Dashboard widget: “Redis Object Cache”
TXT,
    ],
    'embedpress' => [
      'assets_outside_single' => <<<'TXT'
<!-- on archives, home, pages (not single posts) -->
<link rel="stylesheet" href="…/embedpress.css">
<link rel="stylesheet" href="…/plyr.css">
<script src="…/plyr.polyfilled.js"></script>
<script src="…/pdfobject.js"></script>
TXT,
      'gallery_justify' => <<<'TXT'
<script src="…/embedpress-gallery-justify.js"></script>
TXT,
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
