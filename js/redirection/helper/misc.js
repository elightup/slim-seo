import { Dashicon, Tooltip as T } from '@wordpress/components';

export const Tooltip = ( { content, icon = 'editor-help' } ) => (
	<T text={ content }>
		<span className='ss-tooltip'><Dashicon icon={ icon } /></span>
	</T>
);