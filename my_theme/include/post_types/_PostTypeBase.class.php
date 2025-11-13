<?php

namespace include\post_types;

/**
 * 投稿タイプクラスの基底クラス
 */
abstract class PostTypeBase
{

  /**
   * コンストラクタ
   * @param string $postTypeSlug : 投稿タイプのスラグを子クラスに必ず入力させる
   */
  public function __construct()
  {
    add_action('init', [$this, 'registerPostType']);
    add_action('pre_get_posts', [$this, 'preGetPostCallback']);
    $this->customizeAdminColumns();
  }


  /**
   * 投稿タイプのスラグを取得。子クラスでは、return self::SLUG を返すような実装を意図している。
   * @return string
   */
  abstract static public function getSlug(): string;

  abstract static public function getPostsPerPage(): int;

  /**
   * 内部で register_post_type() を実行する関数の実装を義務付ける。
   * 基底クラスで add_action('init', [$this, 'registerPostType']); のコールバック関数となる。
   * public であることが必須。
   * @return void
   */
  abstract public function registerPostType(): void;


  /**
   * register_post_type の 'labels' 要素の値を作って返す。
   * メンバ関数 registerPostType() を支援する関数。
   * @param string $label
   * @return array
   */
  protected function getLabelsValue(string $label): array
  {
    return [
      'name'                  => $label,
      'singular_name'         => $label,
      'add_new'               => $label . 'を追加',
      'add_new_item'          => '新規の' . $label . 'を追加',
      'edit_item'             => $label . 'を編集する',
      'new_item'              => '新規の' . $label . 'を追加',
      'view_item'             => $label . '1つを表示する',
      'view_items'            => $label . 'を表示する',
      'search_items'          => $label . 'を検索する',
      'not_found'             => $label . 'が見つかりませんでした',
      'not_found_in_trash'    => 'ゴミ箱に' . $label . 'はありません',
      'parent_item_colon'     => '親' . $label . ':',
      'all_items'             => 'すべての' . $label,
      'archives'              => $label . 'アーカイブ',
      'attributes'            => $label . 'の属性',
      'insert_into_item'      => $label . 'に挿入',
      'uploaded_to_this_item' => 'この' . $label . 'へのアップロード',
      'featured_image'        => 'アイキャッチ画像',
      'set_featured_image'    => 'アイキャッチ画像を設定',
      'remove_featured_image' => 'アイキャッチ画像を削除',
      'use_featured_image'    => 'アイキャッチ画像として使用',
      'menu_name'             => $label,
      'filter_items_list'     => $label . 'リストの絞り込み',
      'items_list_navigation' => $label . 'リストナビゲーション',
      'items_list'            => $label . 'リスト',
    ];
  }

  /**
   * - add_action('pre_get_posts')のコールバック関数。
   * - メインループにおける posts_per_page の値を制御する。
   * - もっと複雑な機能が必要なら子クラスでオーバーライド。
   * @param [type] $query
   * @return void
   */
  public function preGetPostCallback($query): void
  {
    $postType = $query->get('post_type');
    if (
      !is_admin() &&
      $query->is_main_query() &&
      ($postType === $this->getSlug() || (is_array($postType) && in_array($this->getSlug(), $postType, true)))
    ) {
      $query->set('posts_per_page', $this->getPostsPerPage());
    }
  }

  /**
   * 管理画面の投稿一覧表をカスタマイズする管理者
   * init()において必ず呼び出される。
   * この関数のために、次の3つの関数を実装・オーバーライドしておく必要がある。
   * - getColumnDefinitions() : カスタムカラムの定義を返す
   * - addCustomColumns() : カスタムカラムを追加する
   * - displayColumnValues() : カスタムカラムに値を表示する
   * @return void
   */
  protected function customizeAdminColumns(): void
  {
    // カスタムカラムの定義がない場合は何もしない
    if ($this->getColumnDefinitions() === []) {
      return;
    }

    // 1. カスタムカラムを追加
    add_filter(
      'manage_edit-' . $this->getSlug() . '_columns',
      [$this, 'addCustomColumns']
    );

    // 2. カスタムカラムに値を表示
    add_action(
      'manage_' . $this->getSlug() . '_posts_custom_column',
      [$this, 'displayColumnValues'],
      10,
      2
    );
  }


  /**
   * カスタムカラムの定義を返す抽象メソッド。子クラスで実装しなければならない。<br>
   * 定義が不要な場合でも、空の配列を返す実装が必要。
   * このメソッドは、カスタムカラムのスラグと表示名をキーと値として持つ連想配列を返す。
   *
   * @return array
   *
   * @example : (A) カスタムカラムが不要なら、次のように空要素を返すだけの実装でよい：
   *
   * ```
   * return [];
   * ```
   *
   * (B) カスタムカラムを定義したい場合、次のように、カスタムカラムのスラグと表示名をキーと値として持つ連想配列を返す実装をする：
   *
   * ```
   * return [
   *  'column1_slug' => 'カラム名1',
   *  'column2_slug' => 'カラム名2',
   * ];
   * ```
   */
  abstract protected function getColumnDefinitions(): array;


