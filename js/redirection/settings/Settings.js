import { RawHTML, useReducer, useState } from '@wordpress/element';
import { sprintf, __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/misc';

const Settings = () => {
	const { settings, settingsName } = SSRedirection;

	const [ forceTrailingSlash, setForceTrailingSlash ] = useState( settings['force_trailing_slash'] );
	const [ redirectWWW, setRedirectWWW ] = useState( settings[ 'redirect_www' ] );
	const [ enable404Logs, setEnable404Logs ] = useState( settings[ 'enable_404_logs' ] );
	const [ autoDelete404Logs, setAutoDelete404Logs ] = useState( settings[ 'auto_delete_404_logs' ] );
	const [ deleteLog404Table, toggleDeleteLog404Table ] = useReducer( onOrOff => !onOrOff, false );
	const [ redirect404To, setRedirect404To ] = useState( settings[ 'redirect_404_to' ] );
	const [ redirect404ToURL, setRedirect404ToURL ] = useState( settings[ 'redirect_404_to_url' ] );

	return (
		<>
			<table className='form-table'>
				<thead>
					<tr>
						<th scope="row">
							<label htmlFor="ss-force-trailing-slash">{ __( 'Force trailing slash', 'slim-seo' ) }</label>
							<Tooltip content={ __( 'Enable redirect non-slash URL to URL has slash', 'slim-seo' ) } />
						</th>
						<td>
							<label className='ss-toggle'>
								<input className='ss-toggle__checkbox' id='ss-force-trailing-slash' type='checkbox' name={ `${ settingsName }[force_trailing_slash]` } value="1" checked={ !!forceTrailingSlash } onChange={ () => setForceTrailingSlash( prev => 1 - prev ) } />
								<div className='ss-toggle__switch'></div>
							</label>
							<br />
							<RawHTML>{ [
								'<small>',
								sprintf(
									__( 'Don\'t forget to add trailing slash in the <a href="%s">permalink settings</a>. If you use a permalink like "%%postname%%.html", then just enable this settings to force it work for category/tag/taxonomy/archive pages.', 'slim-seo' ),
									SSRedirection.permalinkUrl
								),
								'</small>',
							]}</RawHTML>
						</td>
					</tr>
				</thead>

				<tbody>
					<tr>
						<th scope="row">
							<label htmlFor="ss-redirect-www">{ __( 'Redirect www', 'slim-seo' ) }</label>
							<Tooltip content={ __( 'Auto redirect www to non-www and vice versa', 'slim-seo' ) } />
						</th>
						<td>
							<select id='ss-redirect-www' name={ `${ settingsName }[redirect_www]` } value={ redirectWWW } onChange={ e => setRedirectWWW( prev => e.target.value ) }>
								<option value=''>{ __( 'Do nothing', 'slim-seo' ) }</option>
								<option value='www-to-non'>{ __( 'www to non-wwww', 'slim-seo' ) }</option>
								<option value='non-to-www'>{ __( 'non-www to www', 'slim-seo' ) }</option>
							</select>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label htmlFor="ss-enable-404-logs">{ __( 'Enable 404 logs', 'slim-seo' ) }</label>
							<Tooltip content={ __( 'Enable to track 404 logs', 'slim-seo' ) } />
						</th>
						<td>
							<label className='ss-toggle'>
								<input className='ss-toggle__checkbox' id='ss-enable-404-logs' type='checkbox' name={ `${ settingsName }[enable_404_logs]` } value={ enable404Logs } checked={ 1 == enable404Logs } onChange={ () => setEnable404Logs( prev => 1 == prev ? 0 : 1 ) } />
								<div className='ss-toggle__switch'></div>
							</label>
						</td>
					</tr>

					{
						enable404Logs
							? (
								<tr>
									<th scope="row">
										<label htmlFor="ss-auto-delete-404-logs">{ __( 'Auto delete 404 logs', 'slim-seo' ) }</label>
										<Tooltip content={ __( '404 logs in the database will be automatically removed', 'slim-seo' ) } />
									</th>
									<td>
										<select id='ss-auto-delete-404-logs' name={ `${ settingsName }[auto_delete_404_logs]` } value={ autoDelete404Logs } onChange={ e => setAutoDelete404Logs( e.target.value ) }>
											<option value='-1'>{ __( 'Never', 'slim-seo' ) }</option>
											<option value='7'>{ __( 'Older than a week', 'slim-seo' ) }</option>
											<option value='30'>{ __( 'Older than a month', 'slim-seo' ) }</option>
										</select>
									</td>
								</tr>
							)
							: ( SSRedirection.isLog404TableExist && (
								<tr>
									<th scope="row">
										<label htmlFor="ss-delete-404-log-table">{ __( 'Delete 404 logs table', 'slim-seo' ) }</label>
										<Tooltip content={ __( 'Delete 404 logs table', 'slim-seo' ) } />
									</th>
									<td>
										<label className='ss-toggle'>
											<input className='ss-toggle__checkbox' id='ss-delete-404-log-table' type='checkbox' name={ `${ settingsName }[delete_404_log_table]` } value='1' checked={ !!deleteLog404Table } onChange={ toggleDeleteLog404Table } />
											<div className='ss-toggle__switch'></div>
										</label>
									</td>
								</tr>
							) )
					}

					<tr>
						<th scope="row">
							<label htmlFor="ss-redirect-404-to">{ __( 'Redirect all 404 to', 'slim-seo' ) }</label>
							<Tooltip content={ __( 'Auto redirect 404 pages if they do not have redirection rule.', 'slim-seo' ) } />
						</th>
						<td>
							<select id='ss-redirect-404-to' name={ `${ settingsName }[redirect_404_to]` } value={ redirect404To } onChange={ e => setRedirect404To( e.target.value ) }>
								<option value=''>{ __( 'Do nothing', 'slim-seo' ) }</option>
								<option value='homepage'>{ __( 'Homepage', 'slim-seo' ) }</option>
								<option value='custom'>{ __( 'Custom URL', 'slim-seo' ) }</option>
							</select>
							{
								'custom' === redirect404To
								&& <input type='text' className='regular-text' name={ `${ settingsName }[redirect_404_to_url]` } value={ redirect404ToURL } onChange={ e => setRedirect404ToURL( e.target.value.trim() ) } />
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