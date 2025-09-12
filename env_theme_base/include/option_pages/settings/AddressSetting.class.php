<?php

namespace include\option_pages\settings;

use \include\option_pages\settings\SettingBase;

class AddressSetting extends SettingBase
{
  /**
   * オプション名。get_option の第一引数にもなる。割り当てられたフィールドを配列で抱える。
   */
  const OPTION_NAME = 'contact_address';

  const GROUP_NAME = self::OPTION_NAME . '_group';
  const SECTION_NAME = self::OPTION_NAME . '_section';

  const FLD_PHONE_NUMBER = 'phone_number';
  const FLD_MAIL_ADDRESS = 'mail_address';
  const FLD_POSTALCODE = 'postal_code';
  const FLD_ADDRESS = 'address';
  const FLD_MAP_EMBED = 'map_embed';

  public function __construct(string $pageSlug)
  {
    parent::__construct($pageSlug, self::OPTION_NAME, self::GROUP_NAME);
  }

  public function registerSettings(): void
  {
    // オプション設定を登録
    register_setting(
      self::GROUP_NAME, // オプショングループ名
      self::OPTION_NAME, // 設定名
      [
        'type' => 'array',
        'label' => '住所・地図・連絡先',
        'description' => '住所、地図、連絡先を入力するためのオプション設定',
        'sanitize_callback' => [$this, 'sanitizeOptions'],
        'show_in_rest' => [
          'schema' => [
            'type' => 'array',
            'items' => [
              'type' => 'string'
            ]
          ]
        ],
        'default' => [
          self::FLD_PHONE_NUMBER => '',
          self::FLD_MAIL_ADDRESS => '',
          self::FLD_POSTALCODE => '',
          self::FLD_ADDRESS => '',
          self::FLD_MAP_EMBED => ''
        ]
      ]
    );

    // セクションを追加
    add_settings_section(
      self::SECTION_NAME,
      '所在地情報',
      [$this, 'renderSection'],
      $this->pageSlug
    );

    // 電話番号フィールドを追加
    add_settings_field(
      self::FLD_PHONE_NUMBER . '_field',
      '電話番号',
      [$this, 'renderPhoneNumberField'],
      $this->pageSlug,
      self::SECTION_NAME
    );

    // メールアドレスフィールドを追加
    add_settings_field(
      self::FLD_MAIL_ADDRESS . '_field',
      'メールアドレス',
      [$this, 'renderMailAddressField'],
      $this->pageSlug,
      self::SECTION_NAME
    );

    // 郵便番号フィールドを追加
    add_settings_field(
      self::FLD_POSTALCODE . '_field',
      '郵便番号',
      [$this, 'renderPostalCodeField'],
      $this->pageSlug,
      self::SECTION_NAME
    );

    // 住所フィールドを追加
    add_settings_field(
      self::FLD_ADDRESS . '_field',
      '住所',
      [$this, 'renderAddressField'],
      $this->pageSlug,
      self::SECTION_NAME
    );

    // Google Maps埋め込みフィールドを追加
    add_settings_field(
      self::FLD_MAP_EMBED . '_field',
      'Google Map埋め込みコード',
      [$this, 'renderMapEmbedField'],
      $this->pageSlug,
      self::SECTION_NAME
    );
  }

  /**
   * セクション冒頭表示のコールバック関数
   * @return void
   */
  public function renderSection(): void
  {
?>
    <p>事業所の連絡先・住所・地図を設定します。</p>
  <?php
  }

  /**
   * 電話番号フィールド表示のコールバック関数
   * @return void
   */
  public function renderPhoneNumberField(): void
  {
    $options = get_option(self::OPTION_NAME);
    $phone_number = isset($options[self::FLD_PHONE_NUMBER]) ? $options[self::FLD_PHONE_NUMBER] : '';
  ?>
    <input
      type="text"
      id="<?= self::FLD_PHONE_NUMBER ?>_field"
      name="<?= self::OPTION_NAME; ?>[<?= self::FLD_PHONE_NUMBER ?>]"
      value="<?= esc_attr($phone_number); ?>"
      placeholder="090-1234-5678 or 09012345678"
      class="regular-text">
    <p class="description">電話番号を入力してください（例：03-1234-5678）</p>
  <?php
  }


