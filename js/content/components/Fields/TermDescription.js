import { Control } from "@elightup/form";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { formatDescription, normalize, request } from "../../functions";
import PropInserter from "./PropInserter";

const TermDescription = ( { id, description = '', std = '', rows = 3, min = 50, max = 160, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ preview, setPreview ] = useState( std );
	let [ placeholder, setPlaceholder ] = useState( std );
	const wpDescription = document.querySelector( '#description' );

	description = sprintf( __( 'Recommended length: 50-160 characters. %s', 'slim-seo' ), description );

	const getClassName = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return '';
		}

		const desc = normalize( value || placeholder );
		return min > desc.length || desc.length > max ? 'ss-input-warning' : 'ss-input-success';
	};

	const getDescription = () => {
		if ( value.includes( '{{' ) ) {
			return description;
		}

		const desc = normalize( value || placeholder );
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), desc.length, description );
	};

	const handleChange = e => {
		setValue( e.target.value );

		if ( e.target.value.includes( '{{' ) ) {
			request( 'content/render', { ID: ss.single.ID, text: e.target.value } ).then( res => setPreview( prev => res ) );
		} else {
			setPreview( e.target.value || placeholder );
		}
	};

	const handleFocus = () => {
		setValue( prev => prev || placeholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === placeholder ? '' : prev );
	};

	const handleInsertVariables = value => {
		setValue( prev => prev + value );
		request( 'content/render', { ID: ss.single.ID, text: value } ).then( res => setPreview( prev => prev + res ) );
	};

	const handleDescriptionChange = () => {
		const desc = formatDescription( wpDescription ? wpDescription.value : '', max );
		setPlaceholder( desc );

		if ( value ) {
			return;
		}
		if ( desc.includes( '{{' ) ) {
			request( 'content/render', { ID: ss.single.ID, text: desc } ).then( res => setPreview( res ) );
		} else {
			setPreview( desc );
		}
	};

	// Update placeholder when term description changes.
	useEffect( () => {
		handleDescriptionChange();

		if ( wpDescription ) {
			wpDescription.addEventListener( 'input', handleDescriptionChange );
		}

		return () => {
			if ( wpDescription ) {
				wpDescription.removeEventListener( 'input', handleDescriptionChange );
			}
		};
	}, [] );

	return (
		<Control className={ getClassName() } description={ getDescription() } id={ id } label={ __( 'Meta description', 'slim-seo' ) } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea
					id={ id }
					name={ id }
					rows={ rows }
					value={ value }
					placeholder={ placeholder }
					onChange={ handleChange }
					onFocus={ handleFocus }
					onBlur={ handleBlur }
				/>
				<PropInserter onInsert={ handleInsertVariables } />
				<span>{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</span>
			</div>
		</Control>
	);
};

export default TermDescription;
