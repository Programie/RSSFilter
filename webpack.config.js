const path = require("path");
const {CleanWebpackPlugin} = require("clean-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const WebpackAssetsManifest = require("webpack-assets-manifest");

module.exports = {
    entry: "./src/main/resources/assets/script/main.js",
    output: {
        path: path.resolve(__dirname, "httpdocs/assets"),
        publicPath: "/assets/",
        filename: "[name].[hash].js"
    },
    devtool: "source-map",
    plugins: [
        new CleanWebpackPlugin(),
        new MiniCssExtractPlugin({
            filename: "[name].[contenthash].css"
        }),
        new WebpackAssetsManifest({
            output: path.resolve(__dirname, "webpack.assets.json"),
            publicPath: true
        })
    ],
    module: {
        rules: [
            {
                test: /\.(scss)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    "css-loader",
                    {
                        loader: "postcss-loader",
                        options: {
                            postcssOptions: {
                                plugins: function () {
                                    return [
                                        require("autoprefixer")
                                    ];
                                }
                            }
                        }
                    },
                    "sass-loader"
                ]
            }, {
                test: /.(png|jpg|ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
                type: "asset/resource"
            }
        ]
    }
};