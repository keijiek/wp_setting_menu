<?php

namespace include\option_pages\pages;

use include\option_pages\pages\PageBase;
use include\option_pages\settings\AddressSetting;

class AddressAndContact extends PageBase
{
  const PAGE_SLUG = 'address_and_contact';
  const LABEL = '連絡先・住所';
  const CAPABILITY = 'manage_options';

  /**
   * コンストラクタ
   * @param string $parentPageSlug : 親ページのスラグ
   * @param integer $position : 親ページメニュー内のページ項目の位置
   */
  public function __construct(string $parentPageSlug, string $pageSlug, string $pageLabel, string $capability, int $position)
  {
    parent::__construct(
      $parentPageSlug,
      $pageSlug,
      $pageLabel,
      $capability,
      $position
    );
    $this->settings[] = new AddressSetting($pageSlug);
  }
}
