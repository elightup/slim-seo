import { useReducer, createRoot } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/Tooltip';

const App = () => {
	const { settings, settingsName } = SSRobots;
	const [ enableEditRobots, toggleEnableEditRobots ] = useReducer( onOrOff => !onOrOff, !!settings[ 'enable_edit_robots' ] );
	
	return SSRobots.fileExists
		? <p>{ __( 'You cannot edit the robots.txt file because it already exists.', 'slim-seo' ) }</p>
		: (
			<table className='form-table'>
				<tbody>
					<tr>
						<th scope="row">
							<label htmlFor="ss-enable-edit-robots">{ __( 'Enable edit robots.txt', 'slim-seo' ) }</label>
							<Tooltip content={ __( 'Enable to edit robots.txt file', 'slim-seo' ) } />
						</th>
						<td>
							<label className='ss-toggle'>
								<input id='ss-enable-edit-robots' type='checkbox' name={ `${ settingsName }[enable_edit_robots]` } value='1' checked={ enableEditRobots } onChange={ toggleEnableEditRobots } />
								<div className='ss-toggle__switch'></div>
							</label>
						</td>
					</tr>

					{
						enableEditRobots && (
							<tr>
								<th scope="row"></th>
								<td>
									<textarea className="large-text" rows="10" name={ `${ settingsName }[custom_robots]` } defaultValue={ settings['custom_robots'] } />
									<p className="description">{ __( 'Enter content of robots.txt. Leave the field empty to let WordPress handle the contents dynamically.', 'slim-seo' ) }</p>
								</td>
							</tr>
						)
					}
				</tbody>
			</table>
		);
};

const container = document.getElementById( 'ss-robots' );
const root = createRoot( container );
root.render( <App /> );