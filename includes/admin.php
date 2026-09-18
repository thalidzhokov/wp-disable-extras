<?php

defined('ABSPATH') || exit;

/**
 * @return void
 */
function disable_extras_admin_boot(): void {
  add_action('admin_menu', 'disable_extras_admin_menu');
  add_action('admin_init', 'disable_extras_admin_register');
  add_filter('plugin_action_links_' . plugin_basename(DISABLE_EXTRAS_FILE), 'disable_extras_admin_action_links');
}

/**
 * @return void
 */
function disable_extras_admin_menu(): void {
  add_options_page(
    __('Disable Extras', 'disable-extras'),
    __('Disable Extras', 'disable-extras'),
    'manage_options',
    'disable-extras',
    'disable_extras_admin_render_page'
  );
}

/**
 * @return void
 */
function disable_extras_admin_register(): void {
  register_setting('disable_extras', DISABLE_EXTRAS_OPTION, [
    'type' => 'array',
    'sanitize_callback' => 'disable_extras_sanitize_options',
    'default' => disable_extras_default_options(),
  ]);
}

/**
 * @param string[] $links
 *
 * @return string[]
 */
function disable_extras_admin_action_links(array $links): array {
  $url = admin_url('options-general.php?page=disable-extras');
  array_unshift(
    $links,
    '<a href="' . esc_url($url) . '">' . esc_html__('Settings', 'disable-extras') . '</a>'
  );

  return $links;
}

/**
 * @return array<string, array{label: string, active: bool}>
 */
function disable_extras_admin_tabs(): array {
  if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
  }

  return [
    'wp' => [
      'label' => __('WP', 'disable-extras'),
      'active' => true,
    ],
    'yoast' => [
      'label' => __('Yoast', 'disable-extras'),
      'active' => disable_extras_is_yoast_active(),
    ],
    'redis' => [
      'label' => __('Redis Object Cache', 'disable-extras'),
      'active' => disable_extras_is_redis_active(),
    ],
    'embedpress' => [
      'label' => __('EmbedPress', 'disable-extras'),
      'active' => disable_extras_is_embedpress_active(),
    ],
  ];
}

/**
 * @return void
 */
function disable_extras_admin_render_page(): void {
  if (!current_user_can('manage_options')) {
    return;
  }

  $options = disable_extras_get_options();
  $labels = disable_extras_option_labels();
  $tabs = disable_extras_admin_tabs();
  $tab = isset($_GET['tab']) ? sanitize_key((string) $_GET['tab']) : 'wp';

  if (!isset($tabs[$tab]) || !$tabs[$tab]['active']) {
    $tab = 'wp';
  }

  $baseUrl = admin_url('options-general.php?page=disable-extras');
  ?>
  <div class="wrap">
    <h1><?php echo esc_html__('Disable Extras', 'disable-extras'); ?></h1>
    <p><?php echo esc_html__('A checked option disables that feature. Defaults match what the theme used to turn off.', 'disable-extras'); ?></p>

    <nav class="nav-tab-wrapper" style="margin-bottom: 1em;">
      <?php foreach ($tabs as $slug => $meta) : ?>
        <?php if ($meta['active']) : ?>
          <a
            href="<?php echo esc_url($baseUrl . '&tab=' . $slug); ?>"
            class="nav-tab <?php echo $tab === $slug ? 'nav-tab-active' : ''; ?>"
          ><?php echo esc_html($meta['label']); ?></a>
        <?php else : ?>
          <span
            class="nav-tab"
            style="opacity: .45; cursor: not-allowed;"
            title="<?php echo esc_attr__('Plugin is not installed or active', 'disable-extras'); ?>"
          ><?php echo esc_html($meta['label']); ?></span>
        <?php endif; ?>
      <?php endforeach; ?>
    </nav>

    <form method="post" action="options.php">
      <?php settings_fields('disable_extras'); ?>

      <table class="form-table" role="presentation">
        <?php foreach ($labels[$tab] as $key => $label) : ?>
          <tr>
            <th scope="row"><?php echo esc_html($label); ?></th>
            <td>
              <label>
                <input
                  type="checkbox"
                  name="<?php echo esc_attr(DISABLE_EXTRAS_OPTION . '[' . $tab . '][' . $key . ']'); ?>"
                  value="1"
                  <?php checked(!empty($options[$tab][$key])); ?>
                >
                <?php echo esc_html__('Disable', 'disable-extras'); ?>
              </label>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>

      <?php submit_button(__('Save Changes', 'disable-extras')); ?>
    </form>
  </div>
  <?php
}
