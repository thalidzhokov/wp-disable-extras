<?php

defined('ABSPATH') || exit;

/**
 * @return void
 */
function disable_extras_admin_boot(): void {
  add_action('admin_menu', 'disable_extras_admin_menu');
  add_action('admin_init', 'disable_extras_admin_register');
  add_action('admin_enqueue_scripts', 'disable_extras_admin_assets');
  add_filter('plugin_action_links_' . plugin_basename(DISABLE_EXTRAS_FILE), 'disable_extras_admin_action_links');
}

/**
 * @param string $hook
 *
 * @return void
 */
function disable_extras_admin_assets(string $hook): void {
  if ($hook !== 'settings_page_disable-extras') {
    return;
  }

  $css = '
    .disable-extras-section {
      margin: 1.5em 0 0.5em;
      padding-bottom: 0.35em;
      border-bottom: 1px solid #c3c4c7;
      font-size: 1.1em;
    }
    .disable-extras-hints {
      margin-top: 10px;
      max-width: 640px;
    }
    .disable-extras-hints p {
      margin: 0.6em 0 0;
      color: #50575e;
      font-size: 13px;
      line-height: 1.5;
    }
    .disable-extras-hints p:first-child {
      margin-top: 0;
    }
    .disable-extras-hints code {
      padding: 0;
      font: 12px/1.45 Consolas, Monaco, monospace;
      background: transparent;
    }
    .disable-extras-hint {
      margin-top: 10px;
    }
    .disable-extras-hint:first-child {
      margin-top: 0;
    }
    .disable-extras-hint-title {
      display: block;
      margin: 0 0 4px;
      color: #646970;
      font-size: 12px;
      font-weight: 600;
    }
    .disable-extras-label-note {
      display: block;
      margin-top: 4px;
      color: #b32d2e;
      font-size: 12px;
      font-weight: 400;
    }
    .disable-extras-label-note.is-ok {
      color: #007017;
    }
    .disable-extras-label-note.is-muted {
      color: #646970;
    }
    .disable-extras-code {
      margin: 0;
      padding: 10px 12px;
      overflow: auto;
      background: #1d2327;
      color: #f0f0f1;
      border-radius: 4px;
      font: 12px/1.5 Consolas, Monaco, monospace;
      white-space: pre-wrap;
      word-break: break-word;
    }
    .disable-extras-code-part {
      display: block;
    }
    .disable-extras-code-part + .disable-extras-code-part {
      margin-top: 10px;
      padding-top: 10px;
      border-top: 1px solid #3c434a;
    }
    .disable-extras-sep {
      margin: 6px 0;
      color: #646970;
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.02em;
    }
    .disable-extras-where-line {
      margin: 0;
      color: #1d2327;
      font-size: 13px;
      line-height: 1.45;
    }
  ';

  wp_register_style('disable-extras-admin', false, [], DISABLE_EXTRAS_VERSION);
  wp_enqueue_style('disable-extras-admin');
  wp_add_inline_style('disable-extras-admin', $css);
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
 * @param string|list<string> $parts
 *
 * @return list<string>
 */
function disable_extras_admin_hint_parts($parts): array {
  if (is_string($parts)) {
    $trimmed = trim($parts);

    return $trimmed === '' ? [] : [__($trimmed, 'disable-extras')];
  }

  if (!is_array($parts)) {
    return [];
  }

  $out = [];

  foreach ($parts as $part) {
    if (!is_string($part)) {
      continue;
    }

    if ($part === 'or' || $part === 'and_also') {
      $out[] = $part;
      continue;
    }

    $trimmed = trim($part);

    if ($trimmed !== '') {
      $out[] = __($trimmed, 'disable-extras');
    }
  }

  return $out;
}

/**
 * @param string $sep
 *
 * @return string
 */
function disable_extras_admin_hint_sep_label(string $sep): string {
  if ($sep === 'or') {
    return __('OR', 'disable-extras');
  }

  return $sep;
}

/**
 * @param string|list<string> $parts
 * @param string $mode what|where
 *
 * @return void
 */
function disable_extras_admin_render_hint_parts($parts, string $mode): void {
  $items = disable_extras_admin_hint_parts($parts);

  if ($items === []) {
    return;
  }

  if ($mode === 'where') {
    $groups = [];
    $current = [];

    foreach ($items as $item) {
      if ($item === 'or') {
        if ($current !== []) {
          $groups[] = implode(', ', $current);
          $current = [];
        }

        $groups[] = null;
        continue;
      }

      if ($item === 'and_also') {
        continue;
      }

      $current[] = $item;
    }

    if ($current !== []) {
      $groups[] = implode(', ', $current);
    }

    foreach ($groups as $group) {
      if ($group === null) {
        echo '<div class="disable-extras-sep">' . esc_html(disable_extras_admin_hint_sep_label('or')) . '</div>';
        continue;
      }

      echo '<p class="disable-extras-where-line">' . esc_html($group) . '</p>';
    }

    return;
  }

  $groups = [];
  $current = [];

  foreach ($items as $item) {
    if ($item === 'or') {
      if ($current !== []) {
        $groups[] = $current;
        $current = [];
      }

      $groups[] = null;
      continue;
    }

    if ($item === 'and_also') {
      continue;
    }

    $current[] = $item;
  }

  if ($current !== []) {
    $groups[] = $current;
  }

  foreach ($groups as $group) {
    if ($group === null) {
      echo '<div class="disable-extras-sep">' . esc_html(disable_extras_admin_hint_sep_label('or')) . '</div>';
      continue;
    }

    echo '<pre class="disable-extras-code">';

    foreach ($group as $part) {
      echo '<span class="disable-extras-code-part">' . esc_html($part) . '</span>';
    }

    echo '</pre>';
  }
}

/**
 * @param string $tab
 * @param string $key
 * @param string $label
 * @param array<string, bool> $options
 * @param array<string, array<string, array<string, mixed>>> $hints
 *
 * @return void
 */
function disable_extras_admin_render_option_row(
  string $tab,
  string $key,
  string $label,
  array $options,
  array $hints
): void {
  $hint = $hints[$tab][$key] ?? [];
  $labelNote = isset($hint['label_note']) ? trim((string) $hint['label_note']) : '';
  $labelNoteTone = isset($hint['label_note_tone']) ? (string) $hint['label_note_tone'] : 'warn';
  $summary = $hint['summary'] ?? null;
  $what = $hint['what'] ?? null;
  $where = $hint['where'] ?? null;
  $constant = isset($hint['constant']) ? trim((string) $hint['constant']) : '';
  $summaryParts = [];

  if (is_string($summary)) {
    $summary = trim($summary);

    if ($summary !== '') {
      $summaryParts = [$summary];
    }
  } elseif (is_array($summary)) {
    foreach ($summary as $part) {
      if (!is_string($part)) {
        continue;
      }

      $part = trim($part);

      if ($part !== '') {
        $summaryParts[] = $part;
      }
    }
  }

  $hasSummary = $summaryParts !== [];
  $hasWhat = !$hasSummary && $what !== null && disable_extras_admin_hint_parts($what) !== [];
  $hasWhere = !$hasSummary && $where !== null && disable_extras_admin_hint_parts($where) !== [];
  $hasConstant = $constant !== '';
  $labelNoteClass = 'disable-extras-label-note';

  if ($labelNoteTone === 'ok') {
    $labelNoteClass .= ' is-ok';
  } elseif ($labelNoteTone === 'muted') {
    $labelNoteClass .= ' is-muted';
  }
  ?>
  <tr>
    <th scope="row">
      <?php echo esc_html($label); ?>
      <?php if ($labelNote !== '') : ?>
        <span class="<?php echo esc_attr($labelNoteClass); ?>"><?php echo esc_html(__($labelNote, 'disable-extras')); ?></span>
      <?php endif; ?>
    </th>
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
      <?php if ($hasSummary || $hasWhat || $hasWhere || $hasConstant) : ?>
        <div class="disable-extras-hints">
          <?php foreach ($summaryParts as $summaryPart) : ?>
            <p><?php echo wp_kses(__($summaryPart, 'disable-extras'), ['code' => []]); ?></p>
          <?php endforeach; ?>
          <?php if ($hasWhat) : ?>
            <div class="disable-extras-hint">
              <span class="disable-extras-hint-title"><?php echo esc_html__('What is removed', 'disable-extras'); ?></span>
              <?php disable_extras_admin_render_hint_parts($what, 'what'); ?>
            </div>
          <?php endif; ?>
          <?php if ($hasWhere) : ?>
            <div class="disable-extras-hint">
              <span class="disable-extras-hint-title"><?php echo esc_html__('Where it is removed', 'disable-extras'); ?></span>
              <?php disable_extras_admin_render_hint_parts($where, 'where'); ?>
            </div>
          <?php endif; ?>
          <?php if ($hasConstant) : ?>
            <p>
              <?php echo esc_html__('Can be disabled with a constant in wp-config.php', 'disable-extras'); ?><br>
              <code><?php echo esc_html($constant); ?></code>
            </p>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </td>
  </tr>
  <?php
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
  $hints = disable_extras_option_hints();
  $sections = disable_extras_option_sections();
  $tabs = disable_extras_admin_tabs();
  $tab = isset($_GET['tab']) ? sanitize_key((string) $_GET['tab']) : 'wp';

  if (!isset($tabs[$tab]) || !$tabs[$tab]['active']) {
    $tab = 'wp';
  }

  $baseUrl = admin_url('options-general.php?page=disable-extras');
  $tabSections = $sections[$tab] ?? [];
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

      <?php foreach ($tabSections as $section) : ?>
        <h2 class="disable-extras-section"><?php echo esc_html($section['title']); ?></h2>
        <table class="form-table" role="presentation">
          <?php foreach ($section['keys'] as $key) : ?>
            <?php
            if (!isset($labels[$tab][$key])) {
              continue;
            }

            disable_extras_admin_render_option_row(
              $tab,
              $key,
              $labels[$tab][$key],
              $options,
              $hints
            );
            ?>
          <?php endforeach; ?>
        </table>
      <?php endforeach; ?>

      <?php submit_button(__('Save Changes', 'disable-extras')); ?>
    </form>
  </div>
  <?php
}
