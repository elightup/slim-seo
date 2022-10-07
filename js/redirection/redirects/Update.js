import { useEffect, useReducer, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tooltip } from '../helper/misc';
import request from '../helper/request';

const Update = ( { redirectToEdit, callback, setShowUpdateRedirectModal } ) => {
	const [ redirect, setRedirect ] = useState( {} );
	const [ isProcessing, setIsProcessing ] = useState( false );
	const [ warningMessage, setWarningMessage ] = useState( '' );
	const [ showAdvancedOptions, toggleAdvancedOptions ] = useReducer( onOrOff => !onOrOff, false );

	const updateRedirect = () => {
		setWarningMessage( '' );

		request( 'update_redirect', { redirect }, 'POST' ).then( result => {
			setShowUpdateRedirectModal( false );
			callback();
		} );
	};

	const handleChange = obj => {
		setRedirect( prev => ( { ...prev, ...obj } ) );
	};

	const updateRedirectButtonClicked = e => {
		e.preventDefault();

		if ( !redirect.from.length || !redirect.to.length ) {
			setWarningMessage( __( 'Please fill out From URL and To URL', 'slim-seo' ) );
			return;
		}

		setIsProcessing( true );

		if ( SSRedirection.defaultRedirect.id == redirect.id ) {
			request( 'is_exists', { from: redirect.from } ).then( result => {
				if ( result ) {
					setIsProcessing( false );
					setWarningMessage( __( 'From URL already exists, which means this page already has a redirect rule!', 'slim-seo' ) );
				} else {
					updateRedirect();
				}
			} );
		} else {
			updateRedirect();
		}

	};

	const closeModalButtonClicked = e => {
		e.preventDefault();

		setShowUpdateRedirectModal( false );
	};

	useEffect( () => {
		setRedirect( prev => redirectToEdit );
	}, [ redirectToEdit ] );

	return (
		<div className='ss-modal'>
			<div className='ss-modal__content'>
				<button className='ss-modal__close' onClick={ closeModalButtonClicked }>&times;</button>
				<h3>{ SSRedirection.defaultRedirect.id == redirect.id ? __( 'Add Redirect', 'slim-seo' ) : __( 'Update Redirect', 'slim-seo' ) }</h3>

				<div className='form-wrap'>
					<div className='form-field'>
						<label for='ss-type'>{ __( 'Type', 'slim-seo' ) }
							<Tooltip content={ __( 'Redirect type', 'slim-seo' ) } />
						</label>
						<select id='ss-type' name='ssr_type' value={ redirect.type } onChange={ e => handleChange( { type: e.target.value } ) }>
							{ Object.entries( SSRedirection.redirectTypes ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
						</select>
					</div>

					<div className='form-field'>
						<label for='ss-from'>
							{ __( 'From URL', 'slim-seo' ) }
							<Tooltip content={ __( 'URL to redirect', 'slim-seo' ) } />
						</label>

						<select id='ss-condition' name='ssr_condition' value={ redirect.condition } onChange={ e => handleChange( { condition: e.target.value } ) }>
							{ Object.entries( SSRedirection.conditionOptions ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
						</select>
						<input id='ss-from' type='text' name='ssr_from' value={ redirect.from } onChange={ e => handleChange( { from: e.target.value.trim() } ) } />
					</div>

					<div className='form-field'>
						<label for='ss-to'>
							{ __( 'To URL', 'slim-seo' ) }
							<Tooltip content={ __( 'Destination URL', 'slim-seo' ) } />
						</label>
						<input id='ss-to' type='text' name='ssr_to' value={ redirect.to } onChange={ e => handleChange( { to: e.target.value.trim() } ) } />
					</div>

					<div className='form-field'>
						<label for='ss-note'>
							{ __( 'Note', 'slim-seo' ) }
							<Tooltip content={ __( 'Something that reminds you about this redirect', 'slim-seo' ) } />
						</label>
						<input id='ss-note' type='text' name='ssr_note' value={ redirect.note } onChange={ e => handleChange( { note: e.target.value } ) } />
					</div>

					<div className='form-field'>
						<label className='ss-toggle'>
							<input className='ss-toogle__checkbox' id='ss-enable' type='checkbox' name='ssr_enable' value={ redirect.enable } checked={ 1 == redirect.enable } onChange={ e => handleChange( { enable: 1 == redirect.enable ? 0 : 1 } ) } />
							<div className='ss-toogle__switch'></div>
							<span className='ss-toogle__label'>{ __( 'Enable', 'slim-seo' ) }</span>
						</label>
					</div>

					<div className='form-field'>
						<button type='button' className='button-link' onClick={ toggleAdvancedOptions }>{ __( 'Advanced options', 'slim-seo' ) }</button>
					</div>

					{
						showAdvancedOptions ? (
							<div className='form-field'>
								<label className='ss-toggle'>
									<input className='ss-toogle__checkbox' id='ss-ignore-parameters' type='checkbox' name='ssr_ignore_parameters' value={ redirect.ignoreParameters } checked={ 1 == redirect.ignoreParameters } onChange={ e => handleChange( { ignoreParameters: 1 == redirect.ignoreParameters ? 0 : 1 } ) } />
									<div className='ss-toogle__switch'></div>
									<span className='ss-toogle__label'>{ __( 'Ignore parameters', 'slim-seo' ) }</span>
								</label>
							</div>
						) : ''
					}

					<div className='form-field'>
						<button className='button button-primary' onClick={ updateRedirectButtonClicked } disabled={ true == isProcessing ? 'disabled' : '' }>
							{ SSRedirection.defaultRedirect.id == redirect.id ? __( 'Add Redirect', 'slim-seo' ) : __( 'Update Redirect', 'slim-seo' ) }
						</button>
					</div>

					<p className='ss-warning-message'>{ warningMessage }</p>
				</div>
			</div>
		</div>
	);
};

export default Update;