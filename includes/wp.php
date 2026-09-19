<?php

defined('ABSPATH') || exit;

/**
 * @return void
 */
function disable_extras_wp_boot(): void {
  disable_extras_wp_define_constants();
  add_action('init', 'disable_extras_wp_apply', 0);
}

/**
 * @return void
 */
function disable_extras_wp_define_constants(): void {
  $map = [
    'auto_updates_all' => ['AUTOMATIC_UPDATER_DISABLED', true],
    'auto_updates_core' => ['WP_AUTO_UPDATE_CORE', false],
    'disallow_file_edit' => ['DISALLOW_FILE_EDIT', true],
    'disallow_file_mods' => ['DISALLOW_FILE_MODS', true],
    'disable_wp_cron' => ['DISABLE_WP_CRON', true],
    'disallow_unfiltered_html' => ['DISALLOW_UNFILTERED_HTML', true],
    'post_revisions' => ['WP_POST_REVISIONS', false],
    'empty_trash' => ['EMPTY_TRASH_DAYS', 0],
  ];

  foreach ($map as $optionKey => [$constant, $value]) {
    if (!disable_extras_is_enabled('wp', $optionKey)) {
      continue;
    }

    if (!defined($constant)) {
      define($constant, $value);
    }
  }
}

/**
 * @return void
 */
