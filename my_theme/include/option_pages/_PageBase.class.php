<?php

namespace include\option_pages;

abstract class PageBase
{
  protected string $parentPageSlug;
  protected string $pageSlug;
  protected string $label;
  protected string $capability;
  protected string $position;

  public function __construct(
    string $parentPageSlug,
    string $pageSlug,
    string $label,
    string $capability,
    string $position
  ) {
    $this->parentPageSlug = $parentPageSlug;
    $this->pageSlug = $pageSlug;
    $this->label = $label;
    $this->capability = $capability;
    $this->position = $position;
    add_action('admin_menu', [$this, 'addSubMenu']);
  }

  public function addSubMenu(): void
  {
    add_submenu_page(
      $this->parentPageSlug,
      $this->label . '画面',
      $this->label,
      $this->capability,
      $this->pageSlug,
      [$this, 'renderPage'],
      $this->position
    );
  }
  abstract public function renderPage(): void;
}
