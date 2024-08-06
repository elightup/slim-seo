import { useState, useRef, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { normalize } from "../../functions";

const Textarea = ( property ) => {
	const inputRef = useRef();
	const { id, description, std, className = '', rows = 2, check = false, min = 0, max = 0, truncate, ...rest } = property;

	let [ holder, setHolder ] = useState( std );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > 160 ? 'ss-input-warning': 'ss-input-success' );
	const { select, subscribe } = wp.data;

	const getText = () => {
		if ( !check ) {
			return;
		}

		let editText = normalize( select( 'core/editor' ).getEditedPostContent() );
		if ( !editText ) {
			return;
		}

		if ( truncate ) {
			editText = editText.substring( 0, max );
		}
		if ( !inputRef.current.value && !editText.includes( '{{' ) ) {
			setHolder( editText );
			setNewDescription( formatDescription( editText ) );
			setNewClassName( min > editText.length || editText.length > max ? 'ss-input-warning': 'ss-input-success' );
		}
	}

	const formatDescription = ( newDescription = '' ) => {
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), newDescription.length, description );
	}

	const handleChange = ( e ) => {
		if ( !check ) {
			return;
		}

		inputRef.current.value = e.target.value;
console.log( 'handleChange ', e.target.value, holder );
		setNewDescription( formatDescription( e.target.value || holder ) );
		setNewClassName( min > ( e.target.value || holder ).length || ( e.target.value || holder ).length > max ? 'ss-input-warning': 'ss-input-success' );
	}

	const handleClick  = ( e ) => {
		if ( !check || e.target.value ) {
			return;
		}

		inputRef.current.value = holder;
		setNewDescription( formatDescription( holder ) );
		setNewClassName( holder > max ? 'ss-input-warning': 'ss-input-success' );
	}

	useEffect( () => {
		setTimeout( () => {
			if ( !check ) {
				return;
			}
			const initText = normalize( select( 'core/editor' ).getEditedPostContent() );

			setHolder( initText );
			setNewDescription( formatDescription( std || initText ) );
		}, 200 );
	}, [] );

	subscribe( getText );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea defaultValue={ std } id={ id } name={ id } rows={ rows } ref={ inputRef } placeholder={ holder }  onChange={ handleChange } onClick={ handleClick } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Textarea;