function disable_extras_wp_apply(): void {
  if (disable_extras_is_enabled('wp', 'emoji')) {
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    add_filter('emoji_svg_url', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'generator')) {
    remove_action('wp_head', 'wp_generator');
    add_filter('the_generator', '__return_empty_string');
  }

  if (disable_extras_is_enabled('wp', 'script_versions')) {
    add_filter('style_loader_src', 'disable_extras_wp_strip_core_ver', 15);
    add_filter('script_loader_src', 'disable_extras_wp_strip_core_ver', 15);
  }

  if (
    disable_extras_is_enabled('wp', 'dns_prefetch')
    && disable_extras_is_enabled('wp', 'resource_hints')
  ) {
    remove_action('wp_head', 'wp_resource_hints', 2);
  } elseif (
    disable_extras_is_enabled('wp', 'dns_prefetch')
    || disable_extras_is_enabled('wp', 'resource_hints')
  ) {
    add_filter('wp_resource_hints', 'disable_extras_wp_filter_resource_hints', 10, 2);
  }

  if (disable_extras_is_enabled('wp', 'rsd')) {
    remove_action('wp_head', 'rsd_link');
  }

  if (disable_extras_is_enabled('wp', 'wlwmanifest')) {
    remove_action('wp_head', 'wlwmanifest_link');
  }

  if (disable_extras_is_enabled('wp', 'shortlink')) {
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
    remove_action('template_redirect', 'wp_shortlink_header', 11);
  }

  if (disable_extras_is_enabled('wp', 'adjacent_posts')) {
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
  }

  if (disable_extras_is_enabled('wp', 'xmlrpc')) {
    add_filter('xmlrpc_enabled', '__return_false');
    remove_action('wp_head', 'rsd_link');
  }

  if (disable_extras_is_enabled('wp', 'embeds')) {
    disable_extras_wp_disable_embeds();
  } else {
    if (disable_extras_is_enabled('wp', 'oembed_discovery')) {
      remove_action('wp_head', 'wp_oembed_add_discovery_links');
      remove_action('wp_head', 'wp_oembed_add_host_js');
      remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
      add_filter('embed_oembed_discover', '__return_false');
    }

    if (disable_extras_is_enabled('wp', 'oembed_endpoint')) {
      remove_action('rest_api_init', 'wp_oembed_register_route');
    }
  }

  if (disable_extras_is_enabled('wp', 'rest_links')) {
    remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('template_redirect', 'rest_output_link_header', 11);
  }

  if (disable_extras_is_enabled('wp', 'rest_guests')) {
    add_filter('rest_authentication_errors', 'disable_extras_wp_block_rest_guests', 99);
  } elseif (disable_extras_is_enabled('wp', 'rest_users')) {
    add_filter('rest_authentication_errors', 'disable_extras_wp_block_rest_users', 99);
    add_filter('rest_endpoints', 'disable_extras_wp_remove_users_endpoint');
  }

  if (disable_extras_is_enabled('wp', 'texturization')) {
    add_filter('run_wptexturize', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'capital_p_dangit')) {
    remove_filter('the_title', 'capital_P_dangit', 11);
    remove_filter('the_content', 'capital_P_dangit', 11);
    remove_filter('wp_title', 'capital_P_dangit', 11);
    remove_filter('document_title', 'capital_P_dangit', 11);
    remove_filter('comment_text', 'capital_P_dangit', 31);
    remove_filter('widget_text_content', 'capital_P_dangit', 11);
  }

  if (disable_extras_is_enabled('wp', 'remote_block_patterns')) {
    add_filter('should_load_remote_block_patterns', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'autosave')) {
    add_action('admin_enqueue_scripts', 'disable_extras_wp_disable_autosave', 20);
  }

  if (
    disable_extras_is_enabled('wp', 'dashboard_right_now')
    || disable_extras_is_enabled('wp', 'dashboard_network_right_now')
    || disable_extras_is_enabled('wp', 'dashboard_activity')
    || disable_extras_is_enabled('wp', 'dashboard_quick_press')
    || disable_extras_is_enabled('wp', 'dashboard_primary')
    || disable_extras_is_enabled('wp', 'dashboard_site_health')
    || disable_extras_is_enabled('wp', 'welcome_panel')
  ) {
    add_action('wp_dashboard_setup', 'disable_extras_wp_remove_dashboard_widgets', 999);
    add_action('wp_network_dashboard_setup', 'disable_extras_wp_remove_dashboard_widgets', 999);
  }

  if (
    disable_extras_is_enabled('wp', 'admin_bar')
    || disable_extras_is_enabled('wp', 'admin_bar_non_admins')
  ) {
    add_filter('show_admin_bar', 'disable_extras_wp_filter_admin_bar');
  }

  if (disable_extras_is_enabled('wp', 'admin_bar_wp_logo')) {
    add_action('admin_bar_menu', 'disable_extras_wp_remove_admin_bar_wp_logo', 999);
  }

  if (disable_extras_is_enabled('wp', 'admin_footer')) {
    add_filter('admin_footer_text', '__return_empty_string');
  }

  if (disable_extras_is_enabled('wp', 'login_logo')) {
    add_action('login_enqueue_scripts', 'disable_extras_wp_login_logo_styles');
    add_action('login_header', 'disable_extras_wp_login_logo_ob_start');
    add_action('login_footer', 'disable_extras_wp_login_logo_ob_end', 0);
  }

  if (disable_extras_is_enabled('wp', 'login_language')) {
    add_filter('login_display_language_dropdown', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'comments')) {
    disable_extras_wp_disable_comments();
  }

  if (disable_extras_is_enabled('wp', 'author_enumeration')) {
    add_action('init', 'disable_extras_wp_block_author_query', 1);
    add_action('template_redirect', 'disable_extras_wp_block_author_archives', 0);
    add_filter('wp_sitemaps_add_provider', 'disable_extras_wp_remove_users_sitemap', 10, 2);
    add_filter('author_rewrite_rules', '__return_empty_array');
  }

  $disableHeartbeatFront = disable_extras_is_enabled('wp', 'heartbeat_front');
  $disableHeartbeatAdmin = disable_extras_is_enabled('wp', 'heartbeat_admin');

  if ($disableHeartbeatFront || $disableHeartbeatAdmin) {
    add_action('init', 'disable_extras_wp_disable_heartbeat', 1);
  }

  if (disable_extras_is_enabled('wp', 'heartbeat_slow')) {
    add_filter('heartbeat_settings', 'disable_extras_wp_slow_heartbeat');
  }

  if (
    disable_extras_is_enabled('wp', 'self_pingbacks')
    || disable_extras_is_enabled('wp', 'pingbacks_outgoing')
  ) {
    add_action('pre_ping', 'disable_extras_wp_filter_outbound_pings');
  }

  if (disable_extras_is_enabled('wp', 'pingbacks_incoming')) {
    add_filter('xmlrpc_methods', 'disable_extras_wp_remove_pingback_methods');
    add_filter('wp_headers', 'disable_extras_wp_remove_pingback_header');
    add_filter('pings_open', '__return_false', 20, 2);
  }

  if (disable_extras_is_enabled('wp', 'auto_updates_core')) {
    add_filter('auto_update_core', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'auto_updates_plugins')) {
    add_filter('auto_update_plugin', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'auto_updates_themes')) {
    add_filter('auto_update_theme', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'auto_updates_translations')) {
    add_filter('auto_update_translation', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'auto_updates_all')) {
    add_filter('automatic_updater_disabled', '__return_true');
  }

  if (disable_extras_is_enabled('wp', 'update_nags_admins_only')) {
    add_action('admin_head', 'disable_extras_wp_hide_update_nags', 1);
  }

  if (disable_extras_is_enabled('wp', 'update_phone_home')) {
    add_filter('http_request_args', 'disable_extras_wp_strip_update_phone_home', 10, 2);
  }

  if (disable_extras_is_enabled('wp', 'application_passwords')) {
    add_filter('wp_is_application_passwords_available', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'core_sitemaps')) {
    add_filter('wp_sitemaps_enabled', '__return_false');
  }

  if (disable_extras_wp_has_feed_option()) {
    add_action('template_redirect', 'disable_extras_wp_block_feeds', 1);
    disable_extras_wp_remove_feed_links();
  }

  if (disable_extras_is_enabled('wp', 'jquery_migrate') && !disable_extras_is_enabled('wp', 'jquery_front')) {
    add_action('wp_default_scripts', 'disable_extras_wp_remove_jquery_migrate');
  }

  if (disable_extras_is_enabled('wp', 'block_styles')) {
    remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');
    remove_action('wp_footer', 'wp_enqueue_global_styles', 1);
    remove_action('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles');
    remove_action('enqueue_block_assets', 'wp_enqueue_classic_theme_styles');
  }

  if (
    disable_extras_is_enabled('wp', 'jquery_front')
    || disable_extras_is_enabled('wp', 'jquery_migrate')
    || disable_extras_is_enabled('wp', 'thickbox_front')
    || disable_extras_is_enabled('wp', 'dashicons_guests')
    || disable_extras_is_enabled('wp', 'block_styles')
  ) {
    add_action('wp_enqueue_scripts', 'disable_extras_wp_dequeue_front', 999);
  }
}

