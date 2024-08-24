import { Control } from "@elightup/form";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { formatDescription, normalize } from "../../functions";
import PropInserter from "./PropInserter";

const TermDescription = ( { id, description, std, rows = 3, min = 50, max = 160, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ placeholder, setPlaceholder ] = useState( std );
	const wpDescription = document.querySelector( '#description' );

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
	};

	const handleFocus = () => {
		setValue( prev => prev || placeholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === placeholder ? '' : prev );
	};

	const handleInsertVariables = value => {
		setValue( prev => prev + value );
	};

	const handleDescriptionChange = () => {
		const desc = wpDescription ? wpDescription.value : '';
		setPlaceholder( formatDescription( desc, max ) );
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
		<Control className={ getClassName() } description={ getDescription() } id={ id } { ...rest }>
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
			</div>
		</Control>
	);
};

export default TermDescription;
