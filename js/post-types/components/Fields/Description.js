import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { isBlockEditor, normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Description = ( { id, description, std, rows = 3, min = 50, max = 160, truncate = true, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ placeholder, setPlaceholder ] = useState( std );
	const wpExcerpt = document.querySelector( '#excerpt' );
	const wpContent = document.querySelector( '#content' );
	let contentEditor;

	const format = text => {
		text = normalize( text );
		return truncate ? text.substring( 0, max ) : text;
	};

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
		const desc = getPostExcerpt() || getPostContent();
		setPlaceholder( format( desc ) );
	};

	const getPostContent = () => {
		if ( isBlockEditor ) {
			return wp.data.select( 'core/editor' ).getEditedPostContent();
		}
		return contentEditor && !contentEditor.isHidden() ? contentEditor.getContent() : ( wpContent ? wpContent.value : '' );
	};

	const getPostExcerpt = () => {
		return isBlockEditor ? select( 'core/editor' ).getEditedPostAttribute( 'excerpt' ) : ( wpExcerpt ? wpExcerpt.value : '' );
	};

	// Update placeholder when post description changes.
	useEffect( () => {
		handleDescriptionChange();

		if ( isBlockEditor ) {
			subscribe( handleDescriptionChange );
		} else {
			if ( wpExcerpt ) {
				wpExcerpt.addEventListener( 'input', handleDescriptionChange );
			}
			if ( wpContent ) {
				jQuery( document ).on( 'tinymce-editor-init', ( event, editor ) => {
					if ( editor.id !== 'content' ) {
						return;
					}
					contentEditor = editor;
					editor.on( 'input keyup', handleDescriptionChange );
				} );
			}
		}

		return () => {
			if ( isBlockEditor ) {
				unsubscribe( handleDescriptionChange );
			} else if ( wpContent ) {
				wpExcerpt.removeEventListener( 'input', handleDescriptionChange );
				wpContent.removeEventListener( 'input', handleDescriptionChange );
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

export default Description;