/**
 * @return void
 */
function disable_extras_wp_disable_embeds(): void {
  remove_action('rest_api_init', 'wp_oembed_register_route');
  remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
  remove_action('wp_head', 'wp_oembed_add_discovery_links');
  remove_action('wp_head', 'wp_oembed_add_host_js');
  remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
  add_filter('embed_oembed_discover', '__return_false');
  add_filter('tiny_mce_plugins', 'disable_extras_wp_remove_wpembed_mce_plugin');
  add_filter('rewrite_rules_array', 'disable_extras_wp_remove_embed_rewrites');
  add_action('wp_footer', 'disable_extras_wp_dequeue_embed_script', 1);
  add_action('admin_footer', 'disable_extras_wp_dequeue_embed_script', 1);
  add_action('template_redirect', 'disable_extras_wp_block_embed_request', 1);
}

/**
 * @param string[] $plugins
 *
 * @return string[]
 */
function disable_extras_wp_remove_wpembed_mce_plugin(array $plugins): array {
  return array_values(array_diff($plugins, ['wpembed']));
}

/**
 * @param array<string, string> $rules
 *
 * @return array<string, string>
 */
function disable_extras_wp_remove_embed_rewrites(array $rules): array {
  foreach ($rules as $rule => $rewrite) {
    if (str_contains($rewrite, 'embed=true')) {
      unset($rules[$rule]);
    }
  }

  return $rules;
}

/**
 * @return void
 */
function disable_extras_wp_dequeue_embed_script(): void {
  wp_dequeue_script('wp-embed');
  wp_deregister_script('wp-embed');
}

/**
 * @return void
 */
function disable_extras_wp_block_embed_request(): void {
  if (!is_embed()) {
    return;
  }

  status_header(404);
  nocache_headers();
  wp_die(
    esc_html__('Embeds are disabled.', 'disable-extras'),
    '',
    ['response' => 404]
  );
}

/**
 * @return void
 */
function disable_extras_wp_disable_autosave(): void {
  wp_deregister_script('autosave');
}

/**
 * @return void
 */
