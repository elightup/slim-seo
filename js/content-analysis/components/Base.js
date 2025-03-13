import { useReducer } from '@wordpress/element';

const Base = ( { title, success = false, defaultOpen = false, hiddenFieldName = '', sectionClassName = '', children } ) => {
	const [ open, toggleOpen ] = useReducer( open => !open, defaultOpen );

	return (
		<div className={ 'components-panel__body' + ( open ? ' is-opened' : '' ) + ( sectionClassName ? ' ' + sectionClassName : '' ) }>
			<div className="components-panel__body-title" onClick={ toggleOpen }>
				<button type="button" aria-expanded={ open ? 'true' : 'false' } className="components-button components-panel__body-toggle">
					<span aria-hidden="true">
						<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" className="components-panel__arrow" aria-hidden="true" focusable="false">
							{
								open
								? <path d="M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z"></path>
								: <path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>
							}														
						</svg>
					</span>
					<span className={ success ? 'ss-success' : 'ss-warning' }></span>
					<span className="ss-content-analysis-component-title">{ title }</span>
				</button>
			</div>
			{ hiddenFieldName ? <input type="hidden" name={ `slim_seo[content_analysis][${hiddenFieldName}]` } value={ success ? 1 : 0 } /> : '' }
			{ open ? children : '' }
		</div>
	);
};

export default Base;