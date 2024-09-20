import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { isBlockEditor, normalize, request } from "../../functions";
import PropInserter from "./PropInserter";

export default ( { id, type = '', std = '', placeholder = '', max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ preview, setPreview ] = useState( std );
	let [ newPlaceholder, setNewPlaceholder ] = useState( placeholder || std );
	let [ updateCount, setUpdateCount ] = useState( 0 );
	const wpTitle = document.querySelector( '#title' );
	const description = __( 'Recommended length: ≤ 60 characters. Leave empty to use the default format.', 'slim-seo' );

	const getClassName = () => preview.length > max ? 'ss-input-warning' : 'ss-input-success';
	const getDescription = () => sprintf( __( 'Character count: %s. %s', 'slim-seo' ), preview.length, description );

	const handleChange = e => {
		setValue( e.target.value );
		setUpdateCount( prev => prev + 1 );
	};

	const handleFocus = () => {
		setValue( prev => prev || newPlaceholder );
	};

	const handleBlur = () => {
		setValue( prev => prev === newPlaceholder ? '' : prev );
	};

	const handleInsertVariables = variable => {
		setValue( prev => prev + variable );
		setUpdateCount( prev => prev + 1 );
	};

	const refreshPreviewAndPlaceholder = () => {
		request( 'content/render_post_title', { ID: ss.single.ID, text: value, title: getPostTitle() } ).then( response => {
			setPreview( normalize( response.rendered ) );
			setNewPlaceholder( response.default );
		} );
	};

	const getPostTitle = () => isBlockEditor ? select( 'core/editor' ).getEditedPostAttribute( 'title' ) : ( wpTitle ? wpTitle.value : '' );

	// Trigger refresh preview and placeholder when anything change.
	useEffect( () => {
		refreshPreviewAndPlaceholder();
	}, [ updateCount ] );

	// Update when post title changes.
	useEffect( () => {
		if ( isBlockEditor ) {
			subscribe( refreshPreviewAndPlaceholder );
		} else if ( wpTitle ) {
			wpTitle.addEventListener( 'input', refreshPreviewAndPlaceholder );
		}

		return () => {
			if ( isBlockEditor ) {
				unsubscribe( refreshPreviewAndPlaceholder );
			} else if ( wpTitle ) {
				wpTitle.removeEventListener( 'input', refreshPreviewAndPlaceholder );
			}
		};
	}, [] );

	return (
		<Control className={ getClassName() } description={ getDescription() } id={ id } label={ __( 'Meta title', 'slim-seo' ) } { ...rest }>
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
				{ <span>{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</span> }
			</div>
		</Control>
	);
};
