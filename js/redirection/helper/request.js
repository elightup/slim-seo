const request = async ( apiName, parameters, method = 'GET' ) => {
	let options = {
		method,
		headers: { 'X-WP-Nonce': SSRedirection.nonce, 'Content-Type': 'application/json' },
	};

	let url = `${ SSRedirection.rest }/slim-seo-redirection/${ apiName }`;

	if ( 'POST' === method ) {
		options.body = JSON.stringify( parameters );
	} else {
		const query = ( new URLSearchParams( parameters ) ).toString();

		if ( query ) {
			url += SSRedirection.rest.includes( '?' ) ? `&${ query }` : `?${ query }`;
		}
	}

	const result = await fetch( url, options ).then( response => response.json() );
	
	return result;
};

export default request;