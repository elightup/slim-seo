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


esbuild.build( {
	...config,
	entryPoints: [ 'js/seo-settings/src/settings.js' ],
	outfile: 'js/seo-settings/dist/settings.js'
} );

esbuild.build( {
	...config,
	entryPoints: [ 'js/seo-settings/src/post-term.js' ],
	outfile: 'js/seo-settings/dist/post-term.js'
} );