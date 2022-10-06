const path = require( 'path' );
const webpack = require( 'webpack' );

const commonModules = {
	rules: [
		{
			test: /\.js/,
			exclude: /node_modules/,
			use: {
				loader: 'babel-loader',
				options: {
					plugins: [ '@babel/plugin-transform-react-jsx' ],
				},
			},
		},
		{
			test: /\.css/,
			use: [ 'style-loader', 'css-loader' ],
		},
	],
};

const externals = {
	react: 'React',
	'react-dom': 'ReactDOM',
	'@wordpress/i18n': 'wp.i18n',
	'@wordpress/element': 'wp.element',
	'@wordpress/components': 'wp.components',
	'@wordpress/hooks': 'wp.hooks'
};

const plugins = [
	new webpack.optimize.LimitChunkCountPlugin( {
		maxChunks: 1,
	} ),
];

const redirection = {
	entry: './js/redirection/App.js',
	output: {
		filename: 'redirection.js',
		path: path.resolve( __dirname, './js/' ),
	},
	externals,
	plugins,
	module: commonModules,
};

module.exports = [ redirection ];