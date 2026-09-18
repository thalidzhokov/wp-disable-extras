<?php

defined('ABSPATH') || exit;

/**
 * @return void
 */
function disable_extras_embedpress_boot(): void {
  add_action('plugins_loaded', 'disable_extras_embedpress_apply', 30);
}

/**
 * @return void
 */
function disable_extras_embedpress_apply(): void {
  if (!disable_extras_is_embedpress_active()) {
    return;
  }

  if (disable_extras_is_enabled('embedpress', 'assets_outside_single')) {
    add_action('wp_enqueue_scripts', 'disable_extras_embedpress_dequeue_assets', 999);
    add_action('wp_print_styles', 'disable_extras_embedpress_dequeue_styles', 100);
  }

  if (disable_extras_is_enabled('embedpress', 'gallery_justify')) {
    add_action('wp_print_scripts', 'disable_extras_embedpress_dequeue_gallery_justify', 100);
  }
}

/**
 * @return void
 */
function disable_extras_embedpress_dequeue_assets(): void {
  if (is_single()) {
    return;
  }

  wp_deregister_style('embedpress_blocks-cgb-style-css');
  wp_deregister_style('embedpress');
  wp_deregister_style('plyr');
  wp_deregister_script('plyr.polyfilled');
  wp_deregister_script('embedpress-pdfobject');
  wp_deregister_script('embedpress_documents_viewer_script');
}

/**
 * @return void
 */
function disable_extras_embedpress_dequeue_styles(): void {
  if (is_single()) {
    return;
  }

  wp_dequeue_style('embedpress-css');
  wp_dequeue_style('embedpress-blocks-style');
}

/**
 * @return void
 */
function disable_extras_embedpress_dequeue_gallery_justify(): void {
  wp_dequeue_script('embedpress-gallery-justify');
}
