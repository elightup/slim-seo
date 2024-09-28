import { Control } from "@elightup/form";
import { useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Title = ( { id, std = '', preview = '', placeholder = '', onChange, max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ newPlaceholder, setNewPlaceholder ] = useState( placeholder || std );

	const handleChange = e => {
		setValue( e.target.value );
		onChange( e.target.value );
	};

	const handleFocus = () => {
		setValue( prev => prev || newPlaceholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === newPlaceholder ? '' : prev );
	};

	const handleInsertVariables = variable => {
		setValue( prev => prev + variable );
		onChange(  value + variable );
	};

	const getClassName   = () => preview.length > max ? 'ss-input-warning' : 'ss-input-success';
	const getDescription = () => sprintf( __( 'Character count: %s. Recommended length: ≤ 60 characters. Leave empty to use the default format.', 'slim-seo' ), preview.length );


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
				<span>{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</span>
			</div>
		</Control>
	);
};

export default Title;
