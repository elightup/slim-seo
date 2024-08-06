import { useState, useRef, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { normalize } from "../../functions";

const Textarea = ( property ) => {
	const inputRef = useRef();
	const { id, description, std, className = '', rows = 2, check = false, min = 0, max = 0, truncate, ...rest } = property;

	let [ text, setText ] = useState( std );
	let [ wpText, setWpText ] = useState( null );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > 160 ? 'ss-input-warning': 'ss-input-success' );
	const { select, subscribe } = wp.data;

	const getText = () => {
		let editText = normalize( select( 'core/editor' ).getEditedPostContent() );
		if ( !editText ) {
			return;
		}

		if ( truncate ) {
			editText = editText.substring( 0, max );
		}
		if ( !std && !editText.includes( '{{' ) ) {
			setText( editText );
			setNewDescription( formatDescription( editText ) );
			setNewClassName( min > editText.length || editText.length > max ? 'ss-input-warning': 'ss-input-success' );
		}
		setWpText( editText );
	}

	const formatDescription = ( newDescription = '' ) => {
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), newDescription.length, description );
	}

	const handleChange = ( e ) => {
		setText( e.target.value );
		setNewDescription( formatDescription( e.target.value ) );
		setNewClassName( min > e.target.value.length || e.target.value.length > max ? 'ss-input-warning': 'ss-input-success' );
	}

	useEffect( () => {
		setTimeout( () => {
			if ( !check ) {
				return;
			}
			const initText = normalize( select( 'core/editor' ).getEditedPostContent() );

			setWpText( initText );
			setText( std || initText );
			setNewDescription( formatDescription( std || initText ) );
		}, 200 );
	}, [] );

	subscribe( getText );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea defaultValue={ text } id={ id } name={ id } rows={ rows } ref={ inputRef } placeholder1={ wpText }  onChange={ handleChange } onClick={ handleChange } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Textarea;
