import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

let apiCache = {};

export const request = async ( apiName, params = {}, method = 'GET', cache = true ) => {
	const cacheKey = JSON.stringify( { apiName, params, method } );
	if ( cache && apiCache[ cacheKey ] ) {
		return apiCache[ cacheKey ];
	}

	apiFetch.use( apiFetch.createNonceMiddleware( ssPostTypes.nonce ) );
	const result = await apiFetch( {
		url: addQueryArgs( `${ ssPostTypes.rest }/slim-seo-post-types/${ apiName }` , params ),
		method: method
	} );

	apiCache[ cacheKey ] = result;
	return result;
};