function disable_extras_wp_remove_dashboard_widgets(): void {
  if (disable_extras_is_enabled('wp', 'dashboard_right_now')) {
    remove_meta_box('dashboard_right_now', 'dashboard', 'normal');
  }

  if (disable_extras_is_enabled('wp', 'dashboard_network_right_now')) {
    remove_meta_box('network_dashboard_right_now', 'dashboard-network', 'normal');
    remove_meta_box('dashboard_right_now', 'dashboard-network', 'normal');
  }

  if (disable_extras_is_enabled('wp', 'dashboard_activity')) {
    remove_meta_box('dashboard_activity', 'dashboard', 'normal');
  }

  if (disable_extras_is_enabled('wp', 'dashboard_quick_press')) {
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
  }

  if (disable_extras_is_enabled('wp', 'dashboard_primary')) {
    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_primary', 'dashboard-network', 'side');
  }

  if (disable_extras_is_enabled('wp', 'dashboard_site_health')) {
    remove_meta_box('dashboard_site_health', 'dashboard', 'normal');
  }

  if (disable_extras_is_enabled('wp', 'welcome_panel')) {
    remove_action('welcome_panel', 'wp_welcome_panel');
  }
}

/**
 * @param bool $show
 *
 * @return bool
 */
function disable_extras_wp_filter_admin_bar(bool $show): bool {
  if (is_admin()) {
    return $show;
  }

  if (disable_extras_is_enabled('wp', 'admin_bar')) {
    return false;
  }

  if (
    disable_extras_is_enabled('wp', 'admin_bar_non_admins')
    && !current_user_can('manage_options')
  ) {
    return false;
  }

  return $show;
}

/**
 * @param WP_Admin_Bar $wpAdminBar
 *
 * @return void
 */
function disable_extras_wp_remove_admin_bar_wp_logo(WP_Admin_Bar $wpAdminBar): void {
  $wpAdminBar->remove_node('wp-logo');
}

/**
 * @return void
 */
function disable_extras_wp_login_logo_styles(): void {
  $css = 'body.login h1.wp-login-logo.disable-extras-login-title{'
    . 'background:none;'
    . 'height:auto;'
    . 'width:auto;'
    . 'text-indent:0;'
    . 'overflow:visible;'
    . 'display:block;'
    . 'margin:0 0 24px;'
    . 'padding:0;'
    . 'font-size:20px;'
    . 'font-weight:600;'
    . 'line-height:1.3;'
    . 'text-align:center;'
    . 'color:#1d2327'
    . '}';

  wp_add_inline_style('login', $css);
}

/**
 * @return void
 */
function disable_extras_wp_login_logo_ob_start(): void {
  ob_start('disable_extras_wp_replace_login_logo');
}

/**
 * @return void
 */
function disable_extras_wp_login_logo_ob_end(): void {
  if (ob_get_level() > 0) {
    ob_end_flush();
  }
}

/**
 * @param string $html
 *
 * @return string
 */
function disable_extras_wp_replace_login_logo(string $html): string {
  $name = esc_html(get_bloginfo('name', 'display'));
  $replaced = preg_replace(
    '#<h1\b[^>]*\bwp-login-logo\b[^>]*>\s*<a\b[^>]*>.*?</a>\s*</h1>#is',
    '<h1 class="wp-login-logo disable-extras-login-title">' . $name . '</h1>',
    $html,
    1
  );

  return is_string($replaced) ? $replaced : $html;
}

/**
 * @return void
 */
function disable_extras_wp_disable_heartbeat(): void {
  $disableFront = disable_extras_is_enabled('wp', 'heartbeat_front');
  $disableAdmin = disable_extras_is_enabled('wp', 'heartbeat_admin');

  if ($disableFront && $disableAdmin) {
    wp_deregister_script('heartbeat');
    return;
  }

  if ($disableAdmin && is_admin()) {
    wp_deregister_script('heartbeat');
    return;
  }

  if ($disableFront && !is_admin()) {
    wp_deregister_script('heartbeat');
  }
}

/**
 * @param array<string, mixed> $settings
 *
 * @return array<string, mixed>
 */
function disable_extras_wp_slow_heartbeat(array $settings): array {
  $settings['interval'] = 60;

  return $settings;
}

