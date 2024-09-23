import { Control } from "@elightup/form";
import { useEffect, useState, useRef } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { normalize, request } from "../../functions";
import PropInserter from "./PropInserter";

const wpDescription = document.querySelector( '#description' );
const getDescription = () => wpDescription.value;

const TermDescription = ( { id, std = '', rows = 3, min = 50, max = 160, ...rest } ) => {
	let [ value, setValue ]     = useState( std );
	let [ preview, setPreview ] = useState( std );
	let [ placeholder, setPlaceholder ] = useState( std );
	let [ updateCount, setUpdateCount ] = useState( 0 );
	const descriptionRef = useRef( getDescription() );
	const inputRef = useRef();

	const requestUpdate = () => setUpdateCount( prev => prev + 1 );

	const handleChange = e => {
		setValue( e.target.value );
		requestUpdate();
	};

	const handleFocus = () => {
		setValue( prev => prev || placeholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === placeholder ? '' : prev );
	};

	const handleInsertVariables = variable => {
		setValue( prev => {
			// Insert variable at cursor.
			const cursorPosition = inputRef.current.selectionStart;
			return prev.slice( 0, cursorPosition ) + variable + prev.slice( cursorPosition );
		} );
		requestUpdate();
	};

	const refreshPreviewAndPlaceholder = () => {
		request( 'content/render_term_description', { ID: ss.id, text: value, description: descriptionRef.current } ).then( response => {
			setPreview( response.preview );
			setPlaceholder( response.default );
		} );
	};

	const handleDescriptionChange = () => {
		const description = getDescription();
		if ( descriptionRef.current === description ) {
			return;
		}
		descriptionRef.current = description;
		requestUpdate();
	};

	// Trigger refresh preview and placeholder when anything change.
	// Use debounce technique to avoid sending too many requests.
	useEffect( () => {
		const timer = setTimeout( refreshPreviewAndPlaceholder, 1000 );
		return () => clearTimeout( timer );
	}, [ updateCount ] );

	useEffect( () => {
		wpDescription.addEventListener( 'input', handleDescriptionChange );

		return () => {
			wpDescription.removeEventListener( 'input', handleDescriptionChange );
		};
	}, [] );

	const getClassName = () => min > preview.length || preview.length > max ? 'ss-input-warning' : 'ss-input-success';
	const getDescriptionDetail = () => sprintf( __( 'Character count: %s. Recommended length: 50-160 characters. Leave empty to autogenerate from the term description.', 'slim-seo' ), preview.length );

	return (
		<Control className={ getClassName() } description={ getDescriptionDetail() } id={ id } label={ __( 'Meta description', 'slim-seo' ) } { ...rest }>
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
					ref={ inputRef }
				/>
				<PropInserter onInsert={ handleInsertVariables } />
				<span>{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</span>
			</div>
		</Control>
	);
};

export default TermDescription;
