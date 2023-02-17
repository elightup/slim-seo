const esbuild = require( 'esbuild' );
const { externalGlobalPlugin } = require( 'esbuild-plugin-external-global' );

const config = {
	bundle: true,
	minify: true,
	loader: {
		'.js': 'jsx',
	},
	plugins: [
		externalGlobalPlugin( {
			'react': 'React',
			'react-dom': 'ReactDOM',
			'@wordpress/i18n': 'wp.i18n',
			'@wordpress/element': 'wp.element',
			'@wordpress/components': 'wp.components',
			'@wordpress/hooks': 'wp.hooks'
		} ),
	],
};

esbuild.build( {
	...config,
	entryPoints: [ 'js/redirection/App.js' ],
	outfile: 'js/redirection.js'
} );