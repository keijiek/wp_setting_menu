<?php


namespace include\option_pages;

use include\option_pages\PageBase;

class SubPage01 extends PageBase
{
  // ページのスラグ
  const PAGE_SLUG = 'carousel_page';

  // ページのラベル
  const LABEL = 'カルーセル設定';

  // 権限
  const CAPABILITY = 'manage_options';

  public function __construct(string $parentPageSlug, int $position)
  {
    parent::__construct($parentPageSlug, self::PAGE_SLUG, self::LABEL, self::CAPABILITY, $position);
  }

  public function renderPage(): void
  {
?>
    <div class="wrap">
      <h1><?= esc_html(get_admin_page_title()) ?></h1>
      <form method="post" action="options.php">
        <?php
        // $this->addressSetting->getSettingsFields();
        do_settings_sections($this->pageSlug);
        submit_button();
        ?>
      </form>
    </div>
<?php
  }
}
