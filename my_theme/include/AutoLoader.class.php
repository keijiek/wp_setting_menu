<?php


namespace include;

class AutoLoader
{
  private function __construct()
  {
    // private constructor
  }

  /**
   * クラスファイルのオートローダーを活性化。
   * @link https://php.net/manual/en/function.spl-autoload-register.php
   *
   * 未定義のクラスが必要になった時に、
   * クラスが定義されたファイルを、その名前空間+クラス名からファイルパスを割り出して require_once する。
   * ファイル名とクラス名が一致する、かつ、名前空間とディレクトリ構造が一致する(\ と / を置換した文字列が一致する)場合、ファイルの場所に正しくたどり着ける。
   * 下記では、ファイル名の先頭に _ が付いているファイルだけは、ファイル名とクラス名が異なっても拾うようにしている。
   * 各自、自らのファイル命名規則に合わせてファイルパス生成のロジックを変更すること。
   *
   * 関数だけが定義されたファイルなどは、このオートローダーでは読み込まれない。
   * 読み込み場所と階層を限定した(例えば特定フォルダ直下ファイルに限定した) glob() でファイルパスを拾って require_once する処理を書くか、手動で require_once する。
   */
  static public function activateClassAutoLoader()
  {
    spl_autoload_register(function (string $className) {
      $className = ltrim($className, '\\');
      // $className の先頭が "include\" のものに限る
      // if (strpos($className, 'include\\') === 0) {

      // '\' で $className を分割し、末尾のファイル名($fileName)と、残りの名前空間($namespacePath)とを分離
      $parts = explode('\\', ltrim($className, '\\'));
      $fileName = end($parts);
      $namespacePath = implode('\\', array_slice($parts, 0, -1));

      // theme のルートディレクトリのパスを取得
      $themeRootPath = get_theme_file_path();

      // ファイルパスのパターン。このいずれかに該当するファイルが存在すれば require_once できる。
      $file_paths = [
        $themeRootPath . '/' . str_replace('\\', '/', $namespacePath) .  '/' . $fileName . '.class.php',
        $themeRootPath . '/' . str_replace('\\', '/', $namespacePath) . '/_' . $fileName . '.class.php',
      ];

      // foreach ($file_paths as $path) {
      //   error_log("Looking for: " . $path . " exists: " . (file_exists($path) ? 'YES' : 'NO'));
      // }

      foreach ($file_paths as $file_path) {
        if (file_exists($file_path)) {
          require_once $file_path;
          return true;
        }
      }
      // }
      return false;
    });
  }
}
