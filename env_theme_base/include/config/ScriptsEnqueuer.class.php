<?php


namespace include\config;

class ScriptsEnqueuer
{
  public function __construct()
  {
    add_action('wp_enqueue_scripts', [$this, 'callBackForFront']);
    add_action('admin_enqueue_scripts', [$this, 'callBackForAdmin']);
  }

  /**
   * wp_enqueue_scripts 用のコールバック関数
   * @return void
   */
  public function callBackForFront(): void
  {
    // $this->enqueueViteDistedFiles();
  }

  /**
   * admin_enqueue_scripts 用のコールバック関数
   * @return void
   */
  public function callBackForAdmin(): void
  {
    // Enqueue your admin scripts and styles here
  }


  /**
   * dist フォルダのファイルを、manifest.json を参照して読み込む
   * @return void
   */
  private function enqueueViteDistedFiles(): void
  {
    $distPath = get_theme_file_path('assets/dist');

    $manifestPath = $distPath . '/.vite/manifest.json';
    $manifestJson = file_get_contents($manifestPath);
    $manifest = json_decode($manifestJson, true);

    $this->enqueueFile('disted', 'assets/dist/' . $manifest['main.js']['file'], ['jquery']);
    $this->enqueueFile('disted', 'assets/dist/' . $manifest['main.js']['css'][0], []);
  }

  /**
   * js か css を enqueue する。
   * @param string $handle : ハンドル名
   * @param string $filePathFromRoot : プロジェクトルートからのファイルパス
   * @param array $depends : 依存対象のハンドル名配列
   * @return void
   */
  private function enqueueFile(string $handle, string $filePathFromRoot, array $depends = []): void
  {
    if (!file_exists(get_theme_file_path($filePathFromRoot))) {
      return;
    }

    // 拡張子を取得
    $extension = pathinfo($filePathFromRoot, PATHINFO_EXTENSION);

    // 拡張子で処理を分岐
    switch (strtolower($extension)) {
      case 'js':
        wp_enqueue_script(
          $handle,
          get_theme_file_uri($filePathFromRoot),
          $depends,
          filemtime(get_theme_file_path($filePathFromRoot))
        );
        break;
      case 'css':
        wp_enqueue_style(
          $handle,
          get_theme_file_uri($filePathFromRoot),
          $depends,
          filemtime(get_theme_file_path($filePathFromRoot))
        );
        break;
      default:
        echo "<!-- サポートされていないファイル形式: $filePathFromRoot -->";
    }
  }
}
