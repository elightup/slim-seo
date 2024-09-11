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
};

start();