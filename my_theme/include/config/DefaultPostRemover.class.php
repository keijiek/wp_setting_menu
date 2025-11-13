<?php

namespace include\config;

class DefaultPostRemover
{
  public function __construct()
  {
    // デフォルト投稿の設定値変更による無効化。
    add_action('init', [$this, 'disableDefaultPostType'], 999);

    // デフォルト・タクソノミーの設定値変更による無効化
    add_action('init', [$this, 'disableDefaultTaxonomies'], 999);
  }


  /**
   * 投稿の設定値変更による無効化
   * @return void
   */
  public function disableDefaultPostType(): void
  {
    global $wp_post_types;
    if (isset($wp_post_types['post'])) {
      $wp_post_types['post']->public = false;
      $wp_post_types['post']->exclude_from_search = true;
      $wp_post_types['post']->publicly_queryable = false;
      $wp_post_types['post']->show_in_rest = false;
      $wp_post_types['post']->show_in_nav_menus = false;
      $wp_post_types['post']->show_ui = false;
      $wp_post_types['post']->show_in_menu = false;
      $wp_post_types['post']->show_in_admin_bar = false;
    }
  }


  /**
   * デフォルト・タクソノミーの設定値変更による無効化
   * @return void
   */
  public function disableDefaultTaxonomies(): void
  {
    global $wp_taxonomies;
    if (isset($wp_taxonomies['category'])) {
      $wp_taxonomies['category']->public = false;
      $wp_taxonomies['category']->show_ui = false;
      $wp_taxonomies['category']->show_in_menu = false;
      $wp_taxonomies['category']->show_in_nav_menus = false;
      $wp_taxonomies['category']->show_in_rest = false;
      $wp_taxonomies['category']->show_admin_column = false;
      $wp_taxonomies['category']->show_in_quick_edit = false;
    }
    if (isset($wp_taxonomies['post_tag'])) {
      $wp_taxonomies['post_tag']->public = false;
      $wp_taxonomies['post_tag']->show_ui = false;
      $wp_taxonomies['post_tag']->show_in_menu = false;
      $wp_taxonomies['post_tag']->show_in_nav_menus = false;
      $wp_taxonomies['post_tag']->show_in_rest = false;
      $wp_taxonomies['post_tag']->show_admin_column = false;
      $wp_taxonomies['post_tag']->show_in_quick_edit = false;
    }
  }
}
