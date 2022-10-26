import { Button, Modal } from '@wordpress/components';
import { useReducer, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { fetcher, Tooltip } from '../helper/misc';

const Update = ( { redirectToEdit = SSRedirection.defaultRedirect, children, linkClassName, callback } ) => {
	const [ redirect, setRedirect ] = useState( redirectToEdit );
	const [ isProcessing, setIsProcessing ] = useState( false );
	const [ warningMessage, setWarningMessage ] = useState( '' );
	const [ showAdvancedOptions, toggleAdvancedOptions ] = useReducer( onOrOff => !onOrOff, false );
	const [ showUpdateRedirectModal, setShowUpdateRedirectModal ] = useState( false );
	const title = SSRedirection.defaultRedirect.id == redirectToEdit.id ? __( 'Add Redirect', 'slim-seo' ) : __( 'Update Redirect', 'slim-seo' );

	const showModal = e => {
		e.preventDefault();

		setShowUpdateRedirectModal( true );
	};

	const updateRedirect = () => {
		setWarningMessage( '' );

		fetcher( 'update_redirect', { redirect }, 'POST' ).then( result => {
			if ( SSRedirection.defaultRedirect.id == redirectToEdit.id ) {
				window.location.reload();
				return;
			}

			setShowUpdateRedirectModal( false );
			setIsProcessing( false );
			callback( redirect );
		} );
	};

	const handleChange = obj => setRedirect( prev => ( { ...prev, ...obj } ) );

	const updateRedirectButtonClicked = e => {
		e.preventDefault();

		if ( !redirect.from.length || !redirect.to.length ) {
			setWarningMessage( __( 'Please fill out From URL and To URL', 'slim-seo' ) );
			return;
		}

		setIsProcessing( true );

		if ( SSRedirection.defaultRedirect.id != redirect.id ) {
			updateRedirect();
			return;
		}

		fetcher( 'exists', { from: redirect.from } ).then( result => {
			if ( result ) {
				setIsProcessing( false );
				setWarningMessage( __( 'From URL already exists, which means this page already has a redirect rule!', 'slim-seo' ) );
			} else {
				updateRedirect();
			}
		} );
	};

	const closeModal = e => {
		e.preventDefault();

		setShowUpdateRedirectModal( false );
	};

	return (
		<>
			<a href='#' className={ linkClassName } onClick={ showModal } title={ title }>{ children ? children : title }</a>

			{
				showUpdateRedirectModal && (
					<Modal title={ title } overlayClassName='ss-modal' onRequestClose={ closeModal }>
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
									<input className='ss-toggle__checkbox' id='ss-enable' type='checkbox' name='ssr_enable' value={ redirect.enable } checked={ 1 == redirect.enable } onChange={ e => handleChange( { enable: 1 == redirect.enable ? 0 : 1 } ) } />
									<div className='ss-toggle__switch'></div>
									<span className='ss-toggle__label'>{ __( 'Enable', 'slim-seo' ) }</span>
								</label>
							</div>

							<div className='form-field'>
								<Button className='button-link' onClick={ toggleAdvancedOptions }>{ __( 'Advanced options', 'slim-seo' ) }</Button>
							</div>

							{
								showAdvancedOptions && (
									<div className='form-field'>
										<label className='ss-toggle'>
											<input className='ss-toggle__checkbox' id='ss-ignore-parameters' type='checkbox' name='ssr_ignore_parameters' value={ redirect.ignoreParameters } checked={ 1 == redirect.ignoreParameters } onChange={ e => handleChange( { ignoreParameters: 1 == redirect.ignoreParameters ? 0 : 1 } ) } />
											<div className='ss-toggle__switch'></div>
											<span className='ss-toggle__label'>{ __( 'Ignore parameters', 'slim-seo' ) }</span>
										</label>
									</div>
								)
							}

							<div className='form-field'>
								<Button variant='primary' onClick={ updateRedirectButtonClicked } disabled={ isProcessing }>
									{ SSRedirection.defaultRedirect.id == redirect.id ? __( 'Add Redirect', 'slim-seo' ) : __( 'Update Redirect', 'slim-seo' ) }
								</Button>
							</div>

							<p className='ss-warning-message'>{ warningMessage }</p>
						</div>
					</Modal>
				)
			}
		</>
	);
};

export default Update;