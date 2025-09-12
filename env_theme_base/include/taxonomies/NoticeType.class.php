<?php

namespace include\taxonomies;


use \include\taxonomies\TaxonomyBase;

// require_once POST_TYPES_DIR . '/notices/Notices.class.php';

use \include\post_types\Notices;

class NoticeType extends TaxonomyBase
{
  public const SLUG = 'notice_type';

  public function __construct()
  {
    parent::__construct();

    // add_filter('manage_edit-notices_columns', [$this, 'manageColumnCallback']);
  }

  public function register_taxonomy(): void
  {
    register_taxonomy(
      self::SLUG,
      [Notices::getSlug()],
      [
        'hierarchical' => true,
        'labels' => [
          'name' => 'お知らせタイプ',
          'singular_name' => 'お知らせタイプ',
        ],
        'public' => true,
        'show_admin_column' => true,
        'show_in_rest' => true,
      ]
    );
  }
}
