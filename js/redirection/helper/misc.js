import { Dashicon } from '@wordpress/components';
import ReactTooltip from 'react-tooltip';
import useSWR from 'swr';

export const Tooltip = ( { content, icon = 'editor-help', place = 'right' } ) => {
	const id = Math.random().toString( 16 ).slice( 2 );

	return (
		<span className='ss-tooltip'>
			<Dashicon icon={ icon } data-tip={ content } data-for={ id } />
			<ReactTooltip id={ id } place={ place } />
		</span>
	);	
};

export const isValidUrl = url => {
	return null !== url.match( /(http(s)?:\/\/.)?(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,100}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/g );
};

export const getFullURL = url => {
	if ( isValidUrl( url ) ) {
		return url;
	}

	url = '/' === url[0] ? url : `/${url}`

	return SSRedirection.homeURL + `${url}`;
};

export const fetcher = ( apiName, parameters = {}, method = 'GET' ) => {
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