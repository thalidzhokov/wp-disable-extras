<?php

defined('ABSPATH') || exit;

/**
 * @return array{
 *   wp: array<string, bool>,
 *   yoast: array<string, bool>,
 *   redis: array<string, bool>,
 *   embedpress: array<string, bool>,
 *   wpforms: array<string, bool>,
 *   wp_mail_smtp: array<string, bool>
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
      'jquery_migrate' => false,
      'thickbox_front' => true,
      'block_styles' => false,
      'rsd' => false,
      'wlwmanifest' => false,
      'shortlink' => false,
      'adjacent_posts' => false,
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
      'capital_p_dangit' => true,
      'autosave' => false,
      'remote_block_patterns' => true,
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
      'admin_footer' => true,
      'login_logo' => false,
      'login_language' => true,
      'comments' => false,
      'author_enumeration' => true,
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
    'wpforms' => [
      'global_assets' => true,
      'assets_without_form' => false,
      'admin_bar' => true,
      'dashboard_widget' => true,
      'announcements' => true,
      'menu_upsells' => true,
    ],
    'wp_mail_smtp' => [
      'dashboard_widget' => true,
      'admin_bar' => true,
      'announcements' => true,
      'flyout' => true,
      'menu_upsells' => true,
      'delivery_error_notices' => false,
    ],
  ];
}

/**
 * @return array{
 *   wp: array<string, bool>,
 *   yoast: array<string, bool>,
 *   redis: array<string, bool>,
 *   embedpress: array<string, bool>,
 *   wpforms: array<string, bool>,
 *   wp_mail_smtp: array<string, bool>
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
  // Constant from wp-config owns this option — plugin must not apply it.
  if (disable_extras_is_locked_by_constant($group, $key)) {
    return false;
  }

  $options = disable_extras_get_options();

  return !empty($options[$group][$key]);
}

/**
 * Option keys that map to wp-config constants (disable value).
 *
 * @return array<string, array<string, array{0: string, 1: mixed}>>
 */
function disable_extras_option_constants(): array {
  return [
    'wp' => [
      'auto_updates_all' => ['AUTOMATIC_UPDATER_DISABLED', true],
      'auto_updates_core' => ['WP_AUTO_UPDATE_CORE', false],
      'disallow_file_edit' => ['DISALLOW_FILE_EDIT', true],
      'disallow_file_mods' => ['DISALLOW_FILE_MODS', true],
      'disable_wp_cron' => ['DISABLE_WP_CRON', true],
      'disallow_unfiltered_html' => ['DISALLOW_UNFILTERED_HTML', true],
      'post_revisions' => ['WP_POST_REVISIONS', false],
      'empty_trash' => ['EMPTY_TRASH_DAYS', 0],
    ],
    'redis' => [
      'adminbar' => ['WP_REDIS_DISABLE_ADMINBAR', true],
      'banners' => ['WP_REDIS_DISABLE_BANNERS', true],
      'dropin_banners' => ['WP_REDIS_DISABLE_DROPIN_BANNERS', true],
      'html_comment' => ['WP_REDIS_DISABLE_COMMENT', true],
      'metrics' => ['WP_REDIS_DISABLE_METRICS', true],
    ],
  ];
}

/**
 * @param string $group
 * @param string $key
 *
 * @return array{0: string, 1: mixed}|null
 */
function disable_extras_option_constant(string $group, string $key): ?array {
  return disable_extras_option_constants()[$group][$key] ?? null;
}

/**
 * Whether a defined constant already disables the feature.
 *
 * @param string $constant
 * @param mixed $expected
 *
 * @return bool
 */
function disable_extras_constant_matches_disable(string $constant, $expected): bool {
  if (!defined($constant)) {
    return false;
  }

  $actual = constant($constant);

  if ($constant === 'WP_POST_REVISIONS') {
    return $actual === false || $actual === 0;
  }

  if ($constant === 'EMPTY_TRASH_DAYS') {
    return (int) $actual === 0;
  }

  return $actual === $expected;
}

/**
 * Snapshot constants defined before this plugin defines any (e.g. wp-config.php).
 *
 * @return void
 */
