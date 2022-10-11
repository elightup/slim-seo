import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/misc';

const Settings = () => {
	const [ forceTrailingSlash, setForceTrailingSlash ] = useState( SSRedirection.settings['force_trailing_slash'] );
	const [ redirectWWW, setRedirectWWW ] = useState( SSRedirection.settings[ 'redirect_www' ] );
	const [ enable404Logs, setEnable404Logs ] = useState( SSRedirection.settings[ 'enable_404_logs' ] );
	const [ redirect404To, setRedirect404To ] = useState( SSRedirection.settings[ 'redirect_404_to' ] );
	const [ redirect404ToURL, setRedirect404ToURL ] = useState( SSRedirection.settings[ 'redirect_404_to_url' ] );

	return (
		<>
			<table className='form-table'>
				<thead>
					<tr>
						<th scope="row">
							{ __( 'Force trailing slash', 'slim-seo' ) }
							<Tooltip content={ __( 'Enable redirect non-slash URL to URL has slash', 'slim-seo' ) } />
						</th>
						<td>
							<label className='ss-toggle'>
								<input className='ss-toggle__checkbox' id='ss-force-trailing-slash' type='checkbox' name={ `${ SSRedirection.settingsName }[force_trailing_slash]` } value={ forceTrailingSlash } checked={ 1 == forceTrailingSlash } onChange={ () => setForceTrailingSlash( prev => 1 == prev ? 0 : 1 ) } />
								<div className='ss-toggle__switch'></div>
							</label>
						</td>
					</tr>
				</thead>

				<tbody>
					<tr>
						<th scope="row">
							{ __( 'Redirect www', 'slim-seo' ) }
							<Tooltip content={ __( 'Auto redirect www to non-www and vice versa', 'slim-seo' ) } />
						</th>
						<td>
							<select id='ss-redirect-www' name={ `${ SSRedirection.settingsName }[redirect_www]` } value={ redirectWWW } onChange={ e => setRedirectWWW( prev => e.target.value ) }>
								<option value=''>{ __( 'Do nothing', 'slim-seo' ) }</option>
								<option value='www-to-non'>{ __( 'www to non-wwww', 'slim-seo' ) }</option>
								<option value='non-to-www'>{ __( 'non-www to www', 'slim-seo' ) }</option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							{ __( 'Enable 404 logs', 'slim-seo' ) }
							<Tooltip content={ __( 'Enable to track 404 logs', 'slim-seo' ) } />
						</th>
						<td>
							<label className='ss-toggle'>
								<input className='ss-toggle__checkbox' id='ss-enable-404-logs' type='checkbox' name={ `${ SSRedirection.settingsName }[enable_404_logs]` } value={ enable404Logs } checked={ 1 == enable404Logs } onChange={ () => setEnable404Logs( prev => 1 == prev ? 0 : 1 ) } />
								<div className='ss-toggle__switch'></div>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row">
							{ __( 'Redirect all 404 to', 'slim-seo' ) }
							<Tooltip content={ __( 'Auto redirect 404 pages if they do not have redirection rule.', 'slim-seo' ) } />
						</th>
						<td>
							<select id='ss-redirect-404-to' name={ `${ SSRedirection.settingsName }[redirect_404_to]` } value={ redirect404To } onChange={ e => setRedirect404To( prev => e.target.value ) }>
								<option value=''>{ __( 'Do nothing', 'slim-seo' ) }</option>
								<option value='homepage'>{ __( 'Homepage', 'slim-seo' ) }</option>
								<option value='custom'>{ __( 'Custom URL', 'slim-seo' ) }</option>
							</select>
							{
								'custom' === redirect404To
								&& <input type='text' className='regular-text' name={ `${ SSRedirection.settingsName }[redirect_404_to_url]` } value={ redirect404ToURL } onChange={ e => setRedirect404ToURL( prev => e.target.value.trim() ) } />
							}
						</td>
					</tr>
				</tbody>
			</table>

			<p className='submit'>
				<input type='submit' name='submit' id='submit' className='button button-primary' value={ __( 'Save Changes', 'slim-seo' ) } />
			</p>
		</>
	);
};

export default Settings;