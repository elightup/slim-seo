import { Button, Modal } from '@wordpress/components';
import { useEffect, useReducer, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tooltip, fetcher } from '../helper/misc';

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

	const handleChange = key => e => {
		const value = e.target.type === 'checkbox' ? Number( e.target.checked ) : ( 'note' === key ? e.target.value : e.target.value.trim() );
		setRedirect( prev => ( { ...prev, [ key ]: value } ) );
	};

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
					<Modal title={ title } overlayClassName='ss-modal ssr-modal' onRequestClose={ closeModal }>
						<div className='ssr-modal-field'>
							<label for='ss-type'>{ __( 'Type', 'slim-seo' ) }
								<Tooltip content={ __( 'Redirect type', 'slim-seo' ) } />
							</label>
							<select id='ss-type' value={ redirect.type } onChange={ handleChange( 'type' ) }>
								{ Object.entries( SSRedirection.redirectTypes ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
							</select>
						</div>

						<div className='ssr-modal-field'>
							<label for='ss-from'>
								{ __( 'From URL', 'slim-seo' ) }
								<Tooltip content={ __( 'URL to redirect', 'slim-seo' ) } />
							</label>

							<div className='ss-from-inputs'>
								<select value={ redirect.condition } onChange={ handleChange( 'condition' ) }>
									{ Object.entries( SSRedirection.conditionOptions ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
								</select>
								<input id='ss-from' type='text' value={ redirect.from } onChange={ handleChange( 'from' ) } />
							</div>
						</div>

						<div className='ssr-modal-field'>
							<label for='ss-to'>
								{ __( 'To URL', 'slim-seo' ) }
								<Tooltip content={ __( 'Destination URL', 'slim-seo' ) } />
							</label>
							<input id='ss-to' type='text' value={ redirect.to } onChange={ handleChange( 'to' ) } />
						</div>

						<div className='ssr-modal-field'>
							<label for='ss-note'>
								{ __( 'Note', 'slim-seo' ) }
								<Tooltip content={ __( 'Something that reminds you about this redirect', 'slim-seo' ) } />
							</label>
							<input id='ss-note' type='text' value={ redirect.note } onChange={ handleChange( 'note' ) } />
						</div>

						<div className='ssr-modal-field'>
							<label className='ss-toggle'>
								<input type='checkbox' value='1' checked={ !!redirect.enable } onChange={ handleChange( 'enable' ) } />
								<div className='ss-toggle__switch'></div>
								<span className='ss-toggle__label'>{ __( 'Enable', 'slim-seo' ) }</span>
							</label>
						</div>

						<div className='ssr-modal-field'>
							<Button className='button-link' onClick={ toggleAdvancedOptions }>{ __( 'Advanced options', 'slim-seo' ) }</Button>
						</div>

						{
							showAdvancedOptions && (
								<div className='ssr-modal-field'>
									<label className='ss-toggle'>
										<input type='checkbox' value='1' checked={ !!redirect.ignoreParameters } onChange={ handleChange( 'ignoreParameters' ) } />
										<div className='ss-toggle__switch'></div>
										<span className='ss-toggle__label'>{ __( 'Ignore parameters', 'slim-seo' ) }</span>
									</label>
								</div>
							)
						}

						<Button variant='primary' onClick={ submit } disabled={ isProcessing }>{ title }</Button>

						<p className='ss-warning-message'>{ warningMessage }</p>
					</Modal>
				)
			}
		</>
	);
};

export default Update;