import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { formatTitle, isBlockEditor, normalize, request } from "../../functions";
import PropInserter from "./PropInserter";

const Title = ( { id, type = '', std, placeholder = '', isSettings = false,  description, max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ preview, setPreview ] = useState( '' );
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
		if ( ! isSettings ) {
			request( 'content/render', { ID: ss.single.ID, text: e.target.value } ).then( res => setPreview( res ) );
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
		if ( ! isSettings ) {
			request( 'content/render', { ID: ss.single.ID, text: value } ).then( res => setPreview( prev => prev + res ) );
		}
	};

	const handleTitleChange = () => {
		const title = isBlockEditor ? select( 'core/editor' ).getEditedPostAttribute( 'title' ) : ( wpTitle ? wpTitle.value : '' );
		setNewPlaceholder( formatTitle( title ) );
	};

	// Update newPlaceholder when post title changes.
	useEffect( () => {
		if ( isSettings ) {
			return;
		}

		if ( std.includes( '{{' ) ) {
			request( 'content/render', { ID: ss.single.ID, text: std } ).then( res => setPreview( res ) );
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
				<span>{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</span>
			</div>
		</Control>
	);
};

export default Title;
