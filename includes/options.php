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
      'dns_prefetch' => true,
      'resource_hints' => true,
      'dashicons_guests' => true,
      'jquery_front' => true,
      'thickbox_front' => true,
      'rsd' => false,
      'wlwmanifest' => false,
      'shortlink' => false,
      'xmlrpc' => false,
      'self_pingbacks' => false,
      'pingbacks_outgoing' => false,
      'pingbacks_incoming' => false,
      'oembed_discovery' => false,
      'oembed_endpoint' => false,
      'rest_users' => false,
      'heartbeat_front' => false,
      'auto_updates_core' => false,
      'auto_updates_plugins' => false,
      'auto_updates_themes' => false,
      'auto_updates_translations' => false,
      'auto_updates_all' => false,
      'application_passwords' => false,
      'core_sitemaps' => false,
      'disallow_file_edit' => false,
      'disallow_file_mods' => false,
      'disable_wp_cron' => false,
      'disallow_unfiltered_html' => false,
      'post_revisions' => false,
      'empty_trash' => false,
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
      'dns_prefetch' => __('DNS prefetch', 'disable-extras'),
      'resource_hints' => __('Resource hints (preconnect, prefetch, prerender)', 'disable-extras'),
      'dashicons_guests' => __('Dashicons for logged-out users', 'disable-extras'),
      'jquery_front' => __('jQuery on the front end', 'disable-extras'),
      'thickbox_front' => __('Thickbox on the front end', 'disable-extras'),
      'rsd' => __('RSD link', 'disable-extras'),
      'wlwmanifest' => __('WLW Manifest link', 'disable-extras'),
      'shortlink' => __('Shortlink', 'disable-extras'),
      'xmlrpc' => __('XML-RPC', 'disable-extras'),
      'self_pingbacks' => __('Self-pingbacks', 'disable-extras'),
      'pingbacks_outgoing' => __('Outgoing pingbacks', 'disable-extras'),
      'pingbacks_incoming' => __('Incoming pingbacks', 'disable-extras'),
      'oembed_discovery' => __('oEmbed discovery links', 'disable-extras'),
      'oembed_endpoint' => __('oEmbed REST endpoint', 'disable-extras'),
      'rest_users' => __('REST users endpoint for guests', 'disable-extras'),
      'heartbeat_front' => __('Heartbeat on the front end', 'disable-extras'),
      'auto_updates_core' => __('Automatic updates: WordPress core', 'disable-extras'),
      'auto_updates_plugins' => __('Automatic updates: plugins', 'disable-extras'),
      'auto_updates_themes' => __('Automatic updates: themes', 'disable-extras'),
      'auto_updates_translations' => __('Automatic updates: translations', 'disable-extras'),
      'auto_updates_all' => __('Automatic updates: everything', 'disable-extras'),
      'application_passwords' => __('Application Passwords', 'disable-extras'),
      'core_sitemaps' => __('Core sitemaps', 'disable-extras'),
      'disallow_file_edit' => __('Theme and plugin file editor', 'disable-extras'),
      'disallow_file_mods' => __('Install / update / edit plugins and themes', 'disable-extras'),
      'disable_wp_cron' => __('Built-in WP-Cron', 'disable-extras'),
      'disallow_unfiltered_html' => __('Unfiltered HTML for admins/editors', 'disable-extras'),
      'post_revisions' => __('Post revisions', 'disable-extras'),
      'empty_trash' => __('Trash (immediate permanent delete)', 'disable-extras'),
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
 * Logical sections for settings UI (order of keys = order in UI).
 *
 * @return array<string, list<array{title: string, keys: list<string>}>>
 */
function disable_extras_option_sections(): array {
  return [
    'wp' => [
      [
        'title' => __('Head & discovery', 'disable-extras'),
        'keys' => [
          'emoji',
          'generator',
          'script_versions',
          'dns_prefetch',
          'resource_hints',
          'rsd',
          'wlwmanifest',
          'shortlink',
          'oembed_discovery',
          'oembed_endpoint',
        ],
      ],
      [
        'title' => __('Front-end scripts', 'disable-extras'),
        'keys' => [
          'dashicons_guests',
          'jquery_front',
          'thickbox_front',
          'heartbeat_front',
        ],
      ],
      [
        'title' => __('API & remote access', 'disable-extras'),
        'keys' => [
          'self_pingbacks',
          'pingbacks_outgoing',
          'pingbacks_incoming',
          'xmlrpc',
          'rest_users',
          'application_passwords',
          'core_sitemaps',
        ],
      ],
      [
        'title' => __('Updates', 'disable-extras'),
        'keys' => [
          'auto_updates_core',
          'auto_updates_plugins',
          'auto_updates_themes',
          'auto_updates_translations',
          'auto_updates_all',
        ],
      ],
      [
        'title' => __('Security & administration', 'disable-extras'),
        'keys' => [
          'disallow_file_edit',
          'disallow_file_mods',
          'disable_wp_cron',
          'disallow_unfiltered_html',
        ],
      ],
      [
        'title' => __('Content storage', 'disable-extras'),
        'keys' => [
          'post_revisions',
          'empty_trash',
        ],
      ],
    ],
    'yoast' => [
      [
        'title' => __('Premium redirects & notices', 'disable-extras'),
        'keys' => [
          'premium_redirects',
          'premium_notifications',
        ],
      ],
      [
        'title' => __('SEO output', 'disable-extras'),
        'keys' => [
          'tracking',
          'ai_noise',
          'rss_footer',
          'adjacent_rel',
        ],
      ],
      [
        'title' => __('Admin UI', 'disable-extras'),
        'keys' => [
          'admin_upsells',
          'integrations_ui',
          'dashboard_widget',
        ],
      ],
    ],
    'redis' => [
      [
        'title' => __('UI & metrics', 'disable-extras'),
        'keys' => [
          'adminbar',
          'banners',
          'dropin_banners',
          'html_comment',
          'metrics',
          'dashboard_widget',
        ],
      ],
    ],
    'embedpress' => [
      [
        'title' => __('Assets', 'disable-extras'),
        'keys' => [
          'assets_outside_single',
          'gallery_justify',
        ],
      ],
    ],
  ];
}

