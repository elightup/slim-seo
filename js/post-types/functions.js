let apiCache = {};
export const request = async ( apiName, params = {}, method = 'GET', cache = true ) => {
	const cacheKey = JSON.stringify( { apiName, params, method } );
	if ( cache && apiCache[ cacheKey ] ) {
		return apiCache[ cacheKey ];
	}
	let options = {
		method,
		headers: { 'X-WP-Nonce': ssPostTypes.nonce, 'Content-Type': 'application/json' },
	};
	let url = `${ ssPostTypes.rest }/slim-seo-post-types/${ apiName }`;
	const query = ( new URLSearchParams( params ) ).toString();
	if ( 'POST' === method ) {
		options.body = JSON.stringify( params );
	} else if ( query ) {
		url += ssPostTypes.rest.includes( '?' ) ? `&${ query }` : `?${ query }`;
	}
	const result = await fetch( url, options ).then( response => response.json() );
	apiCache[ cacheKey ] = result;
	return result;
};