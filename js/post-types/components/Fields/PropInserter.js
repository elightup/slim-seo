import { Button, Dropdown } from "@wordpress/components";
import { useContext, useEffect, useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import Select from "react-select";
import slugify from "slugify";
import { request } from "../../functions";
import Inserter from "../Inserter";

const sanitizeId = text => slugify( text, { lower: true } )
	.replace( /[^a-z0-9_]/g, '_' )           // Only accepts alphanumeric and underscores.
	.replace( /[ _]{2,}/g, '_' )             // Remove duplicated `_`.
	.replace( /^_/, '' ).replace( /_$/, '' ) // Trim `_`.
	.replace( /^\d+/, '' )                   // Don't start with numbers.
	.replace( /^_/, '' ).replace( /_$/, '' ) // Trim `_` again.
	;

const PropInserter = ( { inputRef, extraInputRef } ) => {
	const [ showModal, setShowModal ] = useState( false );

	const handleSelectItem = ( e, onToggle ) => {
		onToggle();
		if ( e.target.dataset.value === 'post.custom_field' ) {
			setShowModal( true );
			return;
		}

		setValue( `{{ ${ e.target.dataset.value } }}` );
	};

	const setValue = value => {
		inputRef.current.value += value;
		if ( extraInputRef ) {
			extraInputRef.current.value += value;
		}
	};

	return <>
		<Dropdown
			className="ss-dropdown ss-inserter"
			position="bottom left"
			renderToggle={ ( { onToggle } ) => <Button icon="ellipsis" onClick={ onToggle } /> }
			renderContent={ ( { onToggle } ) => <VariableInserter onSelect={ e => handleSelectItem( e, onToggle ) } /> }
		/>
		{ showModal && <Modal setShowModal={ setShowModal } setValue={ setValue } /> }
	</>;
};

const VariableInserter = ( { onSelect } ) => {
	const [ items, setItems ] = useState( [] );

	useEffect( () => {
		request( 'variables' ).then( setItems );
	}, [] );

	return <Inserter items={ items } group={ true } hasSearch={ true } onSelect={ onSelect } />;
};

const Modal = ( { setShowModal, setValue } ) => {
	const [ options, setOptions ] = useState( [] );
	useEffect( () => {
		request( 'meta_keys' ).then( setOptions );
	}, [] );
	const hideModal = () => setShowModal( false );
	const onSelect = item => {
		setValue( `{{ post.custom_field.${ item.value } }}` );
		setShowModal( false );
	};

	return <>
		<div className="ss-modal-overlay" onClick={ hideModal }></div>
		<div className="ss-modal-body">
			<h3 className="ss-modal-heading">
				{ __( 'Select a custom field', 'slim-seo-schema' ) }
				<span className="ss-modal__close" onClick={ hideModal }>&times;</span>
			</h3>
			<Select
				classNamePrefix="react-select"
				options={ options }
				defaultOptions
				onChange={ onSelect }
			/>
		</div>
	</>;
};

export default PropInserter;