  /**
   * 管理画面の投稿一覧表において、特定カラムの後にカスタムカラムを追加するメソッド。
   * add_filter('manage_カスタム投稿のスラグ_post_custom_column', [$this, 'addCustomColumns'])関数のコールバック関数となるので、public でなければならない。
   * @param array $columns
   * @return array
   */
  public function addCustomColumns(array $columns): array
  {
    // カスタムカラムの定義を取得し、もし定義が空配列なら、この処理の引数 $columns をそのまま返す
    $customColumns = $this->getColumnDefinitions();
    if (empty($customColumns)) {
      return $columns;
    }

    $newColumns = [];
    $inserted = false;

    $insertAfterKeys = ['title', 'name', 'id'];

    foreach ($columns as $key => $value) {
      $newColumns[$key] = $value;

      // 指定されたキーの後に挿入
      if (!$inserted && in_array($key, $insertAfterKeys)) {
        $newColumns = array_merge($newColumns, $customColumns);
        $inserted = true;
      }
    }

    // まだ挿入されていない場合は、最初のカラムの後に挿入
    if (!$inserted) {
      $firstKey = array_key_first($columns);
      if ($firstKey) {
        $newColumns = [];
        foreach ($columns as $key => $value) {
          $newColumns[$key] = $value;
          if ($key === $firstKey) {
            $newColumns = array_merge($newColumns, $customColumns);
          }
        }
      } else {
        // カラムが全くない場合（稀なケース）
        $newColumns = $customColumns;
      }
    }

    return $newColumns;
  }

  public function addCustomColumns_v2(array $columns): array
  {
    // カスタムカラムの定義を取得し、もし定義が空配列なら、この処理の引数 $columns をそのまま返す
    $customColumns = $this->getColumnDefinitions();
    if (empty($customColumns)) {
      return $columns;
    }
    $newColumns = [];
    foreach ($columns as $key => $value) {
      $newColumns[$key] = $value;
      // title列の直後にカスタムカラムを挿入
      if ($key === 'title') {
        $newColumns = array_merge($newColumns, $customColumns);
      }
    }
    return $newColumns;
  }

  public function addCustomColumns_v3(array $columns): array
  {
    // カスタムカラムの定義を取得し、もし定義が空配列なら、この処理の引数 $columns をそのまま返す
    $customColumns = $this->getColumnDefinitions();
    if (empty($customColumns)) {
      return $columns;
    }

    $newColumns = [];

    if (array_key_exists('title', $columns)) {
      // title列の直後にカスタムカラムを挿入
      foreach ($columns as $key => $value) {
        $newColumns[$key] = $value;
        if ($key === 'title') {
          $newColumns = array_merge($newColumns, $customColumns);
        }
      }
    } else {
      // title列が無い場合は最初のカラムの直後に挿入
      $first = true;
      foreach ($columns as $key => $value) {
        $newColumns[$key] = $value;
        if ($first) {
          $newColumns = array_merge($newColumns, $customColumns);
          $first = false;
        }
      }
    }
    return $newColumns;
  }



  /**
   * カスタムカラムに値を表示するメソッド。継承先で具体的に実装しなければならない。必要ない場合でも、何も処理しない空の実装が必要。
   * @return void
   *
   * @example : (A) カスタムカラムが不要の場合、次のように空の実装をする：
   * ```
   * public function displayColumnValues(string $columnSlug, int $postId): void {}
   * ```
   *
   * (B) カスタムカラムが存在する場合、例えば次のような実装になる。
   * ```
   * // カラムスラグと値の表示の対応を配列で設定。
   * $handlers = [
   *    self::COL_PDF => function($postId) { echo $this->echoPdfColumnValue($postId); },
   *    self::COL_OVERWRITE_TITLE => function($postId) { echo $this->echoOverwriteTitleColumnValue($postId); },
   *    self::COL_TAX_TERMS => function($postId) { echo $this->echoTaxonomiesColumnValue($postId); },
   *    self::COL_ACF_TYPE => function($postId) { echo $this->echoTypeOfNewsletterColumnValue($postId); },
   * ];
   * // 表示処理を実行
   * if (isset($handlers[$columnSlug])) {
   *     $handlers[$columnSlug]($postId);
   * } else {
   *     echo "unknown column {$columnSlug}";
   * }
   * ```
   */
  abstract public function displayColumnValues(string $columnSlug, int $postId): void;
}
