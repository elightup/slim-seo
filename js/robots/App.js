import { createRoot, useEffect, useReducer, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const App = () => {
	const { settings } = SSRobots;
	const [ editable, toggleEditable ] = useReducer( onOrOff => !onOrOff, !!settings.robots_txt_editable );
	const [ defaultValue, setDefaultValue ] = useState( '' );

	useEffect( () => {
		if ( !editable || defaultValue || settings.robots_txt_content ) {
			return;
		}

		let isMounted = true;

		fetch( `${ajaxurl}?action=slim_seo_robots_txt_default&nonce=${SSRobots.nonce}` )
			.then( r => r.json() )
			.then( r => {
				if ( r.success && isMounted ) {
					setDefaultValue( r.data );
				}
			} )
			.catch( () => {} );

		return () => {
			isMounted = false;
		};
	}, [ editable ] );

	return SSRobots.fileExists
		? <p>{ __( 'There is a physical robots.txt file already exists in your web root. Please edit it directly.', 'slim-seo' ) }</p>
		: (
			<>
				<div className="ef-control">
					<div className="ef-control__label">
						<label htmlFor="ss-robots-txt-editable">{ __( 'Enable editing?', 'slim-seo' ) }</label>
					</div>
					<div className="ef-control__input">
						<label className='ss-toggle'>
							<input id='ss-robots-txt-editable' type='checkbox' name="slim_seo[robots_txt_editable]" value='1' checked={ editable } onChange={ toggleEditable } />
							<div className='ss-toggle__switch'></div>
						</label>
					</div>
				</div>
				{
					editable && (
						<div className="ef-control">
							<div className="ef-control__label" />
							<div className="ef-control__input">
								<textarea className="code" rows="10" name="slim_seo[robots_txt_content]" defaultValue={ settings.robots_txt_content || defaultValue } />
								<p className="description">{ __( 'Enter custom content for the robots.txt file. The default value is what WordPress and Slim SEO already do for you.', 'slim-seo' ) }</p>
							</div>
						</div>
					)
				}
			</>
		);
};

const container = document.getElementById( 'ss-robots' );
const root = createRoot( container );
root.render( <App /> );