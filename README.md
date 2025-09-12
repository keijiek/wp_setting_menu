# wp-env を使った開発に対応したテーマ基底

## 前提

Windows に WSL を導入することを想定しています。

1. [**WSL**](https://learn.microsoft.com/ja-jp/windows/wsl/) を導入 (参照：[WSL を使用して Windows に Linux をインストールする方法](https://learn.microsoft.com/ja-jp/windows/wsl/install))
1. [**Desktop Docker**](https://www.docker.com/ja-jp/) を導入し、**開発前に必ず起動しておく**
1. [**Node.js**](https://nodejs.org/ja/) を導入(何らかの node バージョン管理ツールの使用を推奨。下記は [volta](https://volta.sh/) を用いることを想定した場合の操作)

```bash
# wsl の ubuntu の bash にて次のように操作

# 1. Volta のインストール(アップグレードも兼ねる)
curl https://get.volta.sh | bash

# 2. LTSの最新版 node をインストール
volta install node
```

### npm でよく使うコマンド

※ これ以降は Node.js に付随する **npm** コマンドをよく使うので、「npm 入門」や「npm 使い方」などで検索し、読みやすいものを選んで学習。

とりあえず、下記コマンドの内容、フルコマンドと省略形の対応、が分かればよい。

```bash
# package.json 作成。-y を省略すると質問と回答を経て作成。
npm init -y

# 既存の package.json に基づいて必要な全てのパッケージをインストール。i は install の省略形
npm install
npm i

# 公開時に必要なパッケージとしてインストール。--save は書かなくてもよい。
npm install --save パッケージ名
npm install パッケージ名
npm i パッケージ名

# 開発時に必要なパッケージとしてインストール。-D は --save-dev の省略形
npm install --save-dev パッケージ名
npm install -D パッケージ名
npm i -D パッケージ名

# グローバルなパッケージ(どのプロジェクトからも参照できるパッケージ)としてインストール。上記二種のローカルインストールが推奨されるため、ほとんど使わない。
npm install --global パッケージ名
npm install --g パッケージ名
npm i --global パッケージ名
npm i --g パッケージ名

# package.json の scripts オブジェクトに設定されたスクリプトを実行。
npm run スクリプト名
```

### 人の手で変更してはいけないフォルダとファイル

次の2つを決して編集してはならない。

- node_modules/
- package.lock.json

ただ、上記を間違って変更してしまっても、正常な package.json さえ残っていれば、上記の両方を復元可能。

次のように両方とも削除し、`npm i` を実行する。

```bash
# プロジェクトルートで、node_modules を削除
rm -rf ./node_modules

# package.lock.json を削除。package.json を削除しないように注意!
rm package.lock.json

# package.json に基づいて必要物を再インストール
npm i
```

---

## 1. プロジェクト作成

```bash
# 任意の場所に、任意の名前のプロジェクトディレクトリを作成し、中に入る。
mkdir project_name
cd $_
```

---

## 2. wp_env 導入

### 2.1. インストール

```bash
# wp-env を開発用パッケージとしてインストール
npm -D install @wordpress/env
```

- カレントディレクトリに `node_modules` というディレクトリが作成され、`node_modules/@wordpress/env` にパッケージがインストールされる。
- @wordpress/env が依存する多数のパッケージ群が、`node_modules`内にインストールされる。
- package.json の devDependencies オブジェクト下に、下記のように `@wordpress/env` のパッケージ名とバージョンが追加される。

```json
{
  "devDependencies": {
    "@wordpress/env": "^10.30.0"
  }
}
```

### 2.2. ".wp-env.json"

- `.wp-env.json` は、wp-env で起動する wordpress の設定を記述するファイル。そのプロジェクトに必要な環境を簡単に定義し分けることができる。
- プロジェクトルートに `.wp-env.json` を作る。(※ ファイル名の先頭に `.` が必要である点に注意)。

```bash
# カレントディレクトリに .wp-env.json という空のファイルを作成
touch .wp-env.json
```

下記は記述例：

```json
{
  "themes": ["."],
  "port": 8888,
  "testsPort": 8889,
  "config": {
    "WP_DEBUG": true,
    "WP_DEBUG_LOG": true,
    "WP_DEBUG_DISPLAY": true,
  }
}
```

#### 重要な設定項目

詳しくは [@wordpress/env#wp-env-json](https://developer.wordpress.org/block-editor/reference-guides/packages/packages-env/#wp-env-json) を参照。

##### `"themes" :["."]` :

カレントディレクトリのプロジェクトが、あたかも wp-content/themes/ 内にあるように wp-env が実行される。

具体的には、「外観」->「テーマ」にこのプロジェクトが登場する。

style.css や functions.php など、テーマとしての最低限のファイルと記述を持っている必要はある。

##### `"pluginhs": []` :

プロジェクトがプラグインの場合、この値を`["."]`に設定。カレントディレクトリのプロジェクトが、あたかも wp-content/plugins/ 内にあるかのように wp-env が実行される。

具体的には、インストール済みのプラグインとしてこのプロジェクトが登場する。

プラグインとして最低限のファイルと記述を持っている必要はある。

##### `"port": 8888` :

例えば値が 8888 なら、[http://localhost:8888] で、この wp-env が動かす wordpress にアクセスできる。

この設定は省略しても良いが、その場合の規定値は 8888。

手元に複数のプロジェクトがある場合、この設定でポートを別々にする。

##### `"testPort": 8889` :

この設定項目は省略しても良い。その場合、上記で設定した port 番号の次が自動的に testPort となる。

ひとまずそのことを覚えておけばよい。このポートで見られる wordpress は自動テスト用。

##### `"config"` :

wp-config.php に定義する定数を記述。

例えば、wp-config.php に `define("WP_DEBUG", true);` と設定された wordpress 環境を得たいなら、`"WP_DEBUG": true` と記述。

### 2.3. wp-env の実行

ローカルにインストールした wp-env は、次のように実行・停止する。

```bash
# wordpress 環境を起動
npx wp-env start

# wordpress 環境の停止
npx wp-env stop

# wordpress 環境を削除
npx wp-env destroy
```

### 2.4. 参考資料

- [wp-env 入門](https://ja.wordpress.org/team/handbook/block-editor/getting-started/devenv/get-started-with-wp-env/)

---

## 3. wp-scripts 導入

wp-scripts 導入のメリットは、第一に、上記の wp-env と併用することで、wp-env の wordpress に開発内容をリアルタイムに反映すること。第二に、ビルドツール。ビルドツールとしてのみの使用も可能。

参考資料：

- [wp-scripts 入門](https://ja.wordpress.org/team/handbook/block-editor/getting-started/devenv/get-started-with-wp-scripts/)

### インストール

```bash
# プロジェクトの開発用パッケージとしてインストール
npm i -D @wordpress/scripts
```

すると、package.json の "devDependencies" に、@wordpress/scripts のパッケージ名とバージョンが追加される。

```json
{
  "devDependencies": {
    "@wordpress/env": "^10.30.0",
    "@wordpress/scripts": "^30.23.0"
  }
}
```

### package.json の scripts に追加

```json
{
  "scripts": {
    "start": "wp-scripts start",
    "build": "wp-scripts build"
  }
}


// なお、package.json に最初から記述された scripts.test がある場合、そのtestの行は削除してよい。
```

こうすることで、次のコマンドを打てるようになる

```bash
# 開発中は常にこちらを実行しておく。wp-env で作った wordpress に、このプロジェクトの開発内容が反映されるようになる。プロジェクトのファイルを書き換えて保存しても変化を確認できない場合、このコマンドの実行を忘れている可能性が大。
npm run start

# src ディレクトリの内容がビルドされ、同一プロジェクトの build ディレクトリ内に出力物が現れる。
# ビルドのエントリーポイントは、他に設定がなければ、src/index.js。
npm run build
```

---

## 4. 使い方

### 4.1. 開発環境の起動

```bash
# 開発を始める前、または再開する前、プロジェクトルートで次を実行
npx wp-env start

# 同じくプロジェクトルートにて、二枚目のシェルを開き、次を実行
npm run start
```

### 4.2. ブラウザで wordpress サイトへアクセス
**.wp-env.json** の **port** の値が仮に`8888`だとすると、次のURL で wordpress にアクセスできる。

- [http://localhost:8888](http://localhost:8888)
- [http://localhost:8888/wp-admin/](http://localhost:8888/wp-admin/)

ログインできるユーザー情報は、デフォルトでは次の通り：
- ユーザー名 : admin
- パスワード : password

## 4.3. wordpress のウェブサーバーに bash でログイン

次のコマンドで、WordPress本体がインストールされているコンテナ内の **/var/www/html/** (wp-contentやwp-includeが存在するディレクトリ)に、シェルでログインする。

エラーログ(wp-content/debug.log)を見たり、wp cli を使った操作を行ったりするために。

```bash
# /var/www/html/ 直下にログイン
npx wp-env run cli bash

# コンテナの外側から、コンテナ内のコマンドを打つ
npx wp-env run cli コマンド

# debug.log への追加を継続的に監視
tail -F /var/www/html/wp-content/debug.log
less +F /var/www/html/wp-content/debug.log

# アクティブなテーマを除きすべてのテーマを削除
wp theme activate テーマのスラグ(プロジェクトのフォルダ名)
wp theme delete --all

# コンテナの外側からテーマを切り替えてその他を消す
npx wp-env run cli wp theme activate テーマのスラグ(プロジェクトのフォルダ名)
npx wp-env run cli wp theme delete --all

# 例：テーマのフォルダ名が env_them_base の場合、それをアクティブにして他を削除するコマンド
npx wp-env run cli wp theme activate env_them_base && npx wp-env run cli wp theme delete --all
```

package.json の `scripts`オブジェクトに次のように書き、npm run で実行することも可能。

```json
{
  "scripts": {
    "bash": "wp-env run cli bash",
    "tailf": "wp-env run cli tail -f wp-content/debug.log"
  },
}
```

```bash
# wordpressのコンテナにログイン
npm run bash

# wordpress のコンテナの debug.log に tailf を実行。継続的にエラーメッセージを確認できる。
npm run tailf
```
---

## 5. その他のファイル、ディレクトリ

### 5.1. .gitignore

まだ .gitignore が無い場合、gitignore.io を利用して .gitignore の作成。

[gitignore.io](https://www.gitignore.io)

```bash
# node と vscode を使った wordpress 用テーマを作成するプロジェクトのための gitignore を自動生成するコマンド
curl -sSL "https://gitignore.io/api/visualstudiocode,node,wordpress" > .gitignore

# git 管理開始
git init
```

### 5.2. 基本のフォルダ構成を作成

```bash
tree --gitinogre

.
├── 404.php
├── README.md
├── footer.php
├── functions.php
├── header.php
├── package-lock.json
├── package.json
├── src
│   └── index.js
└── style.css
```



### 5.3. Bootstrap

もし導入するなら

- [https://getbootstrap.jp/](https://getbootstrap.jp/)

```bash
npm i -D sass
npm i bootstrap @popperjs/core
```

src ディレクトリを参考に、ファイルを用意。

エントリーポイントが src/index.js で、そこで scss ファイルがすべて import されるようにする必要がある。

例えば、bootstrap カスタマイズ用の scss は、styles.scss において、次のように import されているとする。

```scss
@import "./custom_variables";
@import "bootstrap/scss/bootstrap";
@import "./custom_utilities";
```


その場合、その1ファイルのみを、index.js から import していればよい。ｌ

```js
import "./bootstrap/styles.scss";

window.addEventListener("DOMContentLoaded", (e) => {
  // Initialize any JavaScript functionality here
});
```
