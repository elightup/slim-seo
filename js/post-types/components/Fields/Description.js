import { useState, useRef, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { normalize } from "../../functions";

const Description = ( { id, description, std, className = '', rows = 2, min = 0, max = 0, truncate = false, isBlockEditor, ...rest } ) => {
	const inputRef = useRef();

	let [ suggest, setSuggest ] = useState( std );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > 160 ? 'ss-input-warning': 'ss-input-success' );
	const wpExcerpt = document.querySelector( '#excerpt' );
	const wpContent = document.querySelector( '#content' );
	const { select, subscribe } = wp.data;

	const getText = () => {
		let editText = normalize( select( 'core/editor' ).getEditedPostContent() );
		if ( !editText ) {
			return;
		}

		if ( truncate ) {
			editText = editText.substring( 0, max );
		}
		if ( !inputRef.current.value && !editText.includes( '{{' ) ) {
			setSuggest( editText );
			setNewDescription( formatDescription( editText ) );
			setNewClassName( min > editText.length || editText.length > max ? 'ss-input-warning': 'ss-input-success' );
		}
	}

	const formatDescription = ( newDescription = '' ) => {
		return !newDescription.includes( '{{' ) ?
			  sprintf( __( 'Character count: %s. %s', 'slim-seo' ), newDescription.length, description )
			: description
	}

	const handleChange = ( e ) => {
		inputRef.current.value = e.target.value;

		setNewDescription( formatDescription( e.target.value || suggest ) );
		setNewClassName( min > ( e.target.value || suggest ).length || ( e.target.value || suggest ).length > max ? 'ss-input-warning': 'ss-input-success' );
	}

	const handleClick  = ( e ) => {
		if ( e.target.value ) {
			return;
		}

		inputRef.current.value = suggest;
		setNewDescription( formatDescription( suggest ) );
		setNewClassName( min > suggest || suggest > max ? 'ss-input-warning': 'ss-input-success' );
	}

	const onChangeDescription = ( e ) => {}

	useEffect( () => {
		setTimeout( () => {
			let initText = isBlockEditor ? normalize( select( 'core/editor' ).getEditedPostContent() ) : normalize( wpExcerpt.value || wpContent.value );
			if ( truncate ) {
				initText = initText.substring( 0, max );
			}

			setSuggest( initText );
			setNewDescription( formatDescription( std || initText ) );
		}, 200 );

		if ( wpContent ) {
			wpContent.addEventListener( 'input', onChangeDescription );
		}
	}, [] );

	subscribe( getText );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea defaultValue={ std } id={ id } name={ id } rows={ rows } ref={ inputRef } placeholder={ suggest }  onChange={ handleChange } onClick={ handleClick } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Description;
