var Encore = require("@symfony/webpack-encore");
var VueSSRServerPlugin = require("vue-server-renderer/server-plugin");

var merge = require("webpack-merge");
var nodeExternals = require("webpack-node-externals");

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
  // directory where compiled assets will be stored
  .setOutputPath("public/build/")
  // public path used by the web server to access the output path
  .setPublicPath("/build")
  // only needed for CDN's or sub-directory deploy
  //.setManifestKeyPrefix('build/')

  /*
   * ENTRY CONFIG
   *
   * Add 1 entry for each "page" of your app
   * (including one that's included on every page - e.g. "app")
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
   */
  .addEntry("app", "./assets/js/app.js")
  .addEntry("reports", "./assets/js/reports.js")
  .addEntry("main", "./assets/js/main.js")
  .addEntry("advertiserIndex", "./assets/js/pages/advertiser/index.js")
  .addEntry("dspIndex", "./assets/js/pages/dsp/index.js")
  .addEntry("agencyIndex", "./assets/js/pages/agency/index.js")
  .addEntry("userIndex", "./assets/js/pages/user/index.js")
  .addEntry("campaignIndex", "./assets/js/pages/campaign/index.js")
  .addEntry("targetIndex", "./assets/js/pages/target/index.js")
  .addEntry("demoIndex", "./assets/js/pages/demo/index.js")
  //.addEntry("cellEdit", "./assets/js/datatables/plugins/dataTables.cellEdit.js")

  // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
  //.splitEntryChunks()

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .disableSingleRuntimeChunk()

  /*
   * FEATURE CONFIG
   *
   * Enable & configure other features below. For a full
   * list of features, see:
   * https://symfony.com/doc/current/frontend.html#adding-more-features
   */
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())

  // enables @babel/preset-env polyfills
  .configureBabelPresetEnv(config => {
    config.useBuiltIns = "usage";
    config.corejs = 3;
  })

  // enables Sass/SCSS support
  .enableSassLoader()

  // uncomment if you use TypeScript
  //.enableTypeScriptLoader()

  // uncomment to get integrity="..." attributes on your script & link tags
  // requires WebpackEncoreBundle 1.4 or higher
  //.enableIntegrityHashes(Encore.isProduction())

  // uncomment if you're having problems with a jQuery plugin
  .autoProvidejQuery()

  // uncomment if you use API Platform Admin (composer req api-admin)
  //.enableReactPreset()
  //.addEntry('admin', './assets/js/admin.js')

  .enableVueLoader();

var normalConfig = Encore.getWebpackConfig();

normalConfig.name = "default";

var serverConfig = merge(normalConfig, {
  name: "server",
  // Point entry to your app's server entry file
  entry: "./assets/js/reports.server.js",

  module: {
    rules: [
      {
        test: /\.ya?ml$/,
        type: "json", // Required by Webpack v4
        use: "yaml-loader"
      }
    ]
  },

  // This allows webpack to handle dynamic imports in a Node-appropriate
  // fashion, and also tells `vue-loader` to emit server-oriented code when
  // compiling Vue components.
  target: "node",

  // For bundle renderer source map support
  devtool: "source-map",

  // This tells the server bundle to use Node-style exports
  output: {
    libraryTarget: "commonjs2"
  },

  // https://webpack.js.org/configuration/externals/#function
  // https://github.com/liady/webpack-node-externals
  // Externalize app dependencies. This makes the server build much faster
  // and generates a smaller bundle file.
  externals: nodeExternals({
    // do not externalize dependencies that need to be processed by webpack.
    // you can add more file types here e.g. raw *.vue files
    // you should also whitelist deps that modifies `global` (e.g. polyfills)
    whitelist: /\.css$/
  }),

  // This is the plugin that turns the entire output of the server build
  // into a single JSON file. The default file name will be
  // `vue-ssr-server-bundle.json`
  plugins: [new VueSSRServerPlugin()]
});

module.exports = [normalConfig, serverConfig];
