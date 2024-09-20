import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { formatTitle, isBlockEditor, normalize, request } from "../../functions";
import PropInserter from "./PropInserter";

const Title = ( { id, type = '', std = '', placeholder = '', max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	const description = __( 'Recommended length: ≤ 60 characters. Leave empty to use the default format.', 'slim-seo' );

	const getClassName = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return '';
		}

		const title = normalize( value );
		return title.length > max ? 'ss-input-warning' : 'ss-input-success';
	};

	const getDescription = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return description;
		}

		const title = normalize( value );
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), title.length, description );
	};

	const handleChange = e => {
		setValue( e.target.value );
	};

	const handleInsertVariables = value => {
		setValue( prev => prev + value );
	};

	return (
		<Control className={ getClassName() } description={ getDescription() } id={ id } label={ __( 'Meta title', 'slim-seo' ) } { ...rest }>
			<div className="ss-input-wrapper">
				<input
					type="text"
					id={ id }
					name={ id }
					value={ value }
					onChange={ handleChange }
				/>
				<PropInserter onInsert={ handleInsertVariables } />
			</div>
		</Control>
	);
};

export default Title;
