import { Button, Dropdown } from "@wordpress/components";
import { useEffect, useState } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import Select from "react-select";
import { request } from "../../functions";
import Inserter from "../Inserter";

const PropInserter = ( { data = 'meta-tags/variables', inputRef, replace = false, onInsert } ) => {
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
		if ( onInsert ) {
			onInsert( value );
			return;
		}

		if ( replace ) {
			inputRef.current.value = value;
		} else {
			inputRef.current.value += value;
		}
	};

	return <>
		<Dropdown
			className="ss-dropdown ss-inserter"
			position="bottom left"
			renderToggle={ ( { onToggle } ) => <Button icon="ellipsis" onClick={ onToggle } /> }
			renderContent={ ( { onToggle } ) => <VariableInserter data={ data } onSelect={ e => handleSelectItem( e, onToggle ) } /> }
		/>
		{ showModal && <Modal setShowModal={ setShowModal } setValue={ setValue } /> }
	</>;
};

const VariableInserter = ( { data, onSelect } ) => {
	const [ items, setItems ] = useState( [] );

	useEffect( () => {
		request( data ).then( setItems );
	}, [] );

	return <Inserter items={ items } group={ true } hasSearch={ true } onSelect={ onSelect } />;
};

const Modal = ( { setShowModal, setValue } ) => {
	const [ options, setOptions ] = useState( [] );
	useEffect( () => {
		request( 'meta-tags/meta_keys' ).then( setOptions );
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
				{ __( 'Select a custom field', 'slim-seo' ) }
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
