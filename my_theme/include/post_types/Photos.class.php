<?php

namespace include\post_types;

use \include\post_types\PostTypeBase;

class Photos extends PostTypeBase
{
  /**
   * 定数：この投稿タイプのスラグ
   */
  private const SLUG = 'photos';

  /**
   * 定数：この投稿タイプのラベル
   */
  private const LABEL = '写真';

  /**
   * 定数：この投稿の post_per_page の値
   */
  private const POSTS_PER_PAGE = 12;

  /**
   * コンストラクタ
   */
  public function __construct()
  {
    parent::__construct();
    $this->addCustomFields();
  }


  /**
   * この投稿タイプのスラグを返す
   * @return string
   */
  static public function getSlug(): string
  {
    return self::SLUG;
  }

  /**
   * この投稿タイプの posts_per_page の値を返す
   * @return integer
   */
  static public function getPostsPerPage(): int
  {
    return self::POSTS_PER_PAGE;
  }

  /**
   * 投稿タイプを登録する
   * @return void
   */
  public function registerPostType(): void
  {
    register_post_type(
      self::SLUG,
      [
        'labels' => $this->getLabelsValue(self::LABEL),
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'supports' => ['title', 'author', 'thumbnail'],
        'menu_position' => 5,
        'posts_per_page' => self::POSTS_PER_PAGE,
      ]
    );
  }

  /**
   * 投稿一覧のカスタムコラムの定義を返す
   * @return array
   */
  protected function getColumnDefinitions(): array
  {
    return [];
    // return [
    //   self::COL_NOTICE_TYPE => 'unko',
    // ];
  }

  public function displayColumnValues(string $columnSlug, int $postId): void
  {
    // Implement your logic here or leave empty if not needed
  }

  private function addCustomFields()
  {
    add_action('add_meta_boxes', [$this, 'addCustomMetaBox']);
    add_action('save_post_' . self::SLUG, [$this, 'saveCustomFields']);
    add_filter('rest_prepare_' . self::SLUG, [$this, 'addCustomFieldsToRestJson'], 10, 3);
  }

  public function addCustomMetaBox()
  {
    add_meta_box(
      'photo_gallery_field',           // ID
      '写真ギャラリー',            // タイトル
      [$this, 'renderInputField'],   // コールバック
      self::SLUG,                      // 投稿タイプ（「写真」のスラッグ）
      'normal',                      // 表示位置
      'high'                         // 優先度
    );
  }


  public function renderInputField($post)
  {
    $images = get_post_meta($post->ID, 'photo_gallery', true);
    if (!is_array($images)) $images = [];
?>
    <div id="photo-gallery-fields">
      <?php foreach ($images as $img_id): ?>
        <div class="photo-gallery-row">
          <input type="hidden" name="photo_gallery[]" value="<?php echo esc_attr($img_id); ?>" />
          <img src="<?php echo esc_url(wp_get_attachment_url($img_id)); ?>" style="max-width:100px;max-height:100px;vertical-align:middle;" />
          <button type="button" class="select-photo-gallery">画像を登録</button>
          <button type="button" class="remove-photo-gallery">削除</button>
        </div>
      <?php endforeach; ?>
      <div class="photo-gallery-row">
        <input type="hidden" name="photo_gallery[]" value="" />
        <img src="" style="max-width:100px;max-height:100px;vertical-align:middle;display:none;" />
        <button type="button" class="select-photo-gallery">画像を登録</button>
        <button type="button" class="remove-photo-gallery">削除</button>
      </div>
    </div>
    <button type="button" id="add-photo-gallery">追加</button>
    <script>
      document.getElementById('add-photo-gallery').addEventListener('click', function() {
        var container = document.getElementById('photo-gallery-fields');
        var row = document.createElement('div');
        row.className = 'photo-gallery-row';
        row.innerHTML =
          '<input type="hidden" name="photo_gallery[]" value="" />' +
          '<img src="" style="max-width:100px;max-height:100px;vertical-align:middle;display:none;" />' +
          '<button type="button" class="select-photo-gallery">画像を登録</button>' +
          '<button type="button" class="remove-photo-gallery">削除</button>';
        container.appendChild(row);
      });

      document.getElementById('photo-gallery-fields').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-photo-gallery')) {
          e.target.parentNode.remove();
        }
        if (e.target.classList.contains('select-photo-gallery')) {
          e.preventDefault();
          var row = e.target.parentNode;
          var input = row.querySelector('input[type="hidden"]');
          var img = row.querySelector('img');
          var frame = wp.media({
            title: '画像を選択',
            button: {
              text: '画像を挿入'
            },
            multiple: false
          });
          frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            input.value = attachment.id;
            img.src = attachment.url;
            img.style.display = 'inline-block';
          });
          frame.open();
        }
      });
    </script>
    <small>画像IDで保存され、プレビューが表示されます。</small>
<?php
  }

  // 保存処理
  public function saveCustomFields($post_id)
  {
    if (isset($_POST['photo_gallery']) && is_array($_POST['photo_gallery'])) {
      $ids = array_map('intval', $_POST['photo_gallery']);
      $ids = array_filter($ids); // 空欄除外
      update_post_meta($post_id, 'photo_gallery', $ids);
    }
  }

  /**
   * REST APIのレスポンス(JSON文字列)に、カスタムフィールドの値を取得して追加する。
   * @param [type] $response
   * @param [type] $post
   * @param [type] $request
   * @return void
   */
  public function addCustomFieldsToRestJson($response, $post, $request)
  {
    // 画像IDの配列を取得
    $photo_gallery = get_post_meta($post->ID, 'photo_gallery', true);
    if (!is_array($photo_gallery)) {
      $photo_gallery = [];
    }
    // レスポンスに追加
    $response->data['photo_gallery'] = $photo_gallery;
    return $response;
  }
}
