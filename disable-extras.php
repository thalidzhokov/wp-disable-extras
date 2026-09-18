<?php
/**
 * Plugin Name: Disable Extras
 * Plugin URI: https://github.com/thalidzhokov/disable-extras
 * Description: Отключение лишнего в ядре WordPress, Yoast SEO, Redis Object Cache и EmbedPress.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Albert Thalidzhokov
 * Author URI: https://thalidzhokov.ru
 * Text Domain: disable-extras
 */

defined('ABSPATH') || exit;

define('DISABLE_EXTRAS_VERSION', '1.0.0');
define('DISABLE_EXTRAS_FILE', __FILE__);
define('DISABLE_EXTRAS_DIR', plugin_dir_path(__FILE__));
define('DISABLE_EXTRAS_OPTION', 'disable_extras_options');

require_once DISABLE_EXTRAS_DIR . 'includes/options.php';
require_once DISABLE_EXTRAS_DIR . 'includes/wp.php';
require_once DISABLE_EXTRAS_DIR . 'includes/yoast.php';
require_once DISABLE_EXTRAS_DIR . 'includes/redis.php';
require_once DISABLE_EXTRAS_DIR . 'includes/embedpress.php';
require_once DISABLE_EXTRAS_DIR . 'includes/admin.php';

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