  /**
   * メールアドレスフィールド表示のコールバック関数
   * @return void
   */
  public function renderMailAddressField(): void
  {
    $options = get_option(self::OPTION_NAME);
    $mail_address = isset($options[self::FLD_MAIL_ADDRESS]) ? $options[self::FLD_MAIL_ADDRESS] : '';
  ?>
    <input
      type="email"
      id="<?= self::FLD_MAIL_ADDRESS ?>_field"
      name="<?= self::OPTION_NAME; ?>[<?= self::FLD_MAIL_ADDRESS ?>]"
      value="<?= esc_attr($mail_address); ?>"
      placeholder="aaaa@hoge.com"
      class="regular-text">
    <p class="description">メールアドレスを入力してください（例：info@example.com）</p>
  <?php
  }

  /**
   * 郵便番号フィールド表示のコールバック関数
   * @return void
   */
  public function renderPostalCodeField(): void
  {
    $options = get_option(self::OPTION_NAME);
    $postal_code = isset($options[self::FLD_POSTALCODE]) ? $options[self::FLD_POSTALCODE] : '';
  ?>
    <input
      type="text"
      id="<?= self::FLD_POSTALCODE ?>_field"
      name="<?= self::OPTION_NAME; ?>[<?= self::FLD_POSTALCODE ?>]"
      value="<?= esc_attr($postal_code); ?>"
      placeholder="150-0001"
      class="regular-text">
    <p class="description">郵便番号を入力してください（例：150-0001）</p>
  <?php
  }

  /**
   * 住所フィールド表示のコールバック関数
   * @return void
   */
  public function renderAddressField(): void
  {
    $options = get_option(self::OPTION_NAME);
    $address = isset($options[self::FLD_ADDRESS]) ? $options[self::FLD_ADDRESS] : '';
  ?>
    <input
      type="text"
      id="<?= self::FLD_ADDRESS ?>_field"
      name="<?= self::OPTION_NAME; ?>[<?= self::FLD_ADDRESS ?>]"
      value="<?= esc_attr($address); ?>"
      placeholder="東京都渋谷区〇〇1-2-3"
      class="regular-text">
    <p class="description">住所を入力してください（例：東京都渋谷区〇〇1-2-3）</p>
  <?php
  }

  public function renderMapEmbedField(): void
  {
    $options = get_option(self::OPTION_NAME);
    $map_embed = isset($options[self::FLD_MAP_EMBED]) ? $options[self::FLD_MAP_EMBED] : '';
  ?>
    <textarea
      id="<?= self::FLD_MAP_EMBED ?>_field"
      name="<?= self::OPTION_NAME; ?>[<?= self::FLD_MAP_EMBED ?>]"
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

  public function sanitizeOptions($input): array
  {
    $output = [];

    // 電話番号のサニタイズ
    if (isset($input[self::FLD_PHONE_NUMBER])) {
      $output[self::FLD_PHONE_NUMBER] = sanitize_text_field($input[self::FLD_PHONE_NUMBER]);
    }

    // メールアドレスのサニタイズ
    if (isset($input[self::FLD_MAIL_ADDRESS])) {
      $output[self::FLD_MAIL_ADDRESS] = sanitize_email($input[self::FLD_MAIL_ADDRESS]);
    }

    // 郵便番号のサニタイズ
    if (isset($input[self::FLD_POSTALCODE])) {
      $output[self::FLD_POSTALCODE] = sanitize_text_field($input[self::FLD_POSTALCODE]);
    }

    // 住所のサニタイズ
    if (isset($input[self::FLD_ADDRESS])) {
      $output[self::FLD_ADDRESS] = sanitize_text_field($input[self::FLD_ADDRESS]);
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
}
