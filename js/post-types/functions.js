import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

let apiCache = {};

export const request = async ( apiName, data = {}, method = 'GET', cache = true ) => {
	const cacheKey = JSON.stringify( { apiName, data, method } );
	if ( cache && apiCache[ cacheKey ] ) {
		return apiCache[ cacheKey ];
	}
	let options = {
		method,
		headers: { 'X-WP-Nonce': ssPostTypes.nonce, 'Content-Type': 'application/json' },
	};
	let url = `${ ssPostTypes.rest }/slim-seo/${ apiName }`;
	const query = ( new URLSearchParams( data ) ).toString();

	if ( 'POST' === method ) {
		options.body = JSON.stringify( data );
	} else if ( query ) {
		url += ssPostTypes.rest.includes( '?' ) ? `&${ query }` : `?${ query }`;
	}
	const result = await fetch( url, options ).then( response => response.json() );
	apiCache[ cacheKey ] = result;
	return result;
};

export const normalize = html => !html ? '' : html
	.replace( /<(script|style)[^>]*?>.*?<\/\1>/gm, '' ) // Remove <style> & <script>
	.replace( /<[^>]*?>/gm, '' )                        // Remove other HTML tags.
	.replace( /\s+/gm, ' ' )                            // Remove duplicated white spaces.
	.trim();

export const isBlockEditor = document.body.classList.contains( 'block-editor-page' );
