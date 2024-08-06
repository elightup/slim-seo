import { useState, useRef, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { normalize } from "../../functions";

const Text = ( property ) => {
	const inputRef = useRef();
	let { id, std, className= '', description, check = false, max = 0, ...rest } = property;

	let [ title, setTitle ] = useState( std );
	let [ wpTitle, setWpTitle ] = useState( null );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > max ? 'ss-input-warning': 'ss-input-success' );
	const { select, subscribe } = wp.data;

	const getTitle = () => {
		const editTitle = normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) );
		if ( !editTitle ) {
			return;
		}

		if ( !std && !editTitle.includes( '{{' ) ) {
			setTitle( editTitle );
			setNewDescription( formatDescription( editTitle ) );
			setNewClassName( editTitle.length > max ? 'ss-input-warning': 'ss-input-success' );
		}
		setWpTitle( formatTitle( editTitle ) );
	}

	const formatTitle = ( title ) => {
		// need to handle here
		return title;
	}

	const formatDescription = ( newDescription = '' ) => {
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), newDescription.length, description );
	}

	const handleChange = ( e ) => {
		setTitle( e.target.value );
		setNewDescription( formatDescription( e.target.value ) );
		setNewClassName( e.target.value.length > max ? 'ss-input-warning': 'ss-input-success' );
	}

	useEffect( () => {
		setTimeout( () => {
			if ( !check ) {
				return;
			}
			const initTitle = normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) );

			setWpTitle( initTitle );
			setTitle( std || formatTitle( initTitle ) );
			setNewDescription( formatDescription( std || formatTitle( initTitle ) ) );
		}, 200 );
	}, [] );

	subscribe( getTitle );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ title } ref={ inputRef } placeholder={ wpTitle } onChange={ handleChange } onClick={ handleChange } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Text;
