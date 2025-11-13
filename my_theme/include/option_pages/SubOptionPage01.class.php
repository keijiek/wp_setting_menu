<?php

namespace include\option_pages;

use include\option_pages\settings\AddressSetting;
use include\option_pages\PageBase;

class SubOptionPage01 extends PageBase
{
  // ページのスラグ
  const PAGE_SLUG = 'address_and_contact_page';

  // ページのラベル
  const LABEL = '住所・連絡先';

  // 権限
  const CAPABILITY = 'manage_options';


  private AddressSetting $addressSetting;

  public function __construct(string $parentPageSlug, int $position)
  {
    parent::__construct($parentPageSlug, self::PAGE_SLUG, self::LABEL, self::CAPABILITY, $position);

    $this->addressSetting = new AddressSetting(self::PAGE_SLUG);
  }


  public function renderPage(): void
  {
?>
    <div class="wrap">
      <h1><?= esc_html(get_admin_page_title()) ?></h1>
      <form method="post" action="options.php">
        <?php
        $this->addressSetting->getSettingsFields();
        do_settings_sections(self::PAGE_SLUG);
        submit_button();
        ?>
      </form>
    </div>
<?php
  }
}
