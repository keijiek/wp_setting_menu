<?php

namespace include\option_pages;

class OptionPageRegister
{
  // オプション画面のスラグ
  const SLUG = 'site_settings';

  // 権限
  const CAPABILITY = 'manage_options';

  // オプション名
  const OPTION_NAME = 'site_location_settings';

  public function __construct()
  {
    add_action('admin_menu', [$this, 'addOptionPage']);
    // add_action('admin_init', [$this, 'registerSettings']);
    // new \include\option_pages\SubPage01(self::SLUG, 1);
    new \include\option_pages\SubOptionPage01(self::SLUG, 2);
  }

  public function addOptionPage(): void
  {
    // トップレベルメニュー項目の追加
    add_menu_page(
      '共通設定画面',
      '共通設定',
      self::CAPABILITY,
      self::SLUG,
      [$this, 'optionPageRenderer'],
      'dashicons-admin-generic',
      21
    );
  }

  public function registerSettings(): void
  {
    // オプション設定を登録
    register_setting(
      'site_location_group', // オプショングループ名
      self::OPTION_NAME, // オプション名
      [
        'show_in_rest' => [
          'type' => 'object',
          'schema' => [
            'type'  => 'array',
            'items' => [
              'type' => 'string', // 画像IDなら integer
            ],
          ],
        ],
        'sanitize_callback' => [$this, 'sanitizeOptions'],
        'default' => [
          'address' => '',
          'map_embed' => ''
        ]
      ]
    );

    // セクションを追加
    add_settings_section(
      'location_section',
      '所在地情報',
      [$this, 'locationSectionCallback'],
      self::SLUG
    );



    // 住所フィールドを追加
    add_settings_field(
      'address_field',
      '住所',
      [$this, 'addressFieldCallback'],
      self::SLUG,
      'location_section'
    );

    // Google Maps埋め込みフィールドを追加
    add_settings_field(
      'map_embed_field',
      'Google Map埋め込みコード',
      [$this, 'mapEmbedFieldCallback'],
      self::SLUG,
      'location_section'
    );
  }
  /**
   * セクションの説明
   */
  public function locationSectionCallback(): void
  {
    echo '<p>事業所の住所とGoogleマップ埋め込みコードを設定してください。</p>';
  }

  /**
   * 住所フィールドの表示
   */
  public function addressFieldCallback(): void
  {
    $options = get_option(self::OPTION_NAME);
    $address = isset($options['address']) ? $options['address'] : '';
?>
    <input
      type="text"
      id="address_field"
      name="<?php echo self::OPTION_NAME; ?>[address]"
      value="<?php echo esc_attr($address); ?>"
      class="regular-text">
    <p class="description">住所を入力してください（例：東京都渋谷区〇〇1-2-3）</p>
  <?php
  }
  /**
   * Google Maps埋め込みフィールドの表示
   */
  public function mapEmbedFieldCallback(): void
  {
    $options = get_option(self::OPTION_NAME);
    $map_embed = isset($options['map_embed']) ? $options['map_embed'] : '';
  ?>
    <textarea
      id="map_embed_field"
      name="<?php echo self::OPTION_NAME; ?>[map_embed]"
      class="large-text code"
      rows="8"><?php echo esc_textarea($map_embed); ?></textarea>
    <p class="description">Google Mapsの「共有」から「地図を埋め込む」で取得したiframeコードを貼り付けてください</p>
    <?php
    if (!empty($map_embed)) {
    ?>
      <div class="map-preview">
        <h4>プレビュー</h4>
        <?php echo wp_kses($map_embed, [
          'iframe' => [
            'src'             => [],
            'width'           => [],
            'height'          => [],
            'frameborder'     => [],
            'style'           => [],
            'allowfullscreen' => [],
            'aria-hidden'     => [],
            'tabindex'        => [],
            'loading'         => [],
          ]
        ]); ?>
      </div>
    <?php
    }
    ?>
  <?php
  }


  /**
   * オプションデータのサニタイズ
   */
  public function sanitizeOptions($input)
  {
    $output = [];

    // 住所のサニタイズ
    if (isset($input['address'])) {
      $output['address'] = sanitize_text_field($input['address']);
    }

    // マップ埋め込みコードのサニタイズ
    if (isset($input['map_embed'])) {
      // iframeタグのみ許可し、その他の属性は厳しく制限
      $allowed_html = [
        'iframe' => [
          'src'             => [],
          'width'           => [],
          'height'          => [],
          'frameborder'     => [],
          'style'           => [],
          'allowfullscreen' => [],
          'aria-hidden'     => [],
          'tabindex'        => [],
          'loading'         => [],
        ]
      ];

      $output['map_embed'] = wp_kses($input['map_embed'], $allowed_html);

      // iframe以外のスクリプトが含まれていないかチェック
      if (strpos($input['map_embed'], '<script') !== false) {
        add_settings_error(
          self::OPTION_NAME,
          'invalid_map_embed',
          'Googleマップ埋め込みコードには、iframeコードのみ使用できます。',
          'error'
        );
      }

      // Google Mapsドメインかチェック
      if (
        strpos($output['map_embed'], 'maps.google.com') === false &&
        strpos($output['map_embed'], 'google.com/maps') === false
      ) {
        add_settings_error(
          self::OPTION_NAME,
          'invalid_map_domain',
          'Googleマップの埋め込みコードのみ使用できます。',
          'error'
        );
      }
    }

    return $output;
  }

  public function optionPageRenderer()
  {
  ?>
    <div class="wrap">
      <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
      <?php settings_errors(); // エラーや成功メッセージの表示
      ?>

      <form method="post" action="options.php">
        <?php
        // 隠しフィールドなどを出力（nonceなど）
        settings_fields('site_location_group');

        // セクションとフィールドを出力
        do_settings_sections(self::SLUG);

        // 送信ボタン
        submit_button('設定を保存');
        ?>
      </form>
    </div>
<?php
  }
}
