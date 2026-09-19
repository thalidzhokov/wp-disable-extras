<?php
/**
 * Plugin Name: Disable Extras
 * Plugin URI: https://github.com/thalidzhokov/disable-extras
 * Description: Disable extras in WordPress core, Yoast SEO, Redis Object Cache, and EmbedPress.
 * Version: 1.2.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Albert Thalidzhokov
 * Author URI: https://thalidzhokov.ru
 * Text Domain: disable-extras
 * Domain Path: /languages
 */

defined('ABSPATH') || exit;

define('DISABLE_EXTRAS_VERSION', '1.2.0');
define('DISABLE_EXTRAS_FILE', __FILE__);
define('DISABLE_EXTRAS_DIR', plugin_dir_path(__FILE__));
define('DISABLE_EXTRAS_OPTION', 'disable_extras_options');

require_once DISABLE_EXTRAS_DIR . 'includes/options.php';
require_once DISABLE_EXTRAS_DIR . 'includes/wp.php';
require_once DISABLE_EXTRAS_DIR . 'includes/yoast.php';
require_once DISABLE_EXTRAS_DIR . 'includes/redis.php';
require_once DISABLE_EXTRAS_DIR . 'includes/embedpress.php';
require_once DISABLE_EXTRAS_DIR . 'includes/admin.php';

add_action('init', 'disable_extras_load_textdomain');

/**
 * @return void
 */
function disable_extras_load_textdomain(): void {
  load_plugin_textdomain(
    'disable-extras',
    false,
    dirname(plugin_basename(DISABLE_EXTRAS_FILE)) . '/languages'
  );
}

register_activation_hook(__FILE__, static function (): void {
  if (get_option(DISABLE_EXTRAS_OPTION) === false) {
    add_option(DISABLE_EXTRAS_OPTION, disable_extras_default_options());
  }
});

disable_extras_redis_define_constants();
disable_extras_wp_boot();
disable_extras_yoast_boot();
disable_extras_redis_boot();
disable_extras_embedpress_boot();

if (is_admin()) {
  disable_extras_admin_boot();
}
