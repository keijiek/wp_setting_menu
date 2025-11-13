<?php

namespace option_pages;

use option_pages\pages\ParentPage;

class OptionPagesRegister
{
  /**
   * 親ページの add_menu_page() 用のダッシュアイコン文字列
   */
  const DASH_ICON = 'dashicons-admin-generic';

  /**
   * 親ページ
   * @var ParentPage|null
   */
  private $parentPage = null;

  private array $subPages;


  public function __construct()
  {
    $this->parentPage = new ParentPage('parent_page', '共通設定');
    $this->subPages = [];
    add_action('admin_menu', [$this, 'addOptionPages']);
  }


  public function addOptionPages(): void
  {
    if ($this->parentPage === null) {
      return;
    }
    $this->parentPage->addMenuCallback();
  }
}
