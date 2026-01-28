import { createRoot } from '@wordpress/element';
import { useReducer, useState } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/Tooltip';
import ToInput from './redirects/ToInput';

const Post = () => {
	const [ redirect, setRedirect ] = useState( SSRedirection.redirect );
	const [ showAdvancedOptions, toggleAdvancedOptions ] = useReducer( onOrOff => !onOrOff, false );

	const handleChange = key => e => {
		const value = e.target.type === 'checkbox' ? Number( e.target.checked ) : ( 'note' === key ? e.target.value : e.target.value.trim() );
		setRedirect( prev => ( { ...prev, [ key ]: value } ) );
	};

	return (
		<>
			<table className='form-table ssr-post-form'>
				<tbody>
					<tr>
						<th scope='row'>
							<label htmlFor='ss-type'>{ __( 'Type', 'slim-seo' ) }
								<Tooltip content={ __( 'Redirect type', 'slim-seo' ) } />
							</label>
						</th>
						<td>
							<select id='ss-type' name='slim_seo_redirect[type]' value={ redirect.type } onChange={ handleChange( 'type' ) }>
								{ Object.entries( SSRedirection.redirectTypes ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
							</select>
							{ redirect.type == 410 && <p className='description'><small>{ __( '410 means the content is gone and no longer available. It can be deleted permanently. In this case, we need to return the 410 status instead of redirect. If you want to show an alternative page for this content, please consider a 3xx redirect.', 'slim-seo' ) }</small></p> }
						</td>
					</tr>
					
					{
						redirect.type != 410 && (
							<tr>
								<th scope='row'>
									<label htmlFor='ss-to'>
										{ __( 'To URL', 'slim-seo' ) }
										<Tooltip content={ __( 'Destination URL', 'slim-seo' ) } />
									</label>
								</th>
								<td>
									<ToInput name='slim_seo_redirect[to]' value={ redirect.to } setRedirect={ setRedirect } />
								</td>
							</tr>
						)
					}

					<tr>
						<th scope='row'>
							<label htmlFor='ss-note'>
								{ __( 'Note', 'slim-seo' ) }
								<Tooltip content={ __( 'Something that reminds you about this redirect', 'slim-seo' ) } />
							</label>
						</th>
						<td>
							<input id='ss-note' type='text' name='slim_seo_redirect[note]' value={ redirect.note } onChange={ handleChange( 'note' ) } />
						</td>
					</tr>

					<tr>
						<th scope='row'></th>
						<td>
							<label className='ss-toggle'>
								<input type='checkbox' name='slim_seo_redirect[enable]' value='1' checked={ !!redirect.enable } onChange={ handleChange( 'enable' ) } />
								<div className='ss-toggle__switch'></div>
								<span className='ss-toggle__label'>{ __( 'Enable', 'slim-seo' ) }</span>
							</label>
						</td>
					</tr>

					<tr>
						<th scope='row'></th>
						<td>
							<Button className='button-link' onClick={ toggleAdvancedOptions }>{ __( 'Advanced options', 'slim-seo' ) }</Button>

							{
								showAdvancedOptions && (
									<div>									
										<label className='ss-toggle'>
											<input type='checkbox' name='slim_seo_redirect[ignoreParameters]' value='1' checked={ !!redirect.ignoreParameters } onChange={ handleChange( 'ignoreParameters' ) } />
											<div className='ss-toggle__switch'></div>
											<span className='ss-toggle__label'>{ __( 'Ignore parameters', 'slim-seo' ) }</span>
										</label>
									</div>
								)
							}
						</td>
					</tr>
				</tbody>
			</table>
		</>
	);
};

const container = document.getElementById( 'ss-redirection' );
const root = createRoot( container );
root.render( <Post /> );