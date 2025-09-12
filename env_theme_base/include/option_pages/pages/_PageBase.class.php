<?php

namespace include\option_pages\pages;

abstract class PageBase
{
  /**
   * 親ページのスラグ
   * @var string
   */
  protected string $parentPageSlug;

  /**
   * ページのスラグ
   * @var string
   */
  protected string $pageSlug;

  /**
   * ページのラベル
   * @var string
   */
  protected string $label;

  /**
   * ページの操作に必要な権限
   * @var string
   */
  protected string $capability;

  /**
   * 親ページメニュー内のページ項目の位置
   * @var string
   */
  protected string $position;

  /**
   * setting インスタンスの配列
   * @var array<SettingBase>
   */
  protected array $settings = [];

  /**
   * @param string $parentPageSlug : 親ページのスラグ。
   * @param string $pageSlug : ページのスラグ。admin.php?page=*** のクエリパラメータに使われる
   * @param string $label : ページのラベル。メニュー名、ページタイトルに使われる。
   * @param string $capability : ページ操作に必要な権限
   * @param string $position : 親ページメニュー内のページ項目の位置
   */
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


  /**
   * コンストラクタ内の add_action('admin_menu', [$this, 'addSubMenu']) で参照されるコールバック関数。
   * このページの設定、add_submenu_page() を行う。
   * @return void
   */
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


  /**
   * add_submenu_page() の第6引数に指定する、フォーム表示用コールバック関数。
   * @return void
   */
  public function renderPage(): void
  {
?>
    <div class="wrap">
      <h1><?= esc_html(get_admin_page_title()) ?></h1>
      <form method="post" action="options.php">
        <?php
        foreach ($this->settings as $setting) {
          $setting->getSettingsFields();
        }
        do_settings_sections($this->pageSlug);
        submit_button();
        ?>
      </form>
    </div>
<?php
  }
}
