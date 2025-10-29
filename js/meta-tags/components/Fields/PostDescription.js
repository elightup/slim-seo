import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useRef, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { isBlockEditor, request } from "../../functions";
import PropInserter from "./PropInserter";

const PostDescription = ( { id, std = '', min = 50, max = 160, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ preview, setPreview ] = useState( std );
	let [ placeholder, setPlaceholder ] = useState( std );
	let [ updateCount, setUpdateCount ] = useState( 0 );
	const inputRef = useRef();
	let contentEditor;

	const wpExcerpt = document.querySelector( '#excerpt' );
	const wpContent = document.querySelector( '#content' );
	const getContent = () => {
		if ( isBlockEditor ) {
			return wp.data.select( 'core/editor' ).getEditedPostContent();
		}
		return contentEditor && !contentEditor.isHidden() ? contentEditor.getContent() : ( wpContent ? wpContent.value : '' );
	};
	const getExcerpt = () => isBlockEditor ? select( 'core/editor' ).getEditedPostAttribute( 'excerpt' ) : ( wpExcerpt ? wpExcerpt.value : '' );

	const excerptRef = useRef( getExcerpt() );
	const contentRef = useRef( getContent() );

	const requestUpdate = () => setUpdateCount( prev => prev + 1 );

	const handleChange = e => {
		setValue( e.target.value );
		requestUpdate();
	};

	const handleFocus = () => setValue( prev => prev || placeholder );
	const handleBlur = () => setValue( prev => prev === placeholder ? '' : prev );

	const handleInsertVariables = variable => {
		setValue( prev => {
			// Insert variable at cursor.
			const cursorPosition = inputRef.current.selectionStart;
			return prev.slice( 0, cursorPosition ) + variable + prev.slice( cursorPosition );
		} );
		requestUpdate();
	};

	const refreshPreviewAndPlaceholder = () => {
		request( 'meta-tags/preview/post-description', { ID: ss.id, text: value, excerpt: excerptRef.current, content: contentRef.current } ).then( response => {
			setPreview( response.preview );
			setPlaceholder( response.default );
		} );
	};

	const handleDescriptionChange = () => {
		const excerpt = getExcerpt();
		const content = getContent();
		if ( excerptRef.current === excerpt && contentRef.current === content ) {
			return;
		}
		excerptRef.current = excerpt;
		contentRef.current = content;

		requestUpdate();
	};

	// Trigger refresh preview and placeholder when anything change.
	// Use debounce technique to avoid sending too many requests.
	useEffect( () => {
		const timer = setTimeout( refreshPreviewAndPlaceholder, 1000 );
		return () => clearTimeout( timer );
	}, [ updateCount ] );

	useEffect( () => {
		if ( isBlockEditor ) {
			subscribe( handleDescriptionChange );
		} else {
			if ( wpExcerpt ) {
				wpExcerpt.addEventListener( 'input', handleDescriptionChange );
			}
			if ( wpContent ) {
				wpContent.addEventListener( 'input', handleDescriptionChange );

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

	const getClassName = () => min > preview.length || preview.length > max ? 'ss-input-warning' : 'ss-input-success';
	const getDescriptionDetail = () => sprintf( __( 'Character count: %s. Recommended length: 50-160 characters.', 'slim-seo' ), preview.length );

	return (
		<Control className={ getClassName() } description={ getDescriptionDetail() } id={ id } label={ __( 'Meta description', 'slim-seo' ) } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea
					id={ id }
					name={ id }
					rows="3"
					value={ value }
					placeholder={ placeholder }
					onChange={ handleChange }
					onFocus={ handleFocus }
					onBlur={ handleBlur }
					ref={ inputRef }
				/>
				{ preview && <div className="ss-preview">{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</div> }
				<PropInserter onInsert={ handleInsertVariables } />
			</div>
		</Control>
	);
};

export default PostDescription;
