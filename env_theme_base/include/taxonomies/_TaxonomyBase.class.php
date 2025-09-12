<?php


namespace include\taxonomies;

abstract class TaxonomyBase
{

  public function __construct()
  {
    add_action('init', [$this, 'register_taxonomy']);
  }


  abstract public function register_taxonomy(): void;


  /**
   * タクソノミーを追加すると自動で追加されるコラムの位置をタイトルの後に変更するためのコールバック関数。
   * フック名を子クラスで指定して使用。
   * @param array $columns
   * @return void
   */
  public function manageColumnCallback(array $columns)
  {
    $new = [];
    foreach ($columns as $key => $value) {
      $new[$key] = $value;
      if ($key === 'title') {
        $new['taxonomy-notice_type'] = 'お知らせタイプ';
      }
    }
    return $new;
  }
}
