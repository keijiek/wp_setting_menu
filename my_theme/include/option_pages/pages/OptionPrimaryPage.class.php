<?php

namespace include\option_pages\pages;

use \include\option_pages\pages\PageBase;

class OptionPrimaryPage extends PageBase
{
  const PAGE_SLUG = 'option_primary_page';
  const LABEL = '共通設定';
  const CAPABILITY = 'manage_options';

  public function __construct(
    string $parentPageSlug,
    string $pageSlug,
    string $pageLabel,
    string $capability,
    int $position
  ) {
    parent::__construct(
      $parentPageSlug,
      $pageSlug,
      $pageLabel,
      $capability,
      $position
    );
  }
}