/**
 * @return void
 */
function disable_extras_wp_hide_update_nags(): void {
  if (current_user_can('update_core')) {
    return;
  }

  remove_action('admin_notices', 'update_nag', 3);
  remove_action('network_admin_notices', 'update_nag', 3);
  remove_action('admin_notices', 'maintenance_nag');
  remove_action('network_admin_notices', 'maintenance_nag');
}

/**
 * @param array<string, mixed> $args
 * @param string               $url
 *
 * @return array<string, mixed>
 */
function disable_extras_wp_strip_update_phone_home(array $args, string $url): array {
  if (!str_contains($url, 'api.wordpress.org')) {
    return $args;
  }

  if (!empty($args['user-agent']) && is_string($args['user-agent'])) {
    $args['user-agent'] = trim((string) preg_replace('#;?\s*https?://\S+#', '', $args['user-agent']));
  }

  if (isset($args['headers']) && is_array($args['headers'])) {
    unset($args['headers']['wp_install'], $args['headers']['wp_blog']);
  }

  return $args;
}

/**
 * @return bool
 */
function disable_extras_wp_has_feed_option(): bool {
  foreach ([
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
  ] as $key) {
    if (disable_extras_is_enabled('wp', $key)) {
      return true;
    }
  }

  return false;
}

/**
 * @return void
 */
function disable_extras_wp_remove_feed_links(): void {
  if (disable_extras_is_enabled('wp', 'feed_global')) {
    add_filter('feed_links_show_posts_feed', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'feed_global_comments')) {
    add_filter('feed_links_show_comments_feed', '__return_false');

    // Core defaults post-comments head link to the global filter; keep it when only global is off.
    if (!disable_extras_is_enabled('wp', 'feed_post_comments')) {
      add_filter('feed_links_extra_show_post_comments_feed', '__return_true');
    }
  }

  if (disable_extras_is_enabled('wp', 'feed_post_comments')) {
    add_filter('feed_links_extra_show_post_comments_feed', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'feed_authors')) {
    add_filter('feed_links_extra_show_author_feed', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'feed_post_types')) {
    add_filter('feed_links_extra_show_post_type_archive_feed', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'feed_categories')) {
    add_filter('feed_links_extra_show_category_feed', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'feed_tags')) {
    add_filter('feed_links_extra_show_tag_feed', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'feed_custom_taxonomies')) {
    add_filter('feed_links_extra_show_tax_feed', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'feed_search')) {
    add_filter('feed_links_extra_show_search_feed', '__return_false');
  }

  // WP < 6.1: granular extra-feed filters above are no-ops; drop the whole action.
  if (
    disable_extras_is_enabled('wp', 'feed_authors')
    || disable_extras_is_enabled('wp', 'feed_post_types')
    || disable_extras_is_enabled('wp', 'feed_categories')
    || disable_extras_is_enabled('wp', 'feed_tags')
    || disable_extras_is_enabled('wp', 'feed_custom_taxonomies')
    || disable_extras_is_enabled('wp', 'feed_search')
  ) {
    remove_action('wp_head', 'feed_links_extra', 3);
  }

  if (
    disable_extras_is_enabled('wp', 'feed_global')
    && disable_extras_is_enabled('wp', 'feed_global_comments')
  ) {
    remove_action('wp_head', 'feed_links', 2);
  }
}

/**
 * @return void
 */
function disable_extras_wp_block_feeds(): void {
  if (!is_feed() || is_admin()) {
    return;
  }

  if (!disable_extras_wp_is_current_feed_disabled()) {
    return;
  }

  if (disable_extras_is_enabled('wp', 'feed_redirect')) {
    wp_safe_redirect(home_url('/'), 301);
    exit;
  }

  status_header(404);
  nocache_headers();
  wp_die(
    esc_html__('Feeds are disabled.', 'disable-extras'),
    '',
    ['response' => 404]
  );
}

/**
 * @return bool
 */
