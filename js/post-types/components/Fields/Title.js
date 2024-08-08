import { useState, useRef, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { normalize } from "../../functions";

const Title = ( property ) => {
	const inputRef = useRef();
	let { id, std, className= '', description, max = 0, ...rest } = property;

	let [ suggest, setSuggest ] = useState( std );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > max ? 'ss-input-warning': 'ss-input-success' );
	const { select, subscribe } = wp.data;

	const getTitle = () => {
		const editTitle = normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) );
		if ( !editTitle ) {
			return;
		}

		if ( !inputRef.current.value && !editTitle.includes( '{{' ) ) {
			setSuggest( formatTitle( editTitle ) );
			setNewDescription( formatDescription( editTitle ) );
			setNewClassName( editTitle.length > max ? 'ss-input-warning': 'ss-input-success' );
		}
	}

	const formatTitle = ( title ) => {
		const values = {
			site: ss.site.title,
			tagline: ss.site.description,
			title
		};

		return ss.title.parts.map( part => values[ part ] ?? '' ).filter( part => part ).join( ` ${ ss.title.separator } ` );
	}

	const formatDescription = ( newDescription = '' ) => {
		return sprintf( __( 'Character count: %s. %s', 'slim-seo' ), newDescription.length, description );
	}

	const handleChange  = ( e ) => {
		inputRef.current.value = e.target.value;
		setNewDescription( formatDescription( e.target.value || suggest ) );
		setNewClassName( ( e.target.value || suggest ).length > max ? 'ss-input-warning': 'ss-input-success' );
	}

	const handleClick  = ( e ) => {
		if ( e.target.value ) {
			return;
		}

		inputRef.current.value = suggest;
		setNewDescription( formatDescription( suggest ) );
		setNewClassName( suggest > max ? 'ss-input-warning': 'ss-input-success' );
	}

	useEffect( () => {
		setTimeout( () => {
			const initTitle = normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) );

			setSuggest( formatTitle( initTitle ) );
			setNewDescription( formatDescription( std || formatTitle( initTitle ) ) );
		}, 200 );
	}, [] );

	subscribe( getTitle );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } ref={ inputRef } placeholder={ suggest } onChange={ handleChange } onClick={ handleClick } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Title;
