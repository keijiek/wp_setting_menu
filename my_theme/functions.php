<?php

function vardump(mixed $var)
{
?>
  <pre><?php var_dump($var); ?></pre>
<?php
}

/**
 * オートローダー
 */
require_once __DIR__ . '/include/AutoLoader.class.php';
\include\AutoLoader::activateClassAutoLoader();


/**
 * include/config の実行
 */
// テーマサポート
new \include\config\ThemeSetupper();

// エンキュースクリプト
new \include\config\ScriptsEnqueuer();

// メニューロケーション登録
new \include\config\NavMenuRegister();

// デフォルト投稿タイプの無効化
// new \include\config\DefaultPostRemover();


/**
 * include\option_pages
 * オプションページ
 */
// new \include\option_pages\OptionPageRegister();

new option_pages\OptionPagesRegister();



/**
 * - include/post_types の実行
 * - include/taxonomies の実行
 */
// お知らせ投稿タイプ
new \include\post_types\Notices();

// お知らせ用タクソノミー
new \include\taxonomies\NoticeType();

// 写真投稿タイプ
new \include\post_types\Photos();
