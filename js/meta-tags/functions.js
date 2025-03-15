import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs, getQueryArg } from '@wordpress/url';

let apiCache = {};

export const request = async ( apiName, data = {}, method = 'POST', cache = true ) => {
	const cacheKey = JSON.stringify( { apiName, data, method } );
	if ( cache && apiCache[ cacheKey ] ) {
		return apiCache[ cacheKey ];
	}

	const lang = getQueryArg( location.href, 'lang' );

	let options;
	if ( method === 'GET' ) {
		options = {
			path: addQueryArgs( `/slim-seo/${ apiName }`, { ...data, lang } )
		};
	} else {
		options = {
			path: addQueryArgs( `/slim-seo/${ apiName }`, { lang } ),
			method,
			data
		};
	}

	const result = await apiFetch( options );
	apiCache[ cacheKey ] = result;
	return result;
};

export const normalize = html => !html ? '' : html
	.replace( /<(script|style)[^>]*?>.*?<\/\1>/gm, '' ) // Remove <style> & <script>
	.replace( /<[^>]*?>/gm, '' )                        // Remove other HTML tags.
	.replace( /\[.*?\]/gm, "" )                           // Remove shortcode tags.
	.replace( /\s+/gm, ' ' )                            // Remove duplicated white spaces.
	.trim();

export const isBlockEditor = document.body.classList.contains( 'block-editor-page' );
