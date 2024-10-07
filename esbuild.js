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
			'@wordpress/hooks': 'wp.hooks',
			'@wordpress/api-fetch': 'wp.apiFetch'
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
	entryPoints: [ 'js/content-analysis/App.js' ],
	outfile: 'js/content-analysis.js'
} );


esbuild.build( {
	...config,
	entryPoints: [ 'js/meta-tags/src/settings.js' ],
	outfile: 'js/meta-tags/dist/settings.js'
} );


esbuild.build( {
	...config,
	entryPoints: [ 'js/post-types/App.js' ],
	outfile: 'js/post-types.js'
} );

esbuild.build( {
	...config,
	entryPoints: [ 'js/meta-tags/src/object.js' ],
	outfile: 'js/meta-tags/dist/object.js'
} );