function disable_extras_wp_is_current_feed_disabled(): bool {
  $feed = (string) get_query_var('feed');

  if (
    disable_extras_is_enabled('wp', 'feed_atom_rdf')
    && in_array($feed, ['atom', 'rdf', 'rss'], true)
  ) {
    return true;
  }

  if (is_comment_feed()) {
    if (is_singular()) {
      return disable_extras_is_enabled('wp', 'feed_post_comments');
    }

    return disable_extras_is_enabled('wp', 'feed_global_comments');
  }

  if (is_author()) {
    return disable_extras_is_enabled('wp', 'feed_authors');
  }

  if (is_category()) {
    return disable_extras_is_enabled('wp', 'feed_categories');
  }

  if (is_tag()) {
    return disable_extras_is_enabled('wp', 'feed_tags');
  }

  if (is_tax()) {
    return disable_extras_is_enabled('wp', 'feed_custom_taxonomies');
  }

  if (is_search()) {
    return disable_extras_is_enabled('wp', 'feed_search');
  }

  if (is_post_type_archive()) {
    return disable_extras_is_enabled('wp', 'feed_post_types');
  }

  if (disable_extras_is_enabled('wp', 'feed_global')) {
    return true;
  }

  return false;
}

/**
 * @param string[] $urls
 * @param string   $relationType
 *
 * @return string[]
 */
function disable_extras_wp_filter_resource_hints(array $urls, string $relationType): array {
  if (
    $relationType === 'dns-prefetch'
    && disable_extras_is_enabled('wp', 'dns_prefetch')
  ) {
    return [];
  }

  if (
    in_array($relationType, ['preconnect', 'prefetch', 'prerender'], true)
    && disable_extras_is_enabled('wp', 'resource_hints')
  ) {
    return [];
  }

  return $urls;
}

/**
 * @param string $src
 *
 * @return string
 */
function disable_extras_wp_strip_core_ver(string $src): string {
  if (str_contains($src, 'ver=' . get_bloginfo('version'))) {
    $src = (string) remove_query_arg('ver', $src);
  }

  return $src;
}

/**
 * @return void
 */
function disable_extras_wp_dequeue_front(): void {
  if (disable_extras_is_enabled('wp', 'jquery_front') && !is_admin()) {
    wp_deregister_script('jquery');
  } elseif (disable_extras_is_enabled('wp', 'jquery_migrate') && !is_admin()) {
    wp_deregister_script('jquery-migrate');
  }

  if (disable_extras_is_enabled('wp', 'thickbox_front') && !is_admin()) {
    wp_dequeue_script('thickbox');
    wp_dequeue_style('thickbox');
  }

  if (disable_extras_is_enabled('wp', 'dashicons_guests') && !is_user_logged_in()) {
    wp_deregister_style('dashicons');
  }

  if (disable_extras_is_enabled('wp', 'block_styles') && !is_admin()) {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');
  }
}

/**
 * @param WP_Scripts $scripts
 *
 * @return void
 */
function disable_extras_wp_remove_jquery_migrate(WP_Scripts $scripts): void {
  if (is_admin()) {
    return;
  }

  if (isset($scripts->registered['jquery'])) {
    $scripts->registered['jquery']->deps = array_values(array_diff(
      $scripts->registered['jquery']->deps,
      ['jquery-migrate']
    ));
  }

  unset($scripts->registered['jquery-migrate']);
}

/**
 * @return void
 */
function disable_extras_wp_disable_comments(): void {
  add_filter('comments_open', '__return_false', 20, 2);
  add_filter('pings_open', '__return_false', 20, 2);
  add_filter('comments_array', '__return_empty_array', 10, 2);

  add_action('init', 'disable_extras_wp_remove_comment_support', 100);
  add_action('admin_menu', 'disable_extras_wp_remove_comments_menu');
  add_action('admin_init', 'disable_extras_wp_redirect_comments_admin');
  add_action('wp_dashboard_setup', 'disable_extras_wp_remove_comments_dashboard', 999);
  add_action('admin_bar_menu', 'disable_extras_wp_remove_comments_admin_bar', 999);
  add_action('wp_enqueue_scripts', 'disable_extras_wp_dequeue_comment_reply', 999);
}

/**
 * @return void
 */
function disable_extras_wp_remove_comment_support(): void {
  foreach (get_post_types() as $postType) {
    if (post_type_supports($postType, 'comments')) {
      remove_post_type_support($postType, 'comments');
    }

    if (post_type_supports($postType, 'trackbacks')) {
      remove_post_type_support($postType, 'trackbacks');
    }
  }
}

