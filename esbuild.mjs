import esbuild from "esbuild";
import GlobalsPlugin from "esbuild-plugin-globals";

const config = {
	bundle: true,
	minify: true,
	loader: {
		'.js': 'jsx',
	},
	plugins: [
		GlobalsPlugin( {
			react: "React",
			'react-dom': 'ReactDOM',
			"@wordpress/.*": name => name === '@wordpress/api-fetch' ? 'wp.apiFetch' : `wp.${ name.substring( 11 ) }`,
		} ),
	],
};

const start = async () => {
	const redirection = await esbuild.context( {
		...config,
		entryPoints: [ 'js/redirection/App.js' ],
		outfile: 'js/redirection.js'
	} );
	await redirection.watch();

	const settings = await esbuild.context( {
		...config,
		entryPoints: [ 'js/meta-tags/src/settings.js' ],
		outfile: 'js/meta-tags/dist/settings.js'
	} );
	await settings.watch();

	const postTypes = await esbuild.context( {
		...config,
		entryPoints: [ 'js/post-types/App.js' ],
		outfile: 'js/post-types.js'
	} );
	await postTypes.watch();

	const post = await esbuild.context( {
		...config,
		entryPoints: [ 'js/post-types/Single.js' ],
		outfile: 'js/post-type.js'
	} );
	await post.watch();
};

start();