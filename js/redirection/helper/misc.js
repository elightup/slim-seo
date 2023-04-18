import { Dashicon, Tooltip as T } from '@wordpress/components';
import useSWR from 'swr';

export const Tooltip = ( { content, icon = 'editor-help' } ) => {
	return (
		<T text={ content }>
			<span className="ss-tooltip"><Dashicon icon={ icon } /></span>
		</T>
	);
};

export const isValidUrl = url => {
	const nonUrlRegex = /^(mailto|tel|sms):/;
	const urlRegex = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;

	return nonUrlRegex.test( url ) || urlRegex.test( url );
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