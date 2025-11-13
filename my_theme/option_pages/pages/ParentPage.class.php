<?php

namespace option_pages\pages;

use option_pages\pages\PageBase;

class ParentPage extends PageBase
{

  /**
   * 権限名
   * @var string
   */
  protected string $capability;

  /**
   * ダッシュアイコンの文字列
   * @var string
   */
  protected string $dashIcon;

  /**
   * Undocumented function
   *
   * @param string $slug : メニューページスラッグ文字列
   * @param string $label : メニューラベル文字列。タイトル文字列にも使用される
   * @param string $capability : 権限名
   * @param string $dashIcon : ダッシュアイコンの文字列
   */
  public function __construct(string $slug, string $label, string $capability = 'manage_options', string $dashIcon = 'dashicons-admin-generic')
  {
    parent::__construct($slug, $label);
    $this->capability = $capability;
    $this->dashIcon = $dashIcon;
  }

  public function addMenuCallback(): void
  {
    // 親ページ登録
    add_menu_page(
      $this->pageLabel,
      $this->pageLabel,
      $this->capability,
      $this->pageSlug,
      [$this, 'renderPageCallback'],
      $this->dashIcon,
      21
    );
  }
}
