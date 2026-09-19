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
      'embeds' => false,
      'rest_users' => false,
      'rest_guests' => false,
      'rest_links' => false,
      'heartbeat_front' => false,
      'heartbeat_admin' => false,
      'heartbeat_slow' => false,
      'texturization' => false,
      'autosave' => false,
      'dashboard_right_now' => false,
      'dashboard_network_right_now' => false,
      'dashboard_activity' => false,
      'dashboard_quick_press' => false,
      'dashboard_primary' => false,
      'dashboard_site_health' => false,
      'welcome_panel' => false,
      'admin_bar' => false,
      'admin_bar_non_admins' => false,
      'admin_bar_wp_logo' => false,
      'login_logo' => false,
      'feed_redirect' => false,
      'feed_global' => false,
      'feed_global_comments' => false,
      'feed_post_comments' => false,
      'feed_authors' => false,
      'feed_post_types' => false,
      'feed_categories' => false,
      'feed_tags' => false,
      'feed_custom_taxonomies' => false,
      'feed_search' => false,
      'feed_atom_rdf' => false,
      'auto_updates_core' => false,
      'auto_updates_plugins' => false,
      'auto_updates_themes' => false,
      'auto_updates_translations' => false,
      'auto_updates_all' => false,
      'update_nags_admins_only' => false,
      'update_phone_home' => false,
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
      'schema_blocks' => false,
      'assessment_markers' => false,
      'rss_footer' => false,
      'adjacent_rel' => false,
      'admin_upsells' => false,
      'menu_integrations' => false,
      'menu_workouts' => false,
      'menu_courses' => false,
      'menu_academy' => false,
      'menu_licenses' => false,
      'menu_redirects' => false,
      'dashboard_yoast' => false,
      'dashboard_wincher' => false,
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
      'embeds' => __('WordPress embeds (oEmbed host JS, embed endpoints)', 'disable-extras'),
      'rest_users' => __('REST users endpoint for guests', 'disable-extras'),
      'rest_guests' => __('REST API for guests', 'disable-extras'),
      'rest_links' => __('REST API discovery links (head, headers, RSD)', 'disable-extras'),
      'heartbeat_front' => __('Heartbeat on the front end', 'disable-extras'),
      'heartbeat_admin' => __('Heartbeat in admin', 'disable-extras'),
      'heartbeat_slow' => __('Heartbeat interval: slow (60s)', 'disable-extras'),
      'texturization' => __('Texturization (smart quotes, dashes, ellipsis)', 'disable-extras'),
      'autosave' => __('Editor autosave', 'disable-extras'),
      'dashboard_right_now' => __('Dashboard widget: “At a Glance”', 'disable-extras'),
      'dashboard_network_right_now' => __('Network Dashboard widget: “Right Now”', 'disable-extras'),
      'dashboard_activity' => __('Dashboard widget: “Activity”', 'disable-extras'),
      'dashboard_quick_press' => __('Dashboard widget: “Quick Draft”', 'disable-extras'),
      'dashboard_primary' => __('Dashboard widget: “WordPress Events and News”', 'disable-extras'),
      'dashboard_site_health' => __('Dashboard widget: “Site Health Status”', 'disable-extras'),
      'welcome_panel' => __('Dashboard: Welcome panel', 'disable-extras'),
      'admin_bar' => __('Admin bar on the front end (all users)', 'disable-extras'),
      'admin_bar_non_admins' => __('Admin bar on the front end (non-admins)', 'disable-extras'),
      'admin_bar_wp_logo' => __('Admin bar: WordPress logo menu', 'disable-extras'),
      'login_logo' => __('Login: WordPress logo → blog name', 'disable-extras'),
      'feed_redirect' => __('Redirect disabled feeds to the homepage', 'disable-extras'),
      'feed_global' => __('Global posts feed', 'disable-extras'),
      'feed_global_comments' => __('Global comments feed', 'disable-extras'),
      'feed_post_comments' => __('Per-post comments feed', 'disable-extras'),
      'feed_authors' => __('Author feeds', 'disable-extras'),
      'feed_post_types' => __('Post type archive feeds', 'disable-extras'),
      'feed_categories' => __('Category feeds', 'disable-extras'),
      'feed_tags' => __('Tag feeds', 'disable-extras'),
      'feed_custom_taxonomies' => __('Custom taxonomy feeds', 'disable-extras'),
      'feed_search' => __('Search results feeds', 'disable-extras'),
      'feed_atom_rdf' => __('Atom/RDF feed formats', 'disable-extras'),
      'auto_updates_core' => __('Automatic updates: WordPress core', 'disable-extras'),
      'auto_updates_plugins' => __('Automatic updates: plugins', 'disable-extras'),
      'auto_updates_themes' => __('Automatic updates: themes', 'disable-extras'),
      'auto_updates_translations' => __('Automatic updates: translations', 'disable-extras'),
      'auto_updates_all' => __('Automatic updates: everything', 'disable-extras'),
      'update_nags_admins_only' => __('Update nags only for users who can update', 'disable-extras'),
      'update_phone_home' => __('Site URL in WordPress.org update requests', 'disable-extras'),
      'application_passwords' => __('Application Passwords', 'disable-extras'),
      'core_sitemaps' => __('Core sitemaps', 'disable-extras'),
      'disallow_file_edit' => __('Theme and plugin file editor', 'disable-extras'),
      'disallow_file_mods' => __('Install/update/edit plugins and themes', 'disable-extras'),
      'disable_wp_cron' => __('Built-in WP-Cron', 'disable-extras'),
      'disallow_unfiltered_html' => __('Unfiltered HTML for admins/editors', 'disable-extras'),
      'post_revisions' => __('Post revisions', 'disable-extras'),
      'empty_trash' => __('Trash', 'disable-extras'),
    ],
    'yoast' => [
      'premium_redirects' => __('Premium: auto-redirects on slug change', 'disable-extras'),
      'premium_notifications' => __('Premium: trash / slug / term notifications', 'disable-extras'),
      'tracking' => __('Tracking', 'disable-extras'),
      'schema_blocks' => __('Schema blocks in the editor', 'disable-extras'),
      'assessment_markers' => __('Assessment markers in content', 'disable-extras'),
      'rss_footer' => __('RSS footer («appeared first on…»)', 'disable-extras'),
      'adjacent_rel' => __('Adjacent rel links', 'disable-extras'),
      'admin_upsells' => __('Upsells / promotions', 'disable-extras'),
      'menu_integrations' => __('Menu: Integrations', 'disable-extras'),
      'menu_workouts' => __('Menu: Workouts', 'disable-extras'),
      'menu_courses' => __('Menu: Courses', 'disable-extras'),
      'menu_academy' => __('Menu: Academy', 'disable-extras'),
      'menu_licenses' => __('Menu: Licenses', 'disable-extras'),
      'menu_redirects' => __('Menu: Redirects', 'disable-extras'),
      'dashboard_yoast' => __('Dashboard widget: Yoast SEO', 'disable-extras'),
      'dashboard_wincher' => __('Dashboard widget: Wincher', 'disable-extras'),
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
          'rest_links',
        ],
      ],
      [
        'title' => __('Editor', 'disable-extras'),
        'keys' => [
          'texturization',
          'autosave',
        ],
      ],
      [
        'title' => __('Front-end scripts', 'disable-extras'),
        'keys' => [
          'dashicons_guests',
          'jquery_front',
          'thickbox_front',
          'embeds',
          'heartbeat_front',
          'heartbeat_admin',
          'heartbeat_slow',
        ],
      ],
      [
        'title' => __('Admin bar & dashboard', 'disable-extras'),
        'keys' => [
          'admin_bar',
          'admin_bar_non_admins',
          'admin_bar_wp_logo',
          'dashboard_right_now',
          'dashboard_network_right_now',
          'dashboard_activity',
          'dashboard_quick_press',
          'dashboard_primary',
          'dashboard_site_health',
          'welcome_panel',
        ],
      ],
      [
        'title' => __('Login', 'disable-extras'),
        'keys' => [
          'login_logo',
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
          'rest_guests',
          'application_passwords',
          'core_sitemaps',
        ],
      ],
      [
        'title' => __('Feeds', 'disable-extras'),
        'keys' => [
          'feed_redirect',
          'feed_global',
          'feed_global_comments',
          'feed_post_comments',
          'feed_authors',
          'feed_post_types',
          'feed_categories',
          'feed_tags',
          'feed_custom_taxonomies',
          'feed_search',
          'feed_atom_rdf',
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
          'update_nags_admins_only',
          'update_phone_home',
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
          'schema_blocks',
          'assessment_markers',
          'rss_footer',
          'adjacent_rel',
        ],
      ],
      [
        'title' => __('Admin UI', 'disable-extras'),
        'keys' => [
          'admin_upsells',
          'menu_integrations',
          'menu_workouts',
          'menu_courses',
          'menu_academy',
          'menu_licenses',
          'menu_redirects',
          'dashboard_yoast',
          'dashboard_wincher',
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
 * Hints for settings UI.
 *
 * Keys per option (all optional — omit key and heading is skipped):
 * - what: string|list — removed markup/behavior; list items "or" / "and_also" are separators
 * - where: string|list — page / screen / taxonomy / editor; same separators
 * - constant: string — wp-config-style define() example
 *
 * @return array<string, array<string, array{
 *   what?: string|list<string>,
 *   where?: string|list<string>,
 *   constant?: string
 * }>>
 */
function disable_extras_option_hints(): array {
  return [
    'wp' => [
      'emoji' => [
        'what' => [
          <<<'TXT'
<script src="…/wp-emoji-release.min.js"></script>
<link rel="stylesheet" href="…/emoji.css">
TXT,
          'and_also',
          'emoji images instead of native characters in feeds/mail',
        ],
        'where' => [
          'Front end <head>',
          'and_also',
          'RSS/Atom feeds',
          'and_also',
          'Outgoing mail (wp_mail)',
        ],
      ],
      'generator' => [
        'what' => [
          '<meta name="generator" content="WordPress 6.7.1">',
          'and_also',
          '<generator>https://wordpress.org/?v=6.7.1</generator>',
        ],
        'where' => [
          'Front end <head>',
          'and_also',
          'RSS/Atom feeds',
        ],
      ],
      'script_versions' => [
        'what' => <<<'TXT'
/wp-includes/css/dashicons.min.css?ver=6.7.1
/wp-includes/js/jquery/jquery.min.js?ver=3.7.1
TXT,
        'where' => 'Front end and wp-admin (core CSS/JS URLs)',
      ],
      'dns_prefetch' => [
        'what' => <<<'TXT'
<link rel="dns-prefetch" href="//s.w.org">
<link rel="dns-prefetch" href="//fonts.googleapis.com">
TXT,
        'where' => 'Front end <head>',
      ],
      'resource_hints' => [
        'what' => <<<'TXT'
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="prefetch" href="https://example.com/next-page/">
<link rel="prerender" href="https://example.com/next-page/">
TXT,
        'where' => 'Front end <head>',
      ],
      'dashicons_guests' => [
        'what' => '<link rel="stylesheet" href="/wp-includes/css/dashicons.min.css">',
        'where' => 'Front end for logged-out visitors',
      ],
      'jquery_front' => [
        'what' => <<<'TXT'
<script src="/wp-includes/js/jquery/jquery.min.js"></script>
<script src="/wp-includes/js/jquery/jquery-migrate.min.js"></script>
TXT,
        'where' => 'Front end',
      ],
      'thickbox_front' => [
        'what' => <<<'TXT'
<script src="/wp-includes/js/thickbox/thickbox.js"></script>
<link rel="stylesheet" href="/wp-includes/js/thickbox/thickbox.css">
TXT,
        'where' => 'Front end',
      ],
      'rsd' => [
        'what' => '<link rel="EditURI" type="application/rsd+xml" title="RSD" href="https://example.com/xmlrpc.php?rsd">',
        'where' => 'Front end <head>',
      ],
      'wlwmanifest' => [
        'what' => '<link rel="wlwmanifest" type="application/wlwmanifest+xml" href="https://example.com/wp-includes/wlwmanifest.xml">',
        'where' => 'Front end <head>',
      ],
      'shortlink' => [
        'what' => [
          '<link rel="shortlink" href="https://example.com/?p=123">',
          'and_also',
          'Link: <https://example.com/?p=123>; rel=shortlink',
        ],
        'where' => [
          'Front end <head>',
          'and_also',
          'HTTP response headers',
        ],
      ],
      'xmlrpc' => [
        'what' => [
          'POST /xmlrpc.php',
          'and_also',
          '<link rel="EditURI" … href="…/xmlrpc.php?rsd">',
        ],
        'where' => [
          '/xmlrpc.php',
          'and_also',
          'Front end <head> (RSD)',
        ],
      ],
      'self_pingbacks' => [
        'what' => 'pingback.ping → own site URL when a post links to another own post',
        'where' => 'On publish / update (outbound pingbacks)',
      ],
      'pingbacks_outgoing' => [
        'what' => 'pingback.ping / trackback → other sites',
        'where' => 'On publish / update',
      ],
      'pingbacks_incoming' => [
        'what' => [
          'pingback.ping via XML-RPC',
          'and_also',
          'X-Pingback: https://example.com/xmlrpc.php',
        ],
        'where' => [
          '/xmlrpc.php',
          'and_also',
          'HTTP headers',
          'and_also',
          'Discussion settings on posts',
        ],
      ],
      'oembed_discovery' => [
        'what' => <<<'TXT'
<link rel="alternate" type="application/json+oembed" href="…">
<link rel="alternate" type="text/xml+oembed" href="…">
<script src="/wp-includes/js/wp-embed.min.js"></script>
TXT,
        'where' => 'Front end <head> (singular content)',
      ],
      'oembed_endpoint' => [
        'what' => <<<'TXT'
GET /wp-json/oembed/1.0/embed?url=…
GET /wp-json/oembed/1.0/proxy?url=…
TXT,
        'where' => 'REST API',
      ],
      'embeds' => [
        'what' => [
          '/embed/ rewrite + is_embed() templates',
          'and_also',
          '<script src="/wp-includes/js/wp-embed.min.js"></script>',
          'and_also',
          'oEmbed discovery links and REST oEmbed routes',
        ],
        'where' => [
          'Front end',
          'and_also',
          'REST API',
          'and_also',
          'Embed templates',
        ],
      ],
      'rest_users' => [
        'what' => <<<'TXT'
GET /wp-json/wp/v2/users
GET /wp-json/wp/v2/users/1
TXT,
        'where' => 'REST API for logged-out visitors',
      ],
      'rest_guests' => [
        'what' => 'GET /wp-json/… → 401 for logged-out visitors',
        'where' => 'REST API',
      ],
      'rest_links' => [
        'what' => [
          '<link rel="https://api.w.org/" href="https://example.com/wp-json/">',
          'and_also',
          'Link: <https://example.com/wp-json/>; rel="https://api.w.org/"',
          'and_also',
          'REST API entry in RSD',
        ],
        'where' => [
          'Front end <head>',
          'and_also',
          'HTTP response headers',
          'and_also',
          'RSD',
        ],
      ],
      'heartbeat_front' => [
        'what' => <<<'TXT'
<script src="/wp-includes/js/heartbeat.min.js"></script>
admin-ajax.php?action=heartbeat
TXT,
        'where' => 'Front end',
      ],
      'heartbeat_admin' => [
        'what' => <<<'TXT'
<script src="/wp-includes/js/heartbeat.min.js"></script>
admin-ajax.php?action=heartbeat
TXT,
        'where' => 'wp-admin',
      ],
      'heartbeat_slow' => [
        'what' => 'heartbeat_settings.interval = 60 (instead of ~15s)',
        'where' => 'Front end and wp-admin (where Heartbeat runs)',
      ],
      'texturization' => [
        'what' => [
          'Conversion of "quotes" into “quotes”',
          'and_also',
          '-- → —',
          'and_also',
          '... → …',
        ],
        'where' => 'Content, titles, excerpts, comments, feeds',
      ],
      'autosave' => [
        'what' => '<script src="/wp-includes/js/autosave.min.js"></script>',
        'where' => 'Block / classic editor (post screens in wp-admin)',
      ],
      'dashboard_right_now' => [
        'what' => 'Dashboard widget: “At a Glance”',
        'where' => 'wp-admin > Dashboard',
      ],
      'dashboard_network_right_now' => [
        'what' => 'Network Dashboard widget: “Right Now”',
        'where' => 'Network Admin > Dashboard',
      ],
      'dashboard_activity' => [
        'what' => 'Dashboard widget: “Activity”',
        'where' => 'wp-admin > Dashboard',
      ],
      'dashboard_quick_press' => [
        'what' => 'Dashboard widget: “Quick Draft”',
        'where' => 'wp-admin > Dashboard',
      ],
      'dashboard_primary' => [
        'what' => 'Dashboard widget: “WordPress Events and News”',
        'where' => 'wp-admin > Dashboard',
      ],
      'dashboard_site_health' => [
        'what' => 'Dashboard widget: “Site Health Status”',
        'where' => 'wp-admin > Dashboard',
      ],
      'welcome_panel' => [
        'what' => 'Welcome panel',
        'where' => 'wp-admin > Dashboard',
      ],
      'admin_bar' => [
        'what' => '#wpadminbar',
        'where' => 'Front end for every logged-in user',
      ],
      'admin_bar_non_admins' => [
        'what' => '#wpadminbar',
        'where' => 'Front end for users without manage_options',
      ],
      'admin_bar_wp_logo' => [
        'label_note' => 'Recommended — less clutter',
        'label_note_tone' => 'ok',
        'summary' => 'Disables the Admin bar item <code>#wp-admin-bar-wp-logo</code> About WordPress (Documentation, Learn, Support, Feedback)',
      ],
      'login_logo' => [
        'what' => <<<'TXT'
<h1 class="wp-login-logo"><a href="https://wordpress.org/">…</a></h1>
TXT,
        'where' => 'wp-login.php (replaced with blog name text, no link)',
      ],
      'feed_redirect' => [
        'what' => 'GET /feed/ → 301 Location: https://example.com/',
        'where' => 'Disabled feed URLs (when other feed options are on)',
      ],
      'feed_global' => [
        'what' => [
          '/feed/, /rss/, /rss2/, /atom/, /rdf/',
          'and_also',
          '<link rel="alternate" type="application/rss+xml" …>',
        ],
        'where' => [
          'Global post feeds',
          'and_also',
          'Front end <head>',
        ],
      ],
      'feed_global_comments' => [
        'what' => [
          '/comments/feed/',
          'and_also',
          '<link rel="alternate" … comments feed>',
        ],
        'where' => [
          'Global comments feed',
          'and_also',
          'Front end <head>',
        ],
      ],
      'feed_post_comments' => [
        'what' => <<<'TXT'
/post-slug/feed/
/post-slug/comments/feed/
TXT,
        'where' => 'Single post comment feeds',
      ],
      'feed_authors' => [
        'what' => '/author/name/feed/',
        'where' => 'Author archives',
      ],
      'feed_post_types' => [
        'what' => '/cpt-slug/feed/',
        'where' => 'Custom post type archives',
      ],
      'feed_categories' => [
        'what' => '/category/news/feed/',
        'where' => 'Category taxonomy archives',
      ],
      'feed_tags' => [
        'what' => '/tag/esports/feed/',
        'where' => 'Post tag taxonomy archives',
      ],
      'feed_custom_taxonomies' => [
        'what' => '/taxonomy-slug/term/feed/',
        'where' => 'Custom taxonomy archives',
      ],
      'feed_search' => [
        'what' => '/?s=query&feed=rss2',
        'where' => 'Search results',
      ],
      'feed_atom_rdf' => [
        'what' => '/feed/atom/, /feed/rdf/, /feed/rss/',
        'where' => 'Feed format endpoints (RSS2 stays unless other feed options apply)',
      ],
      'auto_updates_core' => [
        'what' => 'Automatic WordPress core updates (minor/major)',
        'where' => 'Background updater / Dashboard > Updates',
        'constant' => "define('WP_AUTO_UPDATE_CORE', false);",
      ],
      'auto_updates_plugins' => [
        'what' => 'Automatic plugin updates',
        'where' => 'Plugins list / background updater',
      ],
      'auto_updates_themes' => [
        'what' => 'Automatic theme updates',
        'where' => 'Appearance > Themes / background updater',
      ],
      'auto_updates_translations' => [
        'what' => 'Automatic language pack / translation updates',
        'where' => 'Background updater',
      ],
      'auto_updates_all' => [
        'label_note' => 'Useful with manual/controlled deploys; not if you rely on auto security patches',
        'label_note_tone' => 'muted',
        'summary' => [
          'Disables all background automatic updates: WordPress core, plugins, themes, translations',
        ],
        'constant' => "define('AUTOMATIC_UPDATER_DISABLED', true);",
      ],
      'update_nags_admins_only' => [
        'what' => 'Admin notice: “WordPress X.Y is available! Please update…”',
        'where' => 'wp-admin (users without update_core)',
      ],
      'update_phone_home' => [
        'what' => <<<'TXT'
User-Agent: WordPress/6.7; https://example.com/
Headers: wp_blog / wp_install = site URL
TXT,
        'where' => 'Outbound checks to api.wordpress.org',
      ],
      'application_passwords' => [
        'what' => [
          'Users > Profile > Application Passwords',
          'and_also',
          'Authorization: Basic … (REST / XML-RPC app auth)',
        ],
        'where' => [
          'wp-admin > Users > Profile',
          'and_also',
          'REST API / XML-RPC',
        ],
      ],
      'core_sitemaps' => [
        'what' => <<<'TXT'
/wp-sitemap.xml
/wp-sitemap-posts-post-1.xml
/wp-sitemap-users-1.xml
TXT,
        'where' => 'Front end sitemap endpoints',
      ],
      'disallow_file_edit' => [
        'label_note' => 'Recommended for better security',
        'label_note_tone' => 'ok',
        'summary' => 'Disables the theme and plugin file editors in wp-admin: <code>Appearance > Theme File Editor</code> and <code>Plugins > Plugin File Editor</code>',
        'constant' => "define('DISALLOW_FILE_EDIT', true);",
      ],
      'disallow_file_mods' => [
        'label_note' => 'Useful on production with controlled deploys',
        'label_note_tone' => 'muted',
        'summary' => [
          'Disables installing and updating plugins and themes in wp-admin: <code>Plugins > Add New / Update</code> and <code>Appearance > Themes > Add New / Update</code>',
          'Updates are still possible via WP-CLI or by replacing files on the server, not from wp-admin',
        ],
        'constant' => "define('DISALLOW_FILE_MODS', true);",
      ],
      'disable_wp_cron' => [
        'label_note' => 'Useful if you set up a system cron',
        'label_note_tone' => 'muted',
        'summary' => [
          'Disables built-in WP-Cron on page load',
          'Tasks from <code>wp_schedule_event()</code> and <code>wp_schedule_single_event()</code> stay queued and wait; there is no auto-run on page load',
          'To run manually, call <code>wp-cron.php</code>, e.g. <code>php /path/to/wordpress/wp-cron.php</code>',
        ],
        'constant' => "define('DISABLE_WP_CRON', true);",
      ],
      'disallow_unfiltered_html' => [
        'label_note' => 'Recommended for better security',
        'label_note_tone' => 'ok',
        'summary' => 'Disables the ability for Administrators and Editors to insert raw code such as <code>script</code>, <code>iframe</code> without kses in the post/page editor',
        'constant' => "define('DISALLOW_UNFILTERED_HTML', true);",
      ],
      'post_revisions' => [
        'label_note' => 'Not recommended',
        'summary' => 'Disables the revisions UI in the block/classic editor sidebar and deletes posts with <code>post_type = revision</code> from the <code>wp_posts</code> table',
        'constant' => "define('WP_POST_REVISIONS', false);",
      ],
      'empty_trash' => [
        'label_note' => 'Permanent deletion without trash is not recommended',
        'summary' => 'Disables the trash for posts/media/comments: items are permanently deleted immediately in wp-admin content lists',
        'constant' => "define('EMPTY_TRASH_DAYS', 0);",
      ],
    ],
    'yoast' => [
      'premium_redirects' => [
        'what' => 'Automatic redirect /old-slug/ → /new-slug/',
        'where' => 'When a post or term slug changes (Yoast Premium)',
      ],
      'premium_notifications' => [
        'what' => [
          'Notice when a post is trashed',
          'or',
          'Notice when a post/term slug changes',
          'or',
          'Notice when a term is deleted',
        ],
        'where' => 'wp-admin',
      ],
      'tracking' => [
        'what' => 'Anonymous usage / telemetry data',
        'where' => 'Outbound to Yoast',
      ],
      'schema_blocks' => [
        'what' => 'FAQ, How-to, and other Yoast schema blocks',
        'where' => 'Block editor',
      ],
      'assessment_markers' => [
        'what' => 'SEO / readability highlight markers in content',
        'where' => 'Block / classic editor (post content)',
      ],
      'rss_footer' => [
        'what' => [
          'The post <a href="…">Title</a> appeared first on <a href="…">Blog</a>.',
          'or',
          'Сообщение … появились сначала на …',
        ],
        'where' => 'RSS/Atom feed items (rssbefore / rssafter)',
      ],
      'adjacent_rel' => [
        'what' => <<<'TXT'
<link rel="prev" href="https://example.com/post-1/">
<link rel="next" href="https://example.com/post-3/">
TXT,
        'where' => 'Front end <head> on singular posts',
      ],
      'admin_upsells' => [
        'what' => [
          'Admin notices (wpseo_admin_notices)',
          'and_also',
          'Promotional footer (wpseo_admin_footer)',
          'and_also',
          'AI Content Planner banner',
          'and_also',
          <<<'TXT'
#premium-seo-analysis-upsell-ad-sidebar
#premium-seo-analysis-upsell-ad-metabox
#premium-seo-analysis-upsell-ad-elementor
TXT,
          'and_also',
          'Yoast > Upgrade',
          'and_also',
          'Yoast > AI Brand Insights',
        ],
        'where' => [
          'wp-admin notices / Yoast screens',
          'and_also',
          'Post editor (block / classic / Elementor)',
          'and_also',
          'Yoast admin menu',
        ],
      ],
      'menu_integrations' => [
        'what' => 'Yoast > Integrations',
        'where' => 'wp-admin menu',
      ],
      'menu_workouts' => [
        'what' => 'Yoast > Workouts',
        'where' => 'wp-admin menu',
      ],
      'menu_courses' => [
        'what' => 'Yoast > Courses',
        'where' => 'wp-admin menu',
      ],
      'menu_academy' => [
        'what' => 'Yoast > Academy',
        'where' => 'wp-admin menu',
      ],
      'menu_licenses' => [
        'what' => 'Yoast > Premium licenses screens',
        'where' => 'wp-admin menu',
      ],
      'menu_redirects' => [
        'what' => 'Yoast > Redirects (admin.php?page=wpseo_redirects)',
        'where' => 'wp-admin menu',
      ],
      'dashboard_yoast' => [
        'what' => 'Dashboard widget: “Yoast SEO”',
        'where' => 'wp-admin > Dashboard',
      ],
      'dashboard_wincher' => [
        'what' => 'Dashboard widget: “Wincher”',
        'where' => 'wp-admin > Dashboard',
      ],
    ],
    'redis' => [
      'adminbar' => [
        'what' => 'Admin bar: Redis / cache flush & metrics',
        'where' => 'Admin bar (front end and wp-admin)',
        'constant' => "define('WP_REDIS_DISABLE_ADMINBAR', true);",
      ],
      'banners' => [
        'what' => 'Object Cache Pro / Redis Cache upsell banners',
        'where' => 'wp-admin > Settings (Redis screens)',
        'constant' => "define('WP_REDIS_DISABLE_BANNERS', true);",
      ],
      'dropin_banners' => [
        'what' => 'Admin notices about object-cache.php drop-in updates',
        'where' => 'wp-admin',
        'constant' => "define('WP_REDIS_DISABLE_DROPIN_BANNERS', true);",
      ],
      'html_comment' => [
        'what' => '<!-- Performance optimized by Redis Object Cache. Learn more: … -->',
        'where' => 'Front end HTML source',
        'constant' => "define('WP_REDIS_DISABLE_COMMENT', true);",
      ],
      'metrics' => [
        'what' => 'Hit/miss metrics charts and recorded timings',
        'where' => 'Redis Object Cache admin screens',
        'constant' => "define('WP_REDIS_DISABLE_METRICS', true);",
      ],
      'dashboard_widget' => [
        'what' => 'Dashboard widget: “Redis Object Cache”',
        'where' => 'wp-admin > Dashboard',
      ],
    ],
    'embedpress' => [
      'assets_outside_single' => [
        'what' => <<<'TXT'
<link rel="stylesheet" href="…/embedpress.css">
<link rel="stylesheet" href="…/plyr.css">
<script src="…/plyr.polyfilled.js"></script>
<script src="…/pdfobject.js"></script>
TXT,
        'where' => 'Archives, home, pages (not single posts)',
      ],
      'gallery_justify' => [
        'what' => '<script src="…/embedpress-gallery-justify.js"></script>',
        'where' => 'Front end where EmbedPress gallery is used',
      ],
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
