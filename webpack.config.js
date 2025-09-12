const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require("path");

// 二つのエントリーポイントを持たせる設定。bootstrap のビルドは時間がかかるので分離しようとした。
module.exports = {
  ...defaultConfig,
  entry: {
    front: path.resolve(__dirname, "env_theme_base/src/front_main.js"),
    // admin: path.resolve(__dirname, "env_theme_base/src/admin_main.js"),
  },
  output: {
    path: path.resolve(__dirname, "env_theme_base/build"),
    filename: "[name].js",
    clean: true,
  },
};
