import { Control } from "@elightup/form";
import { useRef, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Description = ( { id, std = '', placeholder = '', min = 50, max = 160, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	const description = __( 'Recommended length: 50-160 characters.', 'slim-seo' );
	const inputRef = useRef();

	const handleChange = e => setValue( e.target.value );
	const handleFocus = () => setValue( prev => prev || placeholder );
	const handleBlur = () => setValue( prev => prev === placeholder ? '' : prev );

	const handleInsertVariables = variable => setValue( prev => {
		// Insert variable at cursor.
		const cursorPosition = inputRef.current.selectionStart;
		return prev.slice( 0, cursorPosition ) + variable + prev.slice( cursorPosition );
	} );

	const getClassName = () => {
		let desc = value || placeholder;
		// Do nothing if use variables.
		if ( desc.includes( '{{' ) ) {
			return '';
		}

		desc = normalize( desc );
		return min > desc.length || desc.length > max ? 'ss-input-warning' : 'ss-input-success';
	};

	const getDescription = () => {
		let desc = value || placeholder;
		// Do nothing if use variables.
		if ( desc.includes( '{{' ) ) {
			return description;
		}

		desc = normalize( desc );
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), desc.length, description );
	};

	return (
		<Control className={ getClassName() } description={ getDescription() } id={ id } label={ __( 'Meta description', 'slim-seo' ) } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea
					id={ id }
					name={ id }
					rows="3"
					value={ value }
					placeholder={ placeholder }
					onChange={ handleChange }
					onFocus={ handleFocus }
					onBlur={ handleBlur }
					ref={ inputRef }
				/>
				<PropInserter onInsert={ handleInsertVariables } />
			</div>
		</Control>
	);
};

export default Description;
