import { addQueryArgs } from '@wordpress/url';

let apiCache = {};

export const request = async ( apiName, params = {}, method = 'GET', cache = true ) => {
	const cacheKey = JSON.stringify( { apiName, params, method } );
	if ( cache && apiCache[ cacheKey ] ) {
		return apiCache[ cacheKey ];
	}

	const result = await wp.apiFetch( {
		path: addQueryArgs( `/slim-seo-post-types/v1/${ apiName }` , params ),
		method: method
	} );

	apiCache[ cacheKey ] = result;
	return result;
};
