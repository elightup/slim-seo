import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

let apiCache = {};

export const request = async ( apiName, data = {}, method = 'GET', cache = true ) => {
	const cacheKey = JSON.stringify( { apiName, data, method } );
	if ( cache && apiCache[ cacheKey ] ) {
		return apiCache[ cacheKey ];
	}

	let options;
	if ( method === 'GET' ) {
		options = {
			path: addQueryArgs( `/slim-seo/${ apiName }`, data )
		};
	} else {
		options = {
			path: `/slim-seo/${ apiName }`,
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

export const formatTitle = text => {
	text = normalize( text );

	const values = {
		site: ss.site.title,
		tagline: ss.site.description,
		title: text
	};

	return ss.title.parts.map( part => values[ part ] ?? '' ).filter( part => part ).join( ` ${ ss.title.separator } ` );
};

export const formatDescription = ( text, max = 160 ) => normalize( text ).substring( 0, max );

export const isBlockEditor = document.body.classList.contains( 'block-editor-page' );
