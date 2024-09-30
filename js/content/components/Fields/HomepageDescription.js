import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { request } from "../../functions";
import PropInserter from "./PropInserter";

const Description = ( { id, std = '', placeholder = '', description = '', rows = 3, min = 50, max = 160, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ updateCount, setUpdateCount ] = useState( 0 );
	let [ preview, setPreview ] = useState( std || placeholder );

	const requestUpdate = () => setUpdateCount( prev => prev + 1 );

	const handleChange = e => {
		setValue( e.target.value );
		requestUpdate();
	};

	const handleFocus = () => setValue( prev => prev || placeholder );

	const handleBlur = () => setValue( prev => prev === placeholder ? '' : prev );

	const handleInsertVariables = variable => {
		setValue( prev => prev + variable );
		requestUpdate();
	};

	const refreshPreview = () => request( 'content/render_text', { text: value || placeholder } ).then( res => setPreview( res ) );

	// Trigger refresh preview when value change.
	// Use debounce technique to avoid sending too many requests.
	useEffect( () => {
		const timer = setTimeout( refreshPreview, 1000 );
		return () => clearTimeout( timer );
	}, [ updateCount ] );

	const getClassName   = () => min > preview.length || preview.length > max ? 'ss-input-warning' : 'ss-input-success';
	const getDescription = () => sprintf( __( 'Character count: %s. Recommended length: 50-160 characters. Leave empty to use the default format.', 'slim-seo' ), preview.length, description );

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

export default Description;
