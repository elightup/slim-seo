import { Control } from "@elightup/form";
import { select, subscribe, unsubscribe } from "@wordpress/data";
import { useEffect, useRef, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { isBlockEditor, normalize } from "../../functions";
import PropInserter from "./PropInserter";

const formatTitle = title => {
	const values = {
		site: ss.site.title,
		tagline: ss.site.description,
		title
	};

	return ss.title.parts.map( part => values[ part ] ?? '' ).filter( part => part ).join( ` ${ ss.title.separator } ` );
};

const Title = ( { id, std, description, max = 60, ...rest } ) => {
	const inputRef = useRef();

	let [ placeholder, setPlaceholder ] = useState( std );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > max ? 'ss-input-warning' : 'ss-input-success' );
	const wpTitle = document.querySelector( '#title' );

	const updateCounterAndStatus = () => {
		let value = inputRef.current.value;

		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			setNewDescription( description );
			setNewClassName( '' );
			return;
		}

		value = value || placeholder;
		value = normalize( value );

		console.debug( value, placeholder );
		const text = sprintf( __( 'Character count: %s. %s', 'slim-seo' ), value.length, description );
		setNewDescription( text );
		setNewClassName( value.length > max ? 'ss-input-warning' : 'ss-input-success' );
	};

	const handleFocus = e => {
		inputRef.current.value = inputRef.current.value || placeholder;
	};

	const handleBlur = e => {
		inputRef.current.value = inputRef.current.value === placeholder ? '' : inputRef.current.value;
	};

	const handleTitleChange = () => {
		const title = isBlockEditor ? select( 'core/editor' ).getEditedPostAttribute( 'title' ) : ( wpTitle ? wpTitle.value : '' );
		setPlaceholder( formatTitle( title ) );
		updateCounterAndStatus();
	};

	// Update placeholder when post title changes.
	useEffect( () => {
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
		}
	}, [] );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input
					type="text"
					id={ id }
					name={ id }
					defaultValue={ std }
					ref={ inputRef }
					placeholder={ placeholder }
					onInput={ updateCounterAndStatus }
					onFocus={ handleFocus }
					onBlur={ handleBlur }
				/>
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Title;
