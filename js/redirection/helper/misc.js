export const Tooltip = ( { content } ) => {
	return (
		<button type='button' className='ss-tooltip' data-tippy-content={ content } title={ content }><span className='dashicons dashicons-editor-help'></span></button>
	)
};