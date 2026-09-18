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

  if (disable_extras_is_enabled('wp', 'xmlrpc')) {
    add_filter('xmlrpc_enabled', '__return_false');
    remove_action('wp_head', 'rsd_link');
  }

  if (disable_extras_is_enabled('wp', 'oembed_discovery')) {
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
    add_filter('embed_oembed_discover', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'oembed_endpoint')) {
    remove_action('rest_api_init', 'wp_oembed_register_route');
  }

  if (disable_extras_is_enabled('wp', 'rest_users')) {
    add_filter('rest_authentication_errors', 'disable_extras_wp_block_rest_users', 99);
    add_filter('rest_endpoints', 'disable_extras_wp_remove_users_endpoint');
  }

  if (disable_extras_is_enabled('wp', 'heartbeat_front') && !is_admin()) {
    wp_deregister_script('heartbeat');
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

  if (disable_extras_is_enabled('wp', 'application_passwords')) {
    add_filter('wp_is_application_passwords_available', '__return_false');
  }

  if (disable_extras_is_enabled('wp', 'core_sitemaps')) {
    add_filter('wp_sitemaps_enabled', '__return_false');
  }

  if (
    disable_extras_is_enabled('wp', 'jquery_front')
    || disable_extras_is_enabled('wp', 'thickbox_front')
    || disable_extras_is_enabled('wp', 'dashicons_guests')
  ) {
    add_action('wp_enqueue_scripts', 'disable_extras_wp_dequeue_front', 999);
  }
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
  }

  if (disable_extras_is_enabled('wp', 'thickbox_front') && !is_admin()) {
    wp_dequeue_script('thickbox');
    wp_dequeue_style('thickbox');
  }

  if (disable_extras_is_enabled('wp', 'dashicons_guests') && !is_user_logged_in()) {
    wp_deregister_style('dashicons');
  }
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
