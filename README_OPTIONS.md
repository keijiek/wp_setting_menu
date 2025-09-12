# Wordpress にオプション画面を作る

## ドキュメント

- [ハンドブック(日本語)->プラグイン開発ハンドブック->管理メニュー](https://ja.wordpress.org/team/handbook/plugin-development/administration-menus/)
- [ハンドブック(日本語)->プラグイン開発ハンドブック->設定](https://ja.wordpress.org/team/handbook/plugin-development/settings/)

## 用語

- 「option(オプション)」と「setting(設定, セッティング)」の違い
  - option は「保存される値そのもの」
  - setting は「管理画面で編集できるようにするための登録・管理の仕組み」
  - 実際には「settingで登録した値がoptionとして保存される」ため、呼び方が混同されがち。


## 大まかな手順

1. add_menu_page や add_submenu_page によって、管理画面にメニュー(ページ)を登録。これらは、ページ内容の全体を表示する役割も担う
2. 管理APIと設定APIで、register_setting() を行う。オプション名が指定される。
3. add_settings_section() と、add_settings_field() を行う。field 描画のコールバック関数で、input の name とオプション名を合わせる。

---

## 管理メニュー

メニューには、「トップレベルメニュー」と「サブメニュー」がある。

- トップレベルメニュー(`add_menu_page`で登録)
  - サブメニュー(`add_submenu_page`登録)
  - サブメニュー2...
- 既存メニュー(投稿,固定ページ,設定など。トップレベルメニュー)
  - サブメニュー(`add_submenu_page`登録)

### トップレベルメニューの登録(管理メニューAPI)

`admin_menu` アクションフックにて。

```php
add_menu_page(
  string $page_title,       // メニューページのタイトル
  string $menu_title,       // メニューのラベル
  string $capability,       // 権限名。例: manage_options で管理者、edit_pages で編集者、publish_posts で投稿者
  string $menu_slug,        // メニューのスラグ。設定API の add_settings_section() の第四引数で使用
  mixed $callback=''        // このページのhtmlを出力するコールバック関数。定型がある。後述。
  string $icon_url=''       // ダッシュアイコン名。https://developer.wordpress.org/resource/dashicons/
  int|float $position=null  // メニューポジション
): string|false;

// 返り値は、このページのフック接頭辞(string)、権限が足りなかった場合は false
```

### サブメニューの登録(管理メニューAPI)

```php
add_submenu_page(
  string $parent_slug,      // 親ページのスラグ
  string $page_title,       // ページのタイトル
  string $menu_title,       // メニューのラベル
  string $capability        // 権限名
  string $menu_slug         // スラグ
  mixed $callback=''        // 表示のコールバック関数。https://developer.wordpress.org/resource/dashicons/
  int|float $position=null, // メニューポジション
): string | false

// 返り値は、このページのフック接頭辞(string)、権限が足りなかった場合は false
```

### 既存メニューにサブメニューを追加したい場合(管理メニューAPI)

上記を使わずに、既存のメニュー下にサブメニューページを作成する関数として次がある。

- add_dashboard_page()
- add_posts_page()
- add_media_page()
- add_pages_page()
- add_comments_page()
- add_theme_page()
- add_plugins_page()
- add_users_page()
- add_management_page()
- add_options_page()
- add_options_page()
- add_links_page()


### メニューページの表示用コールバック関数

add_menu_page や add_submenu_page の表示用コールバック関数は、後述するコードのように書く。  

また、バリデーションとサニタイズの記事を事前に読んでおく。

- [バリデーション](https://developer.wordpress.org/apis/security/data-validation/)
- [サニタイズ](https://developer.wordpress.org/apis/security/sanitizing/)

```php
// 表示用コールバック関数
// 表示するhtmlのラップには div.wrap の使用が推奨されている。
function () {
  ?>
  <div class="wrap">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    <?php
    // エラー表示
    settings_errors();
    ?>
    // menu_page_url(string $menu_slug, bool $display=true):string は、メニューページのurlを返す。引数 $menu_slug は、そのメニューページのスラグ
    <form method="post" action="options.php">
      <?php
      // オプショングループ名に対応する nonce や hidden フィールドを出力
      settings_fields(オプショングループ名A);
      settings_fields(オプショングループ名B);
      ...
      // このページスラグに登録されたすべてのセクション(及びその中のフィールド)を自動で出力
      do_settings_sections(ページのスラグ);

      // サブミットボタンを表示
      submit_button();
      ?>
    </form>
  </div>
  <?php
}
```

上記で使われた次の関数は、設定APIに属する。

- get_admin_page_title()
- settings_errors()
- settings_fields()
- do_settings_sections()
- submit_button()

---

## 設定 API

[設定API](https://ja.wordpress.org/team/handbook/plugin-development/settings/settings-api/)

### 関数一覧

#### 設定の登録/登録解除

  - [register_setting()](https://developer.wordpress.org/reference/functions/register_setting/) : 「オプショングループ」に「オプション」を登録することにより、「セッティング」を登録。
  - [unregister_setting()](https://developer.wordpress.org/reference/functions/unregister_setting/) : 「セッティング」を登録解除。重要度低し。

#### フィールド / セクションの追加

  - [add_settings_section()](https://developer.wordpress.org/reference/functions/add_settings_section/) : ひとつのセッティングズページに、新しい一つのセクションを付加。
  - [add_settings_field()](https://developer.wordpress.org/reference/functions/add_settings_field/) : ひとつのセクションに、ひとつのフィールドを付加。

#### オプション・フォームのレンダリング

  - [settings_fields()](https://developer.wordpress.org/reference/functions/settings_fields/) : nonce, action 
  - [do_settings_sections()](https://developer.wordpress.org/reference/functions/do_settings_sections/)
  - [do_settings_fields()](https://developer.wordpress.org/reference/functions/do_settings_fields/)

#### エラー

  - [add_settings_error()](https://developer.wordpress.org/reference/functions/add_settings_error/)
  - [get_settings_errors()](https://developer.wordpress.org/reference/functions/get_settings_errors/)
  - [settings_errors()](https://developer.wordpress.org/reference/functions/settings_errors/)

---

## オプションAPI

[オプション API](https://ja.wordpress.org/team/handbook/plugin-development/settings/options-api/)

### 関数一覧

#### オプションの追加

  - [add_option()](https://developer.wordpress.org/reference/functions/add_option/) : 
  - [add_site_option()](https://developer.wordpress.org/reference/functions/add_site_option/) : 

#### オプションの取得

  - [get_option()](https://developer.wordpress.org/reference/functions/get_option/)
  - [get_site_option()](https://developer.wordpress.org/reference/functions/get_site_option/)
  
#### オプションの更新

  - [update_option()](https://developer.wordpress.org/reference/functions/update_option/)
  - [update_site_option()](https://developer.wordpress.org/reference/functions/update_site_option/)

#### オプションの削除

  - [delete_option()](https://developer.wordpress.org/reference/functions/delete_option/)
  - [delete_site_option()](https://developer.wordpress.org/reference/functions/delete_site_option/)

---

## オプショングループ, オプション

- `register_setting()` で、オプショングループにオプションを登録。
- `settings_fields()` で、そのグループの nonce 等を出力。
- フィールドとオプションとの関連付けは、input要素の name 属性の値で行う

※上記関数のタイミングは `admin_init`

### 登録

オプションは、オプショングループに登録する。

```php
// オプショングループ(第一引数)に、オプション(第二引数)を登録。
// 一つのオプショングループに複数オプションを登録しても良い。

register_setting(
  string $option_group,
  string $option_name,
  array $args
)
```

第一引数の `$option_group`には、次に挙げる既存のグループを用いることが可能。

'general' / 'discussion' / 'media' / 'reading' / 'writing' / 'options'.


#### register_setting() の $args の書き方

この時、第三引数の $args は次の要素を持たせる。

```php
$args = [
  'type' => string, 
  // type の値は次から選択： 'string' / 'boolean' / 'integer' / 'number'/  'array' / 'object'
  'label' => string,
  'description' => string,
  'sanitize_callback' => callable,// 
  'show_in_rest' => bool|array,
  'default' => mixed // get_option() された時の初期値を設定
]
```


#### show_in_rest の書き方

`show_in_rest` の書き方は、`type`の値によって大きく変わる。

```php
// 'type' => 'bool' / 'number' / 'string' の場合
register_setting('group', 'option_name', [
  'type'=>'boolean',
  'show_in_rest'=>true,
]);

// 'type' => 'array' の場合、show_in_rest->schema->items を次のように使う
register_setting('group', 'option_name', [
  'type' => 'array',
  'show_in_rest' => [
    'schema' => [
      'type' => 'array',
      'items' => [
        'type' => 'string', // 要素のキー名の無い同じ型の要素
      ],
    ],
  ],
]);

// 'type' => 'object' の場合、show_in_rest->schema->properties を次のように使う。
register_setting('group', 'option_name', [
  'type' => 'object',
  'show_in_rest' => [
    'schema' => [
      'type' => 'object',
      'properties' => [
        'field1' => [ 'type' => 'string' ],
        'field2' => [ 'type' => 'integer' ],
      ],
    ],
  ],
]);

```

---


## セクションとフィールド

```php
add_settings_section(
  string $id,
  string $title,
  callable $callback,
  string $page_slug,
  array $args = []
):void
```