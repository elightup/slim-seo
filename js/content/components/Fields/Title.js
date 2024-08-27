import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { formatTitle, isBlockEditor, normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Title = ( { id, type = '', std, placeholder = '', isPost = true,  description, max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ newPlaceholder, setNewPlaceholder ] = useState( placeholder || std );
	const wpTitle = document.querySelector( '#title' ) || document.querySelector( '#name' );

	const getClassName = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return '';
		}

		const title = normalize( value || newPlaceholder );
		return title.length > max ? 'ss-input-warning' : 'ss-input-success';
	};

	const getDescription = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return description;
		}

		const title = normalize( value || newPlaceholder );
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), title.length, description );
	};

	const handleChange = e => {
		setValue( e.target.value );
	};

	const handleFocus = () => {
		setValue( prev => prev || newPlaceholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === newPlaceholder ? '' : prev );
	};

	const handleInsertVariables = value => {
		setValue( prev => prev + value );
	};

	const handleTitleChange = () => {
		const title = isBlockEditor ? select( 'core/editor' ).getEditedPostAttribute( 'title' ) : ( wpTitle ? wpTitle.value : '' );
		setNewPlaceholder( formatTitle( title ) );
	};

	// Update newPlaceholder when post title changes.
	useEffect( () => {
		if ( ! isPost ) {
			return;
		}

		handleTitleChange();
		if ( isBlockEditor ) {
			subscribe( handleTitleChange );
		} else if ( wpTitle ) {
			wpTitle.addEventListener( 'input', handleTitleChange );
		}

		return () => {
			if ( isBlockEditor ) {
				unsubscribe( handleTitleChange );
			} else if ( wpTitle ) {
				wpTitle.removeEventListener( 'input', handleTitleChange );
			}
		};
	}, [] );

	return (
		<Control className={ getClassName() } description={ getDescription() } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input
					type="text"
					id={ id }
					name={ id }
					value={ value }
					placeholder={ newPlaceholder }
					onChange={ handleChange }
					onFocus={ handleFocus }
					onBlur={ handleBlur }
				/>
				<PropInserter onInsert={ handleInsertVariables } />
			</div>
		</Control>
	);
};

export default Title;
