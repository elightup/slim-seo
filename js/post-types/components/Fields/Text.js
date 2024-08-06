import { useState, useRef, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { normalize } from "../../functions";

const Text = ( property ) => {
	const inputRef = useRef();
	let { id, std, className= '', description, check = false, max = 0, ...rest } = property;

	let [ holder, setHolder ] = useState( std );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > max ? 'ss-input-warning': 'ss-input-success' );
	const { select, subscribe } = wp.data;

	const getTitle = () => {
		if ( !check ) {
			return;
		}

		const editTitle = normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) );
		if ( !editTitle ) {
			return;
		}

		if ( !inputRef.current.value && !editTitle.includes( '{{' ) ) {
			setHolder( editTitle );
			setNewDescription( formatDescription( editTitle ) );
			setNewClassName( editTitle.length > max ? 'ss-input-warning': 'ss-input-success' );
		}
	}

	const formatTitle = ( title ) => {
		// need to handle here
		return title;
	}

	const formatDescription = ( newDescription = '' ) => {
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), newDescription.length, description );
	}

	const handleChange  = ( e ) => {
		if ( !check ) {
			return;
		}

		inputRef.current.value = e.target.value;
		setNewDescription( formatDescription( e.target.value || holder ) );
		setNewClassName( ( e.target.value || holder ).length > max ? 'ss-input-warning': 'ss-input-success' );
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
			const initTitle = normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) );

			setHolder( formatTitle( initTitle ) );
			setNewDescription( formatDescription( std || formatTitle( initTitle ) ) );
		}, 200 );
	}, [] );

	subscribe( getTitle );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } ref={ inputRef } placeholder={ holder } onChange={ handleChange } onClick={ handleClick } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Text;
