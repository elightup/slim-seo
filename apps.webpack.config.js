// Import the original config from the @wordpress/scripts package.
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

// Utilities.
const path = require( 'path' );

// Add any a new entry point by extending the webpack config.
module.exports = {
	...defaultConfig,
	...{
		entry: {
			single: './js/meta-tags/Single.js', // Meta tags for singular pages.
			"meta-tags": './js/meta-tags/Settings.js', // Settings > Meta Tags tab.
			redirection: './js/redirection/App.js', // Redirection app.
			robots: './js/robots/App.js', // Robots app.
			"link-attributes": './js/link-attributes/block-editor/index.js', // Link attributes in Block Editor
		},
		output: {
			path: path.resolve( __dirname, 'js/build' ),
		},
	},
};
