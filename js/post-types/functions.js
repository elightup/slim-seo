import useSWR from 'swr';

export const fetcher = ( apiName, parameters = {}, method = 'GET' ) => {
	let options = {
		method,
		headers: { 'X-WP-Nonce': ssPostTypes.nonce, 'Content-Type': 'application/json' },
	};
	let url = `${ ssPostTypes.rest }/slim-seo-post-types/${ apiName }`;

	if ( 'POST' === method ) {
		options.body = JSON.stringify( parameters );
	} else {
		const query = ( new URLSearchParams( parameters ) ).toString();

		if ( query ) {
			url += ssPostTypes.rest.includes( '?' ) ? `&${ query }` : `?${ query }`;
		}
	}

	return fetch( url, options ).then( response => response.json() );
};

export const useApi = ( apiName, parameters = {}, args = {}, defaultValue ) => {
	args = { method: 'GET', returnMutate: false, options: {}, ...args, };

	const { data, error, mutate } = useSWR( [ apiName, parameters, args.method ], fetcher, { revalidateOnFocus: false, ...args.options } );
	const result = ( error || !data ? defaultValue : data );

	if ( args.returnMutate ) {
		return { result, mutate };
	}

	return result;
};