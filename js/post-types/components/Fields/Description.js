import { useState, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { isBlockEditor, normalize } from "../../functions";

const Description = ( { id, description, std, rows = 3, min = 0, max = 0, truncate = false, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ placeholder, setPlaceholder ] = useState( std );
	const wpExcerpt = document.querySelector( '#excerpt' );
	const wpContent = document.querySelector( '#content' );

	const prepareDescription = ( desc = '' ) => {
		let text = normalize( desc || value || placeholder );
		return truncate ? text.substring( 0, max ) : text;
	};

	const getClassName = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return '';
		}

		const ssDescription = prepareDescription();
		return min > ssDescription.length || ssDescription.length > max ? 'ss-input-warning' : 'ss-input-success';
	};

	const getDescription = () => {
		if ( value.includes( '{{' ) ) {
			return description;
		}

		const ssDescription = prepareDescription();
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), ssDescription.length, description );
	}

	const handleChange = e => {
		setValue( e.target.value );
	}

	const handleFocus = () => {
		setValue( prev => prev || placeholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === placeholder ? '' : prev );
	};

	const handleInsertVariables = e => {
		setValue( prev => prev + e );
	}

	const handleDescriptionChange = e => {
		const tinymceDescription  = e ? e.target.value || e.currentTarget.innerHTML : '';
		const wpDescription       = isBlockEditor ? select( 'core/editor' ).getEditedPostContent() : ( wpContent ? wpExcerpt.value || tinymceDescription || wpContent.value : '' );

		setPlaceholder( prepareDescription( wpDescription ) );
	};

	// Update placeholder when post description changes.
	useEffect( () => {
		handleDescriptionChange();

		if ( isBlockEditor ) {
			subscribe( handleDescriptionChange );
		} else if ( wpContent ) {
			wpExcerpt.addEventListener( 'input', handleDescriptionChange );
			wpContent.addEventListener( 'input', handleDescriptionChange );

			jQuery( document ).on( 'tinymce-editor-init', ( event, editor ) => {
				if ( editor.id !== 'content' ) {
					return;
				}
				editor.on( 'input keyup', handleDescriptionChange );
			} );
		}

		return () => {
			if ( isBlockEditor ) {
				unsubscribe( handleDescriptionChange );
			} else if ( wpContent ) {
				wpExcerpt.removeEventListener( 'input', handleDescriptionChange );
				wpContent.removeEventListener( 'input', handleDescriptionChange );
			}
		}
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
				<PropInserter onInsert= { handleInsertVariables } />
			</div>
		</Control>
	);
};

export default Description;
