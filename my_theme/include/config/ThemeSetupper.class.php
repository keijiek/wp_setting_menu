<?php

// if (! defined('ABSPATH')) {
//   exit;
// }

namespace include\config;

class ThemeSetupper
{
  public function __construct()
  {
    add_action('after_setup_theme', [$this, 'callbackFunc']);
  }

  public function callbackFunc(): void
  {
    add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets']);
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    // add_theme_support('custom-logo', ['height' => 100, 'width' => 400, 'flex-height' => true, 'flex-width' => true]);
    // add_theme_support('custom-background');
    // add_theme_support('custom-header');
    // add_theme_support('editor-styles');
    // add_editor_style('editor-style.css');
  }
}
