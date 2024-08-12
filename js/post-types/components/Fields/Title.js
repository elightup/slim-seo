import { Control } from "@elightup/form";
import { useEffect, useRef, useState } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { normalize } from "../../functions";
import PropInserter from "./PropInserter";

const Title = ( { id, std, description, max = 0, isBlockEditor, ...rest } ) => {
	const inputRef = useRef();

	let [ placeholder, setPlaceholder ] = useState( std );
	let [ newDescription, setNewDescription ] = useState( null );
	let [ newClassName, setNewClassName ] = useState( std.length > max ? 'ss-input-warning' : 'ss-input-success' );
	const wpTitle = document.querySelector( '#title' );
	const { select, subscribe } = wp.data;

	const getTitle = () => {
		const editTitle = normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) );
		if ( !editTitle ) {
			return;
		}

		if ( !inputRef.current.value ) {
			setPlaceholder( formatTitle( editTitle ) );
			updateCounterAndStatus( editTitle );
		}
	};

	const formatTitle = ( title ) => {
		const values = {
			site: ss.site.title,
			tagline: ss.site.description,
			title
		};

		return ss.title.parts.map( part => values[ part ] ?? '' ).filter( part => part ).join( ` ${ ss.title.separator } ` );
	};

	const updateCounterAndStatus = value => {
		// Do nothing if use variables.
		if ( value.includes( '{{' ) ) {
			setNewDescription( description );
			setNewClassName( '' );
			return;
		}

		const text = sprintf( __( 'Character count: %s. %s', 'slim-seo' ), value.length, description );
		setNewDescription( text );
		setNewClassName( value.length > max ? 'ss-input-warning' : 'ss-input-success' );
	};

	const handleInput = ( e ) => {
		const ssValue = normalize( e.target.value );

		inputRef.current.value = ssValue;
		updateCounterAndStatus( ssValue || placeholder );
	};

	const handleFocus = ( e ) => {
		if ( e.target.value ) {
			return;
		}

		inputRef.current.value = placeholder;
		updateCounterAndStatus( placeholder );
	};

	const handleBlur = ( e ) => {
		inputRef.current.value = inputRef.current.value === e.target.value ? '' : inputRef.current.value;
	};

	const onChangeTitle = ( e ) => {
		const wpValue = normalize( e.target.value );

		if ( !inputRef.current.value ) {
			setPlaceholder( formatTitle( wpValue ) );
		}
		updateCounterAndStatus( wpValue || placeholder );
	};

	useEffect( () => {
		setTimeout( () => {
			const initTitle = isBlockEditor ? normalize( select( 'core/editor' ).getEditedPostAttribute( 'title' ) ) : wpTitle ? normalize( wpTitle.value ) : '';

			setPlaceholder( formatTitle( initTitle ) );
			updateCounterAndStatus( std ||formatTitle( initTitle ) );
		}, 200 );

		if ( wpTitle ) {
			wpTitle.addEventListener( 'input', onChangeTitle );
		}
	}, [] );

	subscribe( getTitle );

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
					onInput={ handleInput }
					onFocus={ handleFocus }
					onBlur={ handleBlur }
				/>
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Title;