function disable_extras_capture_external_constant_locks(): void {
  static $done = false;

  if ($done) {
    return;
  }

  $done = true;
  $locks = [];

  foreach (disable_extras_option_constants() as $group => $items) {
    foreach ($items as $key => [$constant]) {
      if (defined($constant)) {
        $locks[$group . '.' . $key] = true;
      }
    }
  }

  $GLOBALS['disable_extras_external_constant_locks'] = $locks;
}

/**
 * @param string $group
 * @param string $key
 *
 * @return bool
 */
function disable_extras_is_locked_by_constant(string $group, string $key): bool {
  $locks = $GLOBALS['disable_extras_external_constant_locks'] ?? [];

  return !empty($locks[$group . '.' . $key]);
}

/**
 * @return array<string, array<string, string>>
 */
function disable_extras_option_labels(): array {
  return [
    'wp' => [
      'emoji' => __('Emoji (scripts, styles, mail, RSS)', 'disable-extras'),
      'generator' => __('Generator meta tag and RSS version', 'disable-extras'),
      'script_versions' => __('Core ver= query arg on CSS/JS (WordPress version only)', 'disable-extras'),
      'dns_prefetch' => __('DNS prefetch', 'disable-extras'),
      'resource_hints' => __('Resource hints (preconnect, prefetch, prerender)', 'disable-extras'),
      'dashicons_guests' => __('Dashicons for logged-out users', 'disable-extras'),
      'jquery_front' => __('jQuery on the front end', 'disable-extras'),
      'jquery_migrate' => __('jQuery Migrate on the front end', 'disable-extras'),
      'thickbox_front' => __('Thickbox on the front end', 'disable-extras'),
      'block_styles' => __('Block library / global / classic-theme CSS on the front end', 'disable-extras'),
      'rsd' => __('RSD link', 'disable-extras'),
      'wlwmanifest' => __('WLW Manifest link', 'disable-extras'),
      'shortlink' => __('Shortlink', 'disable-extras'),
      'adjacent_posts' => __('Adjacent posts rel links (core)', 'disable-extras'),
      'xmlrpc' => __('XML-RPC', 'disable-extras'),
      'self_pingbacks' => __('Self-pingbacks', 'disable-extras'),
      'pingbacks_outgoing' => __('Outgoing pingbacks', 'disable-extras'),
      'pingbacks_incoming' => __('Incoming pingbacks', 'disable-extras'),
      'oembed_discovery' => __('oEmbed discovery links and host JS', 'disable-extras'),
      'oembed_endpoint' => __('oEmbed REST endpoint', 'disable-extras'),
      'embeds' => __('WordPress embeds (oEmbed host JS, embed endpoints)', 'disable-extras'),
      'rest_users' => __('REST users endpoint for guests', 'disable-extras'),
      'rest_guests' => __('REST API for guests', 'disable-extras'),
      'rest_links' => __('REST API discovery links (head, headers, RSD)', 'disable-extras'),
      'heartbeat_front' => __('Heartbeat on the front end', 'disable-extras'),
      'heartbeat_admin' => __('Heartbeat in admin', 'disable-extras'),
      'heartbeat_slow' => __('Heartbeat interval: slow (60s)', 'disable-extras'),
      'texturization' => __('Texturization (smart quotes, dashes, ellipsis)', 'disable-extras'),
      'capital_p_dangit' => __('capital_P_dangit (force “WordPress” spelling)', 'disable-extras'),
      'autosave' => __('Editor autosave', 'disable-extras'),
      'remote_block_patterns' => __('Remote block patterns (api.wordpress.org)', 'disable-extras'),
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
      'admin_footer' => __('Admin footer: “Thank you for creating with WordPress”', 'disable-extras'),
      'login_logo' => __('Login: WordPress logo → blog name', 'disable-extras'),
      'login_language' => __('Login: language switcher', 'disable-extras'),
      'comments' => __('Comments (front end, admin, new comments)', 'disable-extras'),
      'author_enumeration' => __('Author archives and ?author= enumeration', 'disable-extras'),
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
      'premium_notifications' => __('Premium: trash/slug/term notifications', 'disable-extras'),
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
    'wpforms' => [
      'global_assets' => __('Load Assets Globally (WPForms setting)', 'disable-extras'),
      'assets_without_form' => __('WPForms CSS/JS on pages without a form shortcode/block', 'disable-extras'),
      'admin_bar' => __('Admin bar menu', 'disable-extras'),
      'dashboard_widget' => __('Dashboard widget', 'disable-extras'),
      'announcements' => __('Announcements / notification feed', 'disable-extras'),
      'menu_upsells' => __('Menu: Analytics, SMTP, About, Community, Addons…', 'disable-extras'),
    ],
    'wp_mail_smtp' => [
      'dashboard_widget' => __('Dashboard widget', 'disable-extras'),
      'admin_bar' => __('Admin bar menu', 'disable-extras'),
      'announcements' => __('Announcements / notification feed', 'disable-extras'),
      'flyout' => __('Flyout quick links menu', 'disable-extras'),
      'menu_upsells' => __('Menu: About Us, recommended plugins, Upgrade to Pro', 'disable-extras'),
      'delivery_error_notices' => __('Email delivery error admin notices', 'disable-extras'),
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
          'adjacent_posts',
          'oembed_discovery',
          'oembed_endpoint',
          'rest_links',
        ],
      ],
      [
        'title' => __('Editor', 'disable-extras'),
        'keys' => [
          'texturization',
          'capital_p_dangit',
          'autosave',
          'remote_block_patterns',
        ],
      ],
      [
        'title' => __('Front-end scripts', 'disable-extras'),
        'keys' => [
          'dashicons_guests',
          'jquery_front',
          'jquery_migrate',
          'thickbox_front',
          'block_styles',
          'embeds',
        ],
      ],
      [
        'title' => __('Heartbeat', 'disable-extras'),
        'keys' => [
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
          'admin_footer',
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
          'login_language',
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
          'author_enumeration',
        ],
      ],
      [
        'title' => __('Comments', 'disable-extras'),
        'keys' => [
          'comments',
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
    'wpforms' => [
      [
        'title' => __('Front-end assets', 'disable-extras'),
        'keys' => [
          'global_assets',
          'assets_without_form',
        ],
      ],
      [
        'title' => __('Admin UI', 'disable-extras'),
        'keys' => [
          'admin_bar',
          'dashboard_widget',
          'announcements',
          'menu_upsells',
        ],
      ],
    ],
    'wp_mail_smtp' => [
      [
        'title' => __('Admin UI', 'disable-extras'),
        'keys' => [
          'dashboard_widget',
          'admin_bar',
          'announcements',
          'flyout',
          'menu_upsells',
        ],
      ],
      [
        'title' => __('Notices', 'disable-extras'),
        'keys' => [
          'delivery_error_notices',
        ],
      ],
    ],
  ];
}

/**
 * Hints for settings UI.
 *
 * Keys per option (all optional):
 * - summary: string|list — prose under the checkbox; HTML tags inside <code> use &lt; &gt;
 * - label_note / label_note_tone: short note under the label (ok|warn|muted)
 * - what / where: legacy blocks (skipped when summary is set)
 * - constant: wp-config-style define() example
 *
 * @return array<string, array<string, array<string, mixed>>>
 */
function disable_extras_option_hints(): array {
  return [
    'wp' => [
      'emoji' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes <code>&lt;script src="…/wp-emoji-release.min.js"&gt;&lt;/script&gt;</code> and <code>&lt;link rel="stylesheet" href="…/emoji.css"&gt;</code> from the front <code>&lt;head&gt;</code>, and emoji images instead of native characters in RSS/Atom feeds and outgoing mail (<code>wp_mail</code>)',
      ],
      'generator' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the <code>&lt;meta name="generator" content="WordPress 6.7.1"&gt;</code> tag from HTML and the <code>&lt;generator&gt;https://wordpress.org/?v=6.7.1&lt;/generator&gt;</code> tag from RSS/Atom feeds',
      ],
      'script_versions' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes the <code>?ver=</code> query arg only when it equals the WordPress version (e.g. <code>/wp-includes/css/dashicons.min.css?ver=6.7.1</code>). Assets with their own versions (jQuery, etc.) are left unchanged',
      ],
      'dns_prefetch' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes <code>&lt;link rel="dns-prefetch" href="//s.w.org"&gt;</code> and similar dns-prefetch links from the front <code>&lt;head&gt;</code>',
      ],
      'resource_hints' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes <code>&lt;link rel="preconnect|prefetch|prerender" …&gt;</code> from the front <code>&lt;head&gt;</code>',
      ],
      'dashicons_guests' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes <code>&lt;link rel="stylesheet" href="/wp-includes/css/dashicons.min.css"&gt;</code> on the front end for logged-out visitors',
      ],
      'jquery_front' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Removes <code>&lt;script src="/wp-includes/js/jquery/jquery.min.js"&gt;&lt;/script&gt;</code> and jQuery Migrate from the front end',
      ],
      'jquery_migrate' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes only <code>jquery-migrate.min.js</code> from the front end; keeps jQuery itself. Redundant if “jQuery on the front end” is already on',
      ],
      'thickbox_front' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes Thickbox <code>&lt;script&gt;</code> / <code>&lt;link&gt;</code> from the front end',
      ],
      'block_styles' => [
        'label_note' => 'Optional — not if block content relies on core CSS',
        'label_note_tone' => 'muted',
        'summary' => 'Removes front-end styles <code>wp-block-library</code>, <code>global-styles</code>, and <code>classic-theme-styles</code> (and stops enqueueing global styles)',
      ],
      'rsd' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes <code>&lt;link rel="EditURI" type="application/rsd+xml" title="RSD" href="…/xmlrpc.php?rsd"&gt;</code> from the front <code>&lt;head&gt;</code>',
      ],
      'wlwmanifest' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes <code>&lt;link rel="wlwmanifest" type="application/wlwmanifest+xml" href="…/wlwmanifest.xml"&gt;</code> from the front <code>&lt;head&gt;</code>',
      ],
      'shortlink' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes <code>&lt;link rel="shortlink" href="https://example.com/?p=123"&gt;</code> from the front <code>&lt;head&gt;</code> and the <code>Link: &lt;…&gt;; rel=shortlink</code> HTTP header',
      ],
      'adjacent_posts' => [
        'label_note' => 'Optional — only if a theme/plugin re-adds them',
        'label_note_tone' => 'muted',
        'summary' => 'Removes <code>adjacent_posts_rel_link_wp_head</code> (<code>&lt;link rel="prev|next"&gt;</code>). Core stopped outputting these in WP 5.6+; use when a theme or plugin hooks them back. For Yoast’s own prev/next use the Yoast tab',
      ],
      'xmlrpc' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables <code>POST /xmlrpc.php</code> and removes the RSD <code>&lt;link rel="EditURI" …&gt;</code> from the front <code>&lt;head&gt;</code>',
      ],
      'self_pingbacks' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables <code>pingback.ping</code> to the own site when a post links to another own post on publish/update',
      ],
      'pingbacks_outgoing' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables outgoing <code>pingback.ping</code> / trackback to other sites on publish/update',
      ],
      'pingbacks_incoming' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables incoming <code>pingback.ping</code> via XML-RPC, removes the <code>X-Pingback</code> header, and closes pings via the <code>pings_open</code> filter',
      ],
      'oembed_discovery' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes oEmbed <code>&lt;link rel="alternate" type="application/json+oembed" …&gt;</code> / xml+oembed and <code>&lt;script src="…/wp-embed.min.js"&gt;&lt;/script&gt;</code> (host JS) from the front <code>&lt;head&gt;</code> on singular content',
      ],
      'oembed_endpoint' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables REST oEmbed routes <code>GET /wp-json/oembed/1.0/embed</code> and <code>GET /wp-json/oembed/1.0/proxy</code>',
      ],
      'embeds' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables WordPress embeds: <code>/embed/</code> templates, <code>wp-embed.min.js</code>, oEmbed discovery links and REST oEmbed routes',
      ],
      'rest_users' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Blocks <code>GET /wp-json/wp/v2/users</code> for logged-out visitors',
      ],
      'rest_guests' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Returns 401 for logged-out visitors on the REST API (<code>GET /wp-json/…</code>)',
      ],
      'rest_links' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes <code>&lt;link rel="https://api.w.org/" href="…/wp-json/"&gt;</code> from the front <code>&lt;head&gt;</code>, the matching HTTP <code>Link</code> header, and the REST entry in RSD',
      ],
      'heartbeat_front' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes <code>&lt;script src="…/heartbeat.min.js"&gt;&lt;/script&gt;</code> and <code>admin-ajax.php?action=heartbeat</code> on the front end',
      ],
      'heartbeat_admin' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Removes <code>&lt;script src="…/heartbeat.min.js"&gt;&lt;/script&gt;</code> and <code>admin-ajax.php?action=heartbeat</code> in wp-admin',
      ],
      'heartbeat_slow' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Sets <code>heartbeat_settings.interval = 60</code> instead of ~15s on the front end and in wp-admin',
      ],
      'texturization' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables conversion of "quotes" into “quotes”, <code>--</code> > <code>—</code>, and <code>...</code> > <code>…</code> in content, titles, excerpts, comments, and feeds',
      ],
      'capital_p_dangit' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Stops forcing the spelling “WordPress” (capital P) in titles, content, comments, and widget text via <code>capital_P_dangit</code>',
      ],
      'autosave' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Removes <code>&lt;script src="…/autosave.min.js"&gt;&lt;/script&gt;</code> from the block/classic editor in wp-admin',
      ],
      'remote_block_patterns' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Stops loading remote block patterns from <code>api.wordpress.org</code> in the block editor',
      ],
      'dashboard_right_now' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes the Dashboard widget “At a Glance” from <code>wp-admin > Dashboard</code>',
      ],
      'dashboard_network_right_now' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes the Network Dashboard widget “Right Now” from <code>Network Admin > Dashboard</code>',
      ],
      'dashboard_activity' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes the Dashboard widget “Activity” from <code>wp-admin > Dashboard</code>',
      ],
      'dashboard_quick_press' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes the Dashboard widget “Quick Draft” from <code>wp-admin > Dashboard</code>',
      ],
      'dashboard_primary' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the Dashboard widget “WordPress Events and News” from <code>wp-admin > Dashboard</code>',
      ],
      'dashboard_site_health' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes the Dashboard widget “Site Health Status” from <code>wp-admin > Dashboard</code>',
      ],
      'welcome_panel' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the Welcome panel from <code>wp-admin > Dashboard</code>',
      ],
      'admin_bar' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Hides <code>#wpadminbar</code> on the front end for every logged-in user',
      ],
      'admin_bar_non_admins' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Hides <code>#wpadminbar</code> on the front end for users without <code>manage_options</code>',
      ],
      'admin_bar_wp_logo' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables the Admin bar item <code>#wp-admin-bar-wp-logo</code> About WordPress (Documentation, Learn, Support, Feedback)',
      ],
      'admin_footer' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the “Thank you for creating with WordPress.” text from the wp-admin footer',
      ],
      'login_logo' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Replaces <code>&lt;h1 class="wp-login-logo"&gt;&lt;a href="https://wordpress.org/"&gt;…&lt;/a&gt;&lt;/h1&gt;</code> on <code>wp-login.php</code> with the blog name text (no link)',
      ],
      'login_language' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Hides the language dropdown on <code>wp-login.php</code>',
      ],
      'comments' => [
        'label_note' => 'Optional — only if the site does not use comments',
        'label_note_tone' => 'muted',
        'summary' => [
          'Closes comments and trackbacks on all post types, hides existing comments, removes Comments from the admin menu and admin bar, and deregisters <code>comment-reply</code>',
          'Does not delete old comments from the database',
        ],
      ],
      'author_enumeration' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Blocks <code>?author=1</code> style probes and author archive URLs (<code>/author/name/</code>), and removes the users provider from core sitemaps',
      ],
      'feed_redirect' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Redirects disabled feed URLs with <code>301</code> to the homepage (when other feed options are on)',
      ],
      'feed_global' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Disables global post feeds (<code>/feed/</code>, <code>/rss/</code>, <code>/rss2/</code>, <code>/atom/</code>, <code>/rdf/</code>) and removes <code>&lt;link rel="alternate" type="application/rss+xml" …&gt;</code> from the front <code>&lt;head&gt;</code>',
      ],
      'feed_global_comments' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables <code>/comments/feed/</code> and removes comments feed alternate links from the front <code>&lt;head&gt;</code>',
      ],
      'feed_post_comments' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables per-post comment feeds (<code>/post-slug/feed/</code>, <code>/post-slug/comments/feed/</code>) and their alternate links in <code>&lt;head&gt;</code>',
      ],
      'feed_authors' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables author feeds (<code>/author/name/feed/</code>)',
      ],
      'feed_post_types' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables custom post type archive feeds (<code>/cpt-slug/feed/</code>)',
      ],
      'feed_categories' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables category feeds (<code>/category/news/feed/</code>)',
      ],
      'feed_tags' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables tag feeds (<code>/tag/esports/feed/</code>)',
      ],
      'feed_custom_taxonomies' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables custom taxonomy feeds (<code>/taxonomy-slug/term/feed/</code>)',
      ],
      'feed_search' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables search result feeds (<code>/?s=query&amp;feed=rss2</code>)',
      ],
      'feed_atom_rdf' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables Atom/RDF/RSS format endpoints (<code>/feed/atom/</code>, <code>/feed/rdf/</code>, <code>/feed/rss/</code>); RSS2 stays unless other feed options apply',
      ],
      'auto_updates_core' => [
        'label_note' => 'Optional — useful with controlled deploys',
        'label_note_tone' => 'muted',
        'summary' => 'Disables automatic WordPress core updates (minor/major) in the background updater / <code>Dashboard > Updates</code>',
        'constant' => "define('WP_AUTO_UPDATE_CORE', false);",
      ],
      'auto_updates_plugins' => [
        'label_note' => 'Optional — useful with controlled deploys',
        'label_note_tone' => 'muted',
        'summary' => 'Disables automatic plugin updates in the plugins list / background updater',
      ],
      'auto_updates_themes' => [
        'label_note' => 'Optional — useful with controlled deploys',
        'label_note_tone' => 'muted',
        'summary' => 'Disables automatic theme updates in <code>Appearance > Themes</code> / background updater',
      ],
      'auto_updates_translations' => [
        'label_note' => 'Optional — useful with controlled deploys',
        'label_note_tone' => 'muted',
        'summary' => 'Disables automatic language pack / translation updates in the background updater',
      ],
      'auto_updates_all' => [
        'label_note' => 'Optional — useful with controlled deploys; not if you rely on auto security patches',
        'label_note_tone' => 'muted',
        'summary' => [
          'Disables all background automatic updates: WordPress core, plugins, themes, translations',
        ],
        'constant' => "define('AUTOMATIC_UPDATER_DISABLED', true);",
      ],
      'update_nags_admins_only' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Hides update nags (“WordPress X.Y is available! Please update…”) in wp-admin for users without <code>update_core</code>',
      ],
      'update_phone_home' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Stops sending the site URL in <code>User-Agent</code> / <code>wp_blog</code> / <code>wp_install</code> headers on outbound checks to <code>api.wordpress.org</code>',
      ],
      'application_passwords' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables Application Passwords in <code>Users > Profile</code> and Basic auth for REST / XML-RPC apps',
      ],
      'core_sitemaps' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables core sitemap endpoints (<code>/wp-sitemap.xml</code>, <code>/wp-sitemap-posts-post-1.xml</code>, <code>/wp-sitemap-users-1.xml</code>)',
      ],
      'disallow_file_edit' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables the theme and plugin file editors in wp-admin: <code>Appearance > Theme File Editor</code> and <code>Plugins > Plugin File Editor</code>',
        'constant' => "define('DISALLOW_FILE_EDIT', true);",
      ],
      'disallow_file_mods' => [
        'label_note' => 'Optional — useful on production with controlled deploys',
        'label_note_tone' => 'muted',
        'summary' => [
          'Disables installing and updating plugins and themes in wp-admin: <code>Plugins > Add New / Update</code> and <code>Appearance > Themes > Add New / Update</code>',
          'Updates are still possible via WP-CLI or by replacing files on the server, not from wp-admin',
        ],
        'constant' => "define('DISALLOW_FILE_MODS', true);",
      ],
      'disable_wp_cron' => [
        'label_note' => 'Optional — useful if you set up a system cron',
        'label_note_tone' => 'muted',
        'summary' => [
          'Disables built-in WP-Cron on page load',
          'Tasks from <code>wp_schedule_event()</code> and <code>wp_schedule_single_event()</code> stay queued and wait; there is no auto-run on page load',
          'To run manually, call <code>wp-cron.php</code>, e.g. <code>php /path/to/wordpress/wp-cron.php</code>',
        ],
        'constant' => "define('DISABLE_WP_CRON', true);",
      ],
      'disallow_unfiltered_html' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables the ability for Administrators and Editors to insert raw code such as <code>script</code>, <code>iframe</code> without kses in the post/page editor',
        'constant' => "define('DISALLOW_UNFILTERED_HTML', true);",
      ],
      'post_revisions' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Disables creating new revisions (<code>WP_POST_REVISIONS</code>); the revisions UI in the editor stops growing. Existing <code>post_type = revision</code> rows in <code>wp_posts</code> are not deleted',
        'constant' => "define('WP_POST_REVISIONS', false);",
      ],
      'empty_trash' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Disables the trash for posts/media/comments: items are permanently deleted immediately in wp-admin content lists',
        'constant' => "define('EMPTY_TRASH_DAYS', 0);",
      ],
    ],
    'yoast' => [
      'premium_redirects' => [
        'label_note' => 'Not recommended',
        'label_note_tone' => 'warn',
        'summary' => 'Disables Yoast Premium automatic redirects <code>/old-slug/</code> > <code>/new-slug/</code> when a post or term slug changes',
      ],
      'premium_notifications' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables Yoast Premium admin notices when a post is trashed, a post/term slug changes, or a term is deleted',
      ],
      'tracking' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables anonymous Yoast usage / telemetry data sent outbound to Yoast',
      ],
      'schema_blocks' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables Yoast FAQ, How-to, and other schema blocks in the block editor',
      ],
      'assessment_markers' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables SEO / readability highlight markers in post content in the block/classic editor',
      ],
      'rss_footer' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the RSS footer line like “The post Title appeared first on Blog” (or the Russian variant) from RSS/Atom feed items',
      ],
      'adjacent_rel' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes <code>&lt;link rel="prev"&gt;</code> and <code>&lt;link rel="next"&gt;</code> from the front <code>&lt;head&gt;</code> on singular posts',
      ],
      'admin_upsells' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes Yoast upsells: admin notices, promotional footer, AI Content Planner banner, premium analysis ads in the editor, and menu items <code>Yoast > Upgrade</code> / <code>Yoast > AI Brand Insights</code>',
      ],
      'menu_integrations' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the <code>Yoast > Integrations</code> item from the wp-admin menu',
      ],
      'menu_workouts' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the <code>Yoast > Workouts</code> item from the wp-admin menu',
      ],
      'menu_courses' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the <code>Yoast > Courses</code> item from the wp-admin menu',
      ],
      'menu_academy' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the <code>Yoast > Academy</code> item from the wp-admin menu',
      ],
      'menu_licenses' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes Yoast Premium licenses screens from the wp-admin menu',
      ],
      'menu_redirects' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes <code>Yoast > Redirects</code> (<code>admin.php?page=wpseo_redirects</code>) from the wp-admin menu',
      ],
      'dashboard_yoast' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the Dashboard widget “Yoast SEO” from <code>wp-admin > Dashboard</code>',
      ],
      'dashboard_wincher' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the Dashboard widget “Wincher” from <code>wp-admin > Dashboard</code>',
      ],
    ],
    'redis' => [
      'adminbar' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes Redis / cache flush & metrics from the Admin bar (front end and wp-admin)',
        'constant' => "define('WP_REDIS_DISABLE_ADMINBAR', true);",
      ],
      'banners' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes Object Cache Pro / Redis Cache upsell banners from <code>wp-admin > Settings</code> (Redis screens)',
        'constant' => "define('WP_REDIS_DISABLE_BANNERS', true);",
      ],
      'dropin_banners' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes admin notices about <code>object-cache.php</code> drop-in updates in wp-admin',
        'constant' => "define('WP_REDIS_DISABLE_DROPIN_BANNERS', true);",
      ],
      'html_comment' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the HTML comment <code>&lt;!-- Performance optimized by Redis Object Cache. Learn more: … --&gt;</code> from the front-end HTML source',
        'constant' => "define('WP_REDIS_DISABLE_COMMENT', true);",
      ],
      'metrics' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Disables hit/miss metrics charts and recorded timings on Redis Object Cache admin screens',
        'constant' => "define('WP_REDIS_DISABLE_METRICS', true);",
      ],
      'dashboard_widget' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the Dashboard widget “Redis Object Cache” from <code>wp-admin > Dashboard</code>',
      ],
    ],
    'embedpress' => [
      'assets_outside_single' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes EmbedPress / Plyr CSS/JS (<code>embedpress.css</code>, <code>plyr.css</code>, <code>plyr.polyfilled.js</code>, <code>pdfobject.js</code>) on archives, home, and pages (not single posts)',
      ],
      'gallery_justify' => [
        'label_note' => 'Optional',
        'label_note_tone' => 'muted',
        'summary' => 'Removes <code>&lt;script src="…/embedpress-gallery-justify.js"&gt;&lt;/script&gt;</code> on the front end where an EmbedPress gallery is used',
      ],
    ],
    'wpforms' => [
      'global_assets' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Forces <code>wpforms_global_assets</code> to false so CSS/JS load only when WPForms renders a form (overrides WPForms → Settings → “Load Assets Globally”)',
      ],
      'assets_without_form' => [
        'label_note' => 'Optional — not if the form is only in a widget/template',
        'label_note_tone' => 'muted',
        'summary' => 'Dequeues handles starting with <code>wpforms</code> on singular pages without <code>[wpforms]</code> / block <code>wpforms/form-selector</code> in post content',
      ],
      'admin_bar' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Hides the WPForms menu in the Admin bar (front end and wp-admin)',
      ],
      'dashboard_widget' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the Dashboard widget “WPForms” (<code>wpforms_reports_widget_lite</code> / Pro) from <code>wp-admin > Dashboard</code>',
      ],
      'announcements' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables the WPForms announcements / notification feed (<code>wpforms_admin_notifications_has_access</code>)',
      ],
      'menu_upsells' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes upsell submenu items under WPForms: Analytics, SMTP, About, Community, WPConsent, Addons',
      ],
    ],
    'wp_mail_smtp' => [
      'dashboard_widget' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes the Dashboard widget “WP Mail SMTP” (<code>wp_mail_smtp_reports_widget_lite</code>) from <code>wp-admin > Dashboard</code>',
      ],
      'admin_bar' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Hides the WP Mail SMTP menu in the Admin bar (front end and wp-admin)',
      ],
      'announcements' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Disables the WP Mail SMTP announcements / notification feed (<code>wp_mail_smtp_admin_notifications_has_access</code>)',
      ],
      'flyout' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Hides the floating flyout quick-links menu on WP Mail SMTP admin screens',
      ],
      'menu_upsells' => [
        'label_note' => 'Recommended',
        'label_note_tone' => 'ok',
        'summary' => 'Removes About Us, recommended-plugin landing pages, and “Upgrade to Pro” from the WP Mail SMTP admin menu',
      ],
      'delivery_error_notices' => [
        'label_note' => 'Not recommended — hides real delivery failures',
        'label_note_tone' => 'warn',
        'summary' => 'Disables admin notices about failed email delivery (<code>wp_mail_smtp_admin_is_error_delivery_notice_enabled</code>)',
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
 *   embedpress: array<string, bool>,
 *   wpforms: array<string, bool>,
 *   wp_mail_smtp: array<string, bool>
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
      if (disable_extras_is_locked_by_constant($group, $key)) {
        $pair = disable_extras_option_constant($group, $key);
        $output[$group][$key] = $pair !== null
          && disable_extras_constant_matches_disable($pair[0], $pair[1]);
        continue;
      }

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

/**
 * @return bool
 */
function disable_extras_is_wpforms_active(): bool {
  if (defined('WPFORMS_VERSION')) {
    return true;
  }

  if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  return is_plugin_active('wpforms-lite/wpforms.php')
    || is_plugin_active('wpforms/wpforms.php');
}

/**
 * @return bool
 */
function disable_extras_is_wp_mail_smtp_active(): bool {
  if (defined('WPMS_PLUGIN_VER') || defined('WPMS_PLUGIN_FILE')) {
    return true;
  }

  if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  return is_plugin_active('wp-mail-smtp/wp_mail_smtp.php')
    || is_plugin_active('wp-mail-smtp-pro/wp_mail_smtp.php');
}
