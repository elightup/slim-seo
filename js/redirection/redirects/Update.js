import { Button, Modal } from '@wordpress/components';
import { useReducer, useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { fetcher, Tooltip } from '../helper/misc';

const Update = ( { redirectToEdit = {}, children, linkClassName, callback } ) => {
	const [ redirect, setRedirect ] = useState( {} );
	const [ isProcessing, setIsProcessing ] = useState( false );
	const [ warningMessage, setWarningMessage ] = useState( '' );
	const [ showAdvancedOptions, toggleAdvancedOptions ] = useReducer( onOrOff => !onOrOff, false );
	const [ showModal, setShowModal ] = useState( false );
	const title = redirect.id ? __( 'Update Redirect', 'slim-seo' ) : __( 'Add Redirect', 'slim-seo' );
	
	const openModal = e => {
		e.preventDefault();
		setShowModal( true );
	};
	const closeModal = () => setShowModal( false );

	const updateRedirect = () => {
		setWarningMessage( '' );

		fetcher( 'update_redirect', { redirect }, 'POST' ).then( result => {
			if ( ! redirect.id ) {
				window.location.reload();
				return;
			}

			setShowModal( false );
			setIsProcessing( false );
			callback( redirect );
		} );
	};

	const handleChange = obj => setRedirect( prev => ( { ...prev, ...obj } ) );

	const submit = e => {
		e.preventDefault();

		if ( !redirect.from.length || !redirect.to.length ) {
			setWarningMessage( __( 'Please fill out From URL and To URL', 'slim-seo' ) );
			return;
		}

		setIsProcessing( true );

		if ( redirect.id ) {
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

	useEffect( () => {
		setRedirect( { ...SSRedirection.defaultRedirect, ...redirectToEdit } );
	}, [ redirectToEdit ] );

	return (
		<>
			<a href='#' className={ linkClassName } onClick={ openModal } title={ title }>{ children ? children : title }</a>

			{
				showModal && (
					<Modal title={ title } overlayClassName='ss-modal' onRequestClose={ closeModal }>
						<div className='form-wrap'>
							<div className='form-field'>
								<label for='ss-type'>{ __( 'Type', 'slim-seo' ) }
									<Tooltip content={ __( 'Redirect type', 'slim-seo' ) } />
								</label>
								<select id='ss-type' value={ redirect.type } onChange={ e => handleChange( { type: e.target.value } ) }>
									{ Object.entries( SSRedirection.redirectTypes ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
								</select>
							</div>

							<div className='form-field'>
								<label for='ss-from'>
									{ __( 'From URL', 'slim-seo' ) }
									<Tooltip content={ __( 'URL to redirect', 'slim-seo' ) } />
								</label>

								<select value={ redirect.condition } onChange={ e => handleChange( { condition: e.target.value } ) }>
									{ Object.entries( SSRedirection.conditionOptions ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
								</select>
								<input id='ss-from' type='text' value={ redirect.from } onChange={ e => handleChange( { from: e.target.value.trim() } ) } />
							</div>

							<div className='form-field'>
								<label for='ss-to'>
									{ __( 'To URL', 'slim-seo' ) }
									<Tooltip content={ __( 'Destination URL', 'slim-seo' ) } />
								</label>
								<input id='ss-to' type='text' value={ redirect.to } onChange={ e => handleChange( { to: e.target.value.trim() } ) } />
							</div>

							<div className='form-field'>
								<label for='ss-note'>
									{ __( 'Note', 'slim-seo' ) }
									<Tooltip content={ __( 'Something that reminds you about this redirect', 'slim-seo' ) } />
								</label>
								<input id='ss-note' type='text' value={ redirect.note } onChange={ e => handleChange( { note: e.target.value } ) } />
							</div>

							<div className='form-field'>
								<label className='ss-toggle'>
									<input className='ss-toggle__checkbox' type='checkbox' value={ redirect.enable } checked={ 1 == redirect.enable } onChange={ e => handleChange( { enable: 1 == redirect.enable ? 0 : 1 } ) } />
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
											<input className='ss-toggle__checkbox' type='checkbox' value={ redirect.ignoreParameters } checked={ 1 == redirect.ignoreParameters } onChange={ e => handleChange( { ignoreParameters: 1 == redirect.ignoreParameters ? 0 : 1 } ) } />
											<div className='ss-toggle__switch'></div>
											<span className='ss-toggle__label'>{ __( 'Ignore parameters', 'slim-seo' ) }</span>
										</label>
									</div>
								)
							}

							<div className='form-field'>
								<Button variant='primary' onClick={ submit } disabled={ isProcessing }>{ title }</Button>
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