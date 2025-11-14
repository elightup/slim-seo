import { Dashicon, Tooltip as T } from '@wordpress/components';

export const Tooltip = ( { content, icon = 'editor-help' } ) => {
	return (
		<T text={ content }>
			<span className="ss-tooltip"><Dashicon icon={ icon } /></span>
		</T>
	);
};

export const TextTooltip = ( { content, children } ) => (
	<T text={ content }>
		{ children }
	</T>
);