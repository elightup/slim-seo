import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { formatDescription, isBlockEditor, normalize, request } from "../../functions";
import PropInserter from "./PropInserter";

const Description = ( { id, placeholder = '', std = '', description = '', isSettings = false, rows = 3, min = 50, max = 160, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ newPlaceholder, setNewPlaceholder ] = useState( placeholder || std );
	let [ preview, setPreview ] = useState( std );
	const wpExcerpt = document.querySelector( '#excerpt' );
	const wpContent = document.querySelector( '#content' );
	let contentEditor;

	description = sprintf( __( 'Recommended length: 50-160 characters. %s', 'slim-seo' ), description );

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

		if ( !isSettings && e.target.value.includes( '{{' ) ) {
			request( 'content/render', { ID: ss.id, text: e.target.value } ).then( res => setPreview( prev => res ) );
		} else {
			setPreview( e.target.value || newPlaceholder );
		}
	};

	const handleFocus = () => {
		setValue( prev => prev || newPlaceholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === newPlaceholder ? '' : prev );
	};

	const handleInsertVariables = value => {
		setValue( prev => prev + value );
		if ( !isSettings ) {
			request( 'content/render', { ID: ss.id, text: value } ).then( res => setPreview( prev => prev + res ) );
		}
	};

	const handleDescriptionChange = () => {
		const desc = formatDescription( getPostExcerpt() || getPostContent(), max );
		setNewPlaceholder( desc, max );

		if ( !isSettings && desc.includes( '{{' ) ) {
			request( 'content/render', { ID: ss.id, text: desc } ).then( res => setPreview( res ) );
		} else {
			setPreview( desc );
		}
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
		<Control className={ getClassName() } description={ getDescription() } id={ id } label={ __( 'Meta description', 'slim-seo' ) } { ...rest }>
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
				{ !isSettings && <span>{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</span> }
			</div>
		</Control>
	);
};

export default Description;
