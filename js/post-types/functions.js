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
			path: addQueryArgs( `/slim-seo/${ apiName }` , data )
		}
	} else {
		options = {
			path: `/slim-seo/${ apiName }`,
			method,
			data
		}
	}

	const result = await apiFetch( options );
	apiCache[ cacheKey ] = result;
	return result;
};