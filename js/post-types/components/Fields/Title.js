import { useState, useRef, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { normalize } from "../../functions";

const Title = ( { id, std, description, max = 0, isBlockEditor, ...rest } ) => {
	const inputRef = useRef();

	let [ suggest, setSuggest ] = useState( std );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > max ? 'ss-input-warning': 'ss-input-success' );
	const wpTitle = document.querySelector( '#title' );
	const { select, subscribe } = wp.data;

	const getTitle = () => {
		const editTitle = normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) );
		if ( !editTitle ) {
			return;
		}

		if ( !inputRef.current.value ) {
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
		return !newDescription.includes( '{{' ) ?
			  sprintf( __( 'Character count: %s. %s', 'slim-seo' ), newDescription.length, description )
			: description
	}

	const handleChange  = ( e ) => {
		const ssValue = normalize( e.target.value );

		inputRef.current.value = ssValue;
		setNewDescription( formatDescription( ssValue || suggest ) );
		setNewClassName( ( ssValue || suggest ).length > max ? 'ss-input-warning': 'ss-input-success' );
	}

	const handleClick  = ( e ) => {
		if ( e.target.value ) {
			return;
		}

		inputRef.current.value = suggest;
		setNewDescription( formatDescription( suggest ) );
		setNewClassName( suggest > max ? 'ss-input-warning': 'ss-input-success' );
	}

	const handleOnBlur = ( e ) => {
		inputRef.current.value = inputRef.current.value === e.target.value ? '' : inputRef.current.value;
	}

	const onChangeTitle = ( e ) => {
		const wpValue = normalize( e.target.value );

		if ( ! inputRef.current.value ) {
			setSuggest( formatTitle( wpValue ) );
		}
		setNewDescription( formatDescription( wpValue || suggest ) );
		setNewClassName( ( wpValue || suggest ).length > max ? 'ss-input-warning': 'ss-input-success' );
	}

	useEffect( () => {
		setTimeout( () => {
			const initTitle = isBlockEditor ? normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) ) : wpTitle ? normalize( wpTitle.value ) : '';

			setSuggest( formatTitle( initTitle ) );
			setNewDescription( formatDescription( std || formatTitle( initTitle ) ) );
			setNewClassName( initTitle.length > max ? 'ss-input-warning': 'ss-input-success' );
		}, 200 );

		if ( wpTitle ) {
			wpTitle.addEventListener( 'input', onChangeTitle );
		}
	}, [] );

	subscribe( getTitle );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } ref={ inputRef } placeholder={ suggest } onChange={ handleChange } onClick={ handleClick } onBlur={ handleOnBlur } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Title;
