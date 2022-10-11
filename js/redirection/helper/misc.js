import { Dashicon, Tooltip as T } from '@wordpress/components';

export const Tooltip = ( { content, icon = 'editor-help' } ) => (
	<T text={ content }>
		<span className='ss-tooltip'><Dashicon icon={ icon } /></span>
	</T>
);

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