/**
 * Native constants that disable the same thing (when they exist).
 *
 * @return array<string, array<string, list<string>>>
 */
function disable_extras_option_constants(): array {
  return [
    'wp' => [
      'auto_updates_core' => [
        "define('WP_AUTO_UPDATE_CORE', false);",
      ],
      'auto_updates_all' => [
        "define('AUTOMATIC_UPDATER_DISABLED', true);",
      ],
      'disallow_file_edit' => [
        "define('DISALLOW_FILE_EDIT', true);",
      ],
      'disallow_file_mods' => [
        "define('DISALLOW_FILE_MODS', true);",
      ],
      'disable_wp_cron' => [
        "define('DISABLE_WP_CRON', true);",
      ],
      'disallow_unfiltered_html' => [
        "define('DISALLOW_UNFILTERED_HTML', true);",
      ],
      'post_revisions' => [
        "define('WP_POST_REVISIONS', false);",
      ],
      'empty_trash' => [
        "define('EMPTY_TRASH_DAYS', 0);",
      ],
    ],
    'yoast' => [],
    'redis' => [
      'adminbar' => [
        "define('WP_REDIS_DISABLE_ADMINBAR', true);",
      ],
      'banners' => [
        "define('WP_REDIS_DISABLE_BANNERS', true);",
      ],
      'dropin_banners' => [
        "define('WP_REDIS_DISABLE_DROPIN_BANNERS', true);",
      ],
      'html_comment' => [
        "define('WP_REDIS_DISABLE_COMMENT', true);",
      ],
      'metrics' => [
        "define('WP_REDIS_DISABLE_METRICS', true);",
      ],
    ],
    'embedpress' => [],
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
      'dns_prefetch' => <<<'TXT'
<link rel="dns-prefetch" href="//s.w.org">
<link rel="dns-prefetch" href="//fonts.googleapis.com">
TXT,
      'resource_hints' => <<<'TXT'
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="prefetch" href="https://example.com/next-page/">
<link rel="prerender" href="https://example.com/next-page/">
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
POST /xmlrpc.php  (remote publishing, app auth, all XML-RPC methods)
<link rel="EditURI" ... href=".../xmlrpc.php?rsd">
TXT,
      'self_pingbacks' => <<<'TXT'
<!-- outbound pingback to your own posts when you link them -->
.../xmlrpc.php pingback.ping → https://yoursite.com/...
TXT,
      'pingbacks_outgoing' => <<<'TXT'
Outgoing pingbacks/trackbacks to other sites on publish
.../xmlrpc.php pingback.ping → https://other-site.com/...
TXT,
      'pingbacks_incoming' => <<<'TXT'
Incoming: pingback.ping via XML-RPC
X-Pingback: https://example.com/xmlrpc.php
Discussion → allow pingbacks on posts
TXT,
      'oembed_discovery' => <<<'TXT'
<link rel="alternate" type="application/json+oembed" href="https://example.com/wp-json/oembed/1.0/embed?url=...">
<link rel="alternate" type="text/xml+oembed" href="...">
<script src="/wp-includes/js/wp-embed.min.js"></script>
TXT,
      'oembed_endpoint' => <<<'TXT'
GET /wp-json/oembed/1.0/embed?url=https://example.com/post/
GET /wp-json/oembed/1.0/proxy?url=...
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
      'auto_updates_core' => <<<'TXT'
Dashboard → Updates → automatic WordPress core updates
(minor/major background core upgrades)
TXT,
      'auto_updates_plugins' => <<<'TXT'
Plugins → auto-update toggle / background plugin updates
TXT,
      'auto_updates_themes' => <<<'TXT'
Appearance → Themes → automatic theme updates
TXT,
      'auto_updates_translations' => <<<'TXT'
Automatic language pack / translation file updates
TXT,
      'auto_updates_all' => <<<'TXT'
All background automatic updates:
- WordPress core
- plugins
- themes
- translations
TXT,
      'application_passwords' => <<<'TXT'
Users → Profile → Application Passwords
Authorization: Basic ... (REST / XML-RPC app auth)
TXT,
      'core_sitemaps' => <<<'TXT'
/wp-sitemap.xml
/wp-sitemap-posts-post-1.xml
/wp-sitemap-users-1.xml
TXT,
      'disallow_file_edit' => <<<'TXT'
Appearance → Theme File Editor
Plugins → Plugin File Editor
TXT,
      'disallow_file_mods' => <<<'TXT'
Plugins → Add New / Update
Appearance → Themes → Add New / Update
inline plugin/theme installers in admin
TXT,
      'disable_wp_cron' => <<<'TXT'
Built-in pseudo-cron via front/admin requests
(wp-cron.php spawned on page load)
TXT,
      'disallow_unfiltered_html' => <<<'TXT'
Administrator / Editor capability: unfiltered_html
(raw script/iframe in post content without kses)
TXT,
      'post_revisions' => <<<'TXT'
Post revisions in the editor sidebar
wp_posts rows with post_type = revision
TXT,
      'empty_trash' => <<<'TXT'
Posts / media / comments Trash
(items deleted immediately, no restore period)
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
