import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

let apiCache = {};

export const request = async ( apiName, data = {}, params = {} ) => {
	const {
		method = 'POST',
		cache = true,
		delay = 0
	} = params;

	const cacheKey = JSON.stringify( { apiName, data, method } );
	if ( cache && apiCache[ cacheKey ] ) {
		return apiCache[ cacheKey ];
	}

	if ( delay > 0 ) {
		await new Promise( resolve => setTimeout( resolve, delay ) );
	}

	let options;
	if ( method === 'GET' ) {
		options = {
			path: addQueryArgs( `/slim-seo/${ apiName }`, { ...data, lang: window.ssLang } )
		};
	} else {
		options = {
			path: addQueryArgs( `/slim-seo/${ apiName }`, { lang: window.ssLang } ),
			method,
			data
		};
	}

	const result = await apiFetch( options );
	if ( cache ) {
		apiCache[ cacheKey ] = result;
	}

	return result;
};

export const normalize = html => !html ? '' : html
	.replace( /<(script|style)[^>]*?>.*?<\/\1>/gm, '' ) // Remove <style> & <script>
	.replace( /<[^>]*?>/gm, '' )                        // Remove other HTML tags.
	.replace( /\[.*?\]/gm, "" )                           // Remove shortcode tags.
	.replace( /\s+/gm, ' ' )                            // Remove duplicated white spaces.
	.trim();

export const isBlockEditor = document.body.classList.contains( 'block-editor-page' );
