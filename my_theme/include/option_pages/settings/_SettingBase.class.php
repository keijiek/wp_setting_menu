<?php

namespace include\option_pages\settings;

abstract class SettingBase
{
  protected string $pageSlug;
  protected string $settingName;
  protected string $groupName;

  /**
   *
   * @param string $pageSlug : ページのスラグ
   * @param string $settingName : Option名。
   * @param string $groupName : グループ名。
   */
  public function __construct(string $pageSlug, string $settingName, string $groupName)
  {
    $this->pageSlug = $pageSlug;
    $this->settingName = $settingName;
    $this->groupName = $groupName;
    add_action('admin_init', [$this, 'registerSettings']);
  }


  /**
   * register_setting の登録を行う。
   * このコンストラクタにて、add_action('admin_init') のコールバック関数として参照される。
   * register_setting(), add_settings_section(), 必要な数の add_settings_field() を内部で定義すること。
   * @return void
   */
  abstract public function registerSettings(): void;


  /**
   * このセクション用の nonce を form に埋め込む settings_fields() を実行。
   * page 側の表示関数(renderPage)が form 要素内で呼び出すメソッド。
   * @return mixed
   */
  public function getSettingsFields(): mixed
  {
    return settings_fields($this->groupName);
  }


  /**
   * フィルター型コールバック関数。
   * register_setting()の第三引数(array)の、sanitize_callback 要素の値に指定する。
   * input 値から、フィールドごとに値を取り出してサニタイズ処理を行い、return する配列として組み直す。
   * @param [type] $input
   * @return array
   */
  abstract public function sanitizeOptions($input): array;


  /**
   * セクションの冒頭を表示。
   * add_settings_section() の第三引数に指定するコールバック関数。
   * 少し説明を表示するだけでいい。
   * @return void
   */
  public function renderSection(): void
  {
?>
    <p>入力してください。</p>
<?php
  }
}
