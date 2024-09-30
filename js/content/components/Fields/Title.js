import { Control } from "@elightup/form";
import { useRef, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Title = ( { id, std = '', preview = '', placeholder = '', max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	const description = __( 'Recommended length: ≤ 60 characters.', 'slim-seo' );
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
		let title = value || placeholder;
		// Do nothing if use variables.
		if ( title.includes( '{{' ) ) {
			return '';
		}

		title = normalize( value );
		return title.length > max ? 'ss-input-warning' : 'ss-input-success';
	};
	const getDescription = () => {
		let title = value || placeholder;
		// Do nothing if use variables.
		if ( title.includes( '{{' ) ) {
			return description;
		}

		title = normalize( value );
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), title.length, description );
	};

	return (
		<Control className={ getClassName() } description={ getDescription() } id={ id } label={ __( 'Meta title', 'slim-seo' ) } { ...rest }>
			<div className="ss-input-wrapper">
				<input
					type="text"
					id={ id }
					name={ id }
					value={ value }
					onBlur={ handleBlur }
					onFocus={ handleFocus }
					onChange={ handleChange }
					placeholder={ placeholder }
					ref={ inputRef }
				/>
				<PropInserter onInsert={ handleInsertVariables } />
			</div>
		</Control>
	);
};

export default Title;
