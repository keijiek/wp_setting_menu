<?php

namespace option_pages\pages;

abstract class PageBase
{
  /**
   * このページのスラグ
   * @var string
   */
  protected string $pageSlug;

  /**
   * このページのラベル。管理画面の表示名。
   * @var string
   */
  protected string $pageLabel;

  /**
   * 子ページにとっての親ページのスラグ。
   * 親ページ自身は空文字となる。
   * @var string
   */
  protected string $parentPageSlug;

  /**
   * ページにアクセスできるユーザーの権限
   * @var string
   */
  protected string $capability;

  /**
   * このページが持つ「オプショングループ」インスタンスの配列
   * @var array
   */
  protected array $optionGroups = [];

  /**
   * コンストラクタ
   * @param string $pageSlug
   * @param string $pageLabel
   * @param string $parentPageSlug
   * @param string $capability
   */
  public function __construct(
    string $pageSlug,
    string $pageLabel,
    string $parentPageSlug = '',
    string $capability = 'edit_pages'
  ) {
    $this->parentPageSlug = $parentPageSlug;
    $this->pageSlug = $pageSlug;
    $this->pageLabel = $pageLabel;
    $this->capability = $capability;
  }

  /**
   * このページを add する。
   * add_menu_page か、add_submenu_page を呼び出す。
   * @return void
   */
  public function addMenuCallback(): void
  {
    if ($this->parentPageSlug === '') {
      // 親ページ
      add_menu_page(
        $this->pageLabel,
        $this->pageLabel,
        $this->capability,
        $this->pageSlug,
        [$this, 'renderPageCallback'],
        'dashicons-admin-generic',
        21
      );
    } else {
      // 子ページ
      add_submenu_page(
        $this->parentPageSlug,
        $this->pageLabel,
        $this->pageLabel,
        $this->capability,
        $this->pageSlug,
        [$this, 'renderPageCallback']
      );
    }
  }

  /**
   * このページの内容物を echo するコールバック関数
   *
   * @return void
   */
  public function renderPageCallback(): void
  {
    // html のラップは div.wrap の使用が推奨されている
?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <?php
      settings_errors(); // エラーや成功メッセージの表示
      if (!empty($this->optionGroups)) {
        // menu_page_url(string $menu_slug, bool $dispay=true):string は、メニューページの url を返す。
        //  引数 $menu_slug には、これを表示するメニューページのスラグを渡す。このスラグのページのURLが返り値。
        //  引数 $display が true (規定値)なら、返り値がその場で echo される。
      ?>
        <form method="post" action="options.php">
          <?php
          // foreach ($this->optionGroups as $group) {
          //   $group->render();
          // }
          do_settings_sections($this->pageSlug);
          submit_button();
          ?>
        </form>
      <?php
      } else {
        echo '<p>設定項目がありません。</p>';
      }
      ?>
    </div>
<?php
  }
}
