import { Control } from "@elightup/form";
import { useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Title = ( { id, std = '', preview = '', placeholder = '', onChange, max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ newPlaceholder, setNewPlaceholder ] = useState( placeholder || std );
	const description = __( 'Recommended length: ≤ 60 characters.', 'slim-seo' );

	const handleChange = e => {
		setValue( e.target.value );
		onChange && onChange( e.target.value );
	};

	const handleFocus = () => {
		setValue( prev => prev || newPlaceholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === newPlaceholder ? '' : prev );
	};

	const handleInsertVariables = variable => {
		setValue( prev => prev + variable );
		onChange && onChange(  value + variable );
	};

	const getClassName   = () => {
		const className = onChange ? preview : ( !value.includes( '{{' ) ? value : false )

		// Do nothing if use variables.
		if ( !className ) {
			return '';
		}
		return className.length > max ? 'ss-input-warning' : 'ss-input-success';
	}
	const getDescription = () => {
		const descriptionEdited = onChange ? preview : ( !value.includes( '{{' ) ? value : false );

		if ( !descriptionEdited ) {
			return description;
		}
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), descriptionEdited.length, description );
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
				{ onChange && <span>{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</span> }
			</div>
		</Control>
	);
};

export default Title;