/**
 * @return void
 */
function disable_extras_wp_remove_comments_menu(): void {
  remove_menu_page('edit-comments.php');
}

/**
 * @return void
 */
function disable_extras_wp_redirect_comments_admin(): void {
  global $pagenow;

  if (!is_string($pagenow)) {
    return;
  }

  if ($pagenow === 'edit-comments.php' || $pagenow === 'options-discussion.php') {
    wp_safe_redirect(admin_url());
    exit;
  }
}

/**
 * @return void
 */
function disable_extras_wp_remove_comments_dashboard(): void {
  remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');
}

/**
 * @param WP_Admin_Bar $wpAdminBar
 *
 * @return void
 */
function disable_extras_wp_remove_comments_admin_bar(WP_Admin_Bar $wpAdminBar): void {
  $wpAdminBar->remove_node('comments');
}

/**
 * @return void
 */
function disable_extras_wp_dequeue_comment_reply(): void {
  wp_deregister_script('comment-reply');
}

/**
 * @return void
 */
function disable_extras_wp_block_author_query(): void {
  if (is_admin()) {
    return;
  }

  if (!isset($_GET['author'])) {
    return;
  }

  wp_safe_redirect(home_url('/'), 301);
  exit;
}

/**
 * @return void
 */
function disable_extras_wp_block_author_archives(): void {
  if (!is_author()) {
    return;
  }

  global $wp_query;
  $wp_query->set_404();
  status_header(404);
  nocache_headers();
}

/**
 * @param WP_Sitemaps_Provider|false $provider
 * @param string                     $name
 *
 * @return WP_Sitemaps_Provider|false
 */
function disable_extras_wp_remove_users_sitemap($provider, string $name) {
  if ($name === 'users') {
    return false;
  }

  return $provider;
}

/**
 * @param mixed $result
 *
 * @return mixed
 */
function disable_extras_wp_block_rest_guests($result) {
  if (true === $result || is_wp_error($result) || is_user_logged_in()) {
    return $result;
  }

  return new WP_Error(
    'rest_forbidden',
    __('REST API is disabled for guests.', 'disable-extras'),
    ['status' => 401]
  );
}

/**
 * @param mixed $result
 *
 * @return mixed
 */
function disable_extras_wp_block_rest_users($result) {
  if (is_user_logged_in()) {
    return $result;
  }

  if (!isset($_SERVER['REQUEST_URI'])) {
    return $result;
  }

  $uri = (string) $_SERVER['REQUEST_URI'];

  if (str_contains($uri, '/wp/v2/users')) {
    return new WP_Error(
      'rest_forbidden',
      __('REST users endpoint is disabled.', 'disable-extras'),
      ['status' => 401]
    );
  }

  return $result;
}

/**
 * @param array<string, mixed> $endpoints
 *
 * @return array<string, mixed>
 */
function disable_extras_wp_remove_users_endpoint(array $endpoints): array {
  if (is_user_logged_in()) {
    return $endpoints;
  }

  unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);

  return $endpoints;
}

/**
 * @param string[] $links
 *
 * @return void
 */
function disable_extras_wp_filter_outbound_pings(array &$links): void {
  $blockSelf = disable_extras_is_enabled('wp', 'self_pingbacks');
  $blockOutgoing = disable_extras_is_enabled('wp', 'pingbacks_outgoing');

  if ($blockSelf && $blockOutgoing) {
    $links = [];
    return;
  }

  $home = (string) get_option('home');

  foreach ($links as $index => $link) {
    $isSelf = str_starts_with($link, $home);

    if ($blockSelf && $isSelf) {
      unset($links[$index]);
      continue;
    }

    if ($blockOutgoing && !$isSelf) {
      unset($links[$index]);
    }
  }
}

/**
 * @param array<string, mixed> $methods
 *
 * @return array<string, mixed>
 */
function disable_extras_wp_remove_pingback_methods(array $methods): array {
  unset($methods['pingback.ping'], $methods['pingback.extensions.getPingbacks']);

  return $methods;
}

/**
 * @param array<string, string> $headers
 *
 * @return array<string, string>
 */
function disable_extras_wp_remove_pingback_header(array $headers): array {
  unset($headers['X-Pingback']);

  return $headers;
}
