// Import the original config from the @wordpress/scripts package.
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

// Utilities.
const path = require( 'path' );

// Add any a new entry point by extending the webpack config.
module.exports = {
	...defaultConfig,
	...{
		entry: {
			'single': './js/content/Single.js',
			'content': './js/content/Settings.js',
			'redirection': './js/redirection/App.js',
		},
		output: {
			path: path.resolve( __dirname, 'js/build' ),
		}
	}
};