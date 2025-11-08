import { Button, Modal } from '@wordpress/components';
import { useEffect, useReducer, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { Tooltip } from '../../helper/Tooltip';
import { fetcher, useApi } from '../helper/misc';
import ToInput from './ToInput';

const Update = ( { redirectToEdit = {}, children, linkClassName, callback } ) => {
	const [ redirect, setRedirect ] = useState( {} );
	const [ isProcessing, setIsProcessing ] = useState( false );
	const [ warningMessage, setWarningMessage ] = useState( '' );
	const [ showAdvancedOptions, toggleAdvancedOptions ] = useReducer( onOrOff => !onOrOff, false );
	const [ showModal, setShowModal ] = useState( false );
	const { result: redirects, mutate } = useApi( 'redirects', {}, { returnMutate: true } );

	const title = redirect.id ? __( 'Update Redirect', 'slim-seo' ) : __( 'Add Redirect', 'slim-seo' );

	const openModal = e => {
		e.preventDefault();
		setShowModal( true );
	};
	const closeModal = () => setShowModal( false );

	const updateRedirect = () => {
		setWarningMessage( '' );

		fetcher( 'update_redirect', { redirect }, 'POST' ).then( id => {
			setShowModal( false );
			setIsProcessing( false );

			// Update a redirect.
			if ( redirect.id ) {
				callback( redirect );
				return;
			}

			// Add new redirect.
			redirect.id = id;
			let newRedirects = [ ...redirects ];
			newRedirects.unshift( redirect );
			mutate( newRedirects, { revalidate: false } );
		} );
	};

	const handleChange = key => e => {
		const value = e.target.type === 'checkbox' ? Number( e.target.checked ) : ( 'note' === key ? e.target.value : e.target.value.trim() );
		setRedirect( prev => ( { ...prev, [ key ]: value } ) );
	};

	const submit = e => {
		e.preventDefault();

		if ( !redirect.from.length ) {
			setWarningMessage( __( 'Please fill out From URL.', 'slim-seo' ) );
			return;
		}

		if ( redirect.type != 410 && !redirect.to.length ) {
			setWarningMessage( __( 'Please fill out To URL.', 'slim-seo' ) );
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
							<label htmlFor='ss-type'>{ __( 'Type', 'slim-seo' ) }
								<Tooltip content={ __( 'Redirect type', 'slim-seo' ) } />
							</label>
							<select id='ss-type' value={ redirect.type } onChange={ handleChange( 'type' ) }>
								{ Object.entries( SSRedirection.redirectTypes ).map( ( [ value, label ] ) => <option key={ value } value={ value }>{ label }</option> ) }
							</select>
							{ redirect.type == 410 && <p className='description'><small>{ __( '410 means the content is gone and no longer available. It can be deleted permanently. In this case, we need to return the 410 status instead of redirect. If you want to show an alternative page for this content, please consider a 3xx redirect.', 'slim-seo' ) }</small></p> }
						</div>

						<div className='ssr-modal-field'>
							<label htmlFor='ss-from'>
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

						{
							redirect.type != 410 &&
							<div className='ssr-modal-field'>
								<label htmlFor='ss-to'>
									{ __( 'To URL', 'slim-seo' ) }
									<Tooltip content={ __( 'Destination URL', 'slim-seo' ) } />
								</label>
								<ToInput value={ redirect.to } setRedirect={ setRedirect } />
							</div>
						}

						<div className='ssr-modal-field'>
							<label htmlFor='ss-note'>
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

						<button className='button button-primary' onClick={ submit } disabled={ isProcessing }>{ title }</button>

						{ warningMessage && <p className='ss-warning-message'>{ warningMessage }</p> }
					</Modal>
				)
			}
		</>
	);
};

export default Update;