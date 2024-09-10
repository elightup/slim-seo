import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { formatDescription, isBlockEditor, normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Description = ( { id, placeholder = '', std, description, isSettings = false, rows = 3, min = 50, max = 160, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ newPlaceholder, setNewPlaceholder ] = useState( placeholder || std );
	const wpExcerpt = document.querySelector( '#excerpt' );
	const wpContent = document.querySelector( '#content' );
	let contentEditor;

	const getClassName = () => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			return '';
		}

		const desc = normalize( value || newPlaceholder );
		return min > desc.length || desc.length > max ? 'ss-input-warning' : 'ss-input-success';
	};

	const getDescription = () => {
		if ( value.includes( '{{' ) ) {
			return description;
		}

		const desc = normalize( value || newPlaceholder );
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), desc.length, description );
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

	const handleDescriptionChange = () => {
		const desc = getPostExcerpt() || getPostContent();
		setNewPlaceholder( formatDescription( desc, max ) );
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

	// Update newPlaceholder when post description changes.
	useEffect( () => {
		if ( isSettings ) {
			return;
		}

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
				return;
			}
			if ( wpExcerpt ) {
				wpExcerpt.removeEventListener( 'input', handleDescriptionChange );
			}
			if ( wpContent ) {
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

export default Description;
