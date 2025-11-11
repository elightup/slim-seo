import { createRoot, useReducer } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/Tooltip';

const App = () => {
	const { settings, settingsName } = SSRobots;
	const [ enableEditRobots, toggleEnableEditRobots ] = useReducer( onOrOff => !onOrOff, !!settings[ 'enable_edit_robots' ] );

	return SSRobots.fileExists
		? <p>{ __( 'There is a physical robots.txt file already exists in your web root. Please edit it directly.', 'slim-seo' ) }</p>
		: (
			<>
				<div className="ef-control">
					<div className="ef-control__label">
						<label htmlFor="ss-enable-edit-robots">{ __( 'Enable edit robots.txt', 'slim-seo' ) }</label>
						<Tooltip content={ __( 'Enable to edit robots.txt file', 'slim-seo' ) } />
					</div>
					<div className="ef-control__input">
						<label className='ss-toggle'>
							<input id='ss-enable-edit-robots' type='checkbox' name={ `${ settingsName }[enable_edit_robots]` } value='1' checked={ enableEditRobots } onChange={ toggleEnableEditRobots } />
							<div className='ss-toggle__switch'></div>
						</label>
					</div>
				</div>
				{
					enableEditRobots && (
						<div className="ef-control">
							<div className="ef-control__label" />
							<div className="ef-control__input">
								<textarea className="large-text" rows="10" name={ `${ settingsName }[custom_robots]` } defaultValue={ settings[ 'custom_robots' ] } />
								<p className="description">{ __( 'Enter content of robots.txt. Leave the field empty to let WordPress handle the contents dynamically.', 'slim-seo' ) }</p>
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