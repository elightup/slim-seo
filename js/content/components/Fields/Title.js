import { Control } from "@elightup/form";
import { useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Title = ( { id, std = '', preview = '', placeholder = '', max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ newPlaceholder, setNewPlaceholder ] = useState( placeholder || std );
	const description = __( 'Recommended length: ≤ 60 characters.', 'slim-seo' );

	const handleChange = e => {
		setValue( e.target.value );
	};

	const handleFocus = () => {
		setValue( prev => prev || newPlaceholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === newPlaceholder ? '' : prev );
	};

	const handleInsertVariables = variable => {
		setValue( prev => prev + variable );
	};

	const getClassName   = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return '';
		}

		const title = normalize( value );
		return title.length > max ? 'ss-input-warning' : 'ss-input-success';
	}
	const getDescription = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return description;
		}

		const title = normalize( value );
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
					placeholder={ newPlaceholder }
				/>
				<PropInserter onInsert={ handleInsertVariables } />
			</div>
		</Control>
	);
};

export default Title;
