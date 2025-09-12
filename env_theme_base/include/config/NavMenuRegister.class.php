<?php

namespace include\config;

class NavMenuRegister
{
  /**
   * メニューロケーション: グローバル
   */
  public const GLOBAL_MENU_LOCATION = 'global_nav';

  /**
   * メニューロケーション: サブ
   */
  public const SUB_MENU_LOCATION = 'sub_nav';

  public function __construct()
  {
    add_action('after_setup_theme', [$this, 'callBackFunc']);
  }

  public function callBackFunc(): void
  {
    register_nav_menus([
      self::GLOBAL_MENU_LOCATION => 'global-menu',
      self::SUB_MENU_LOCATION => 'sub-menu'
    ]);
  }
}
