import { useState, useRef, useEffect } from "@wordpress/element";
import { __, sprintf } from "@wordpress/i18n";
import { Control } from "@elightup/form";
import PropInserter from "./PropInserter";
import { normalize } from "../../functions";

const Description = ( { id, description, std, rows = 2, min = 0, max = 0, truncate = false, isBlockEditor, ...rest } ) => {
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

	const handleOnBlur = ( e ) => {
		inputRef.current.value = inputRef.current.value === e.target.value ? '' : inputRef.current.value;
	}

	const onChangeDescription = ( e ) => {
		const wpValue = normalize( e.target.value || e.currentTarget.innerHTML );

		if ( ! inputRef.current.value ) {
			setSuggest( wpValue );
		}
		setNewDescription( formatDescription( wpValue || suggest ) );
		setNewClassName( min > ( wpValue || suggest ).length || ( wpValue || suggest ).length > max ? 'ss-input-warning': 'ss-input-success' );
	}

	useEffect( () => {
		setTimeout( () => {
			let initText = isBlockEditor ? normalize( select( 'core/editor' ).getEditedPostContent() ) : wpContent ? normalize( wpExcerpt.value || wpContent.value ) : '';
			if ( truncate ) {
				initText = initText.substring( 0, max );
			}

			setSuggest( initText );
			setNewDescription( formatDescription( std || initText ) );
			setNewClassName( min > ( std || initText ).length || ( std || initText ).length > max ? 'ss-input-warning': 'ss-input-success' );
		}, 200 );

		if ( wpContent ) {
			wpExcerpt.addEventListener( 'input', onChangeDescription );
			wpContent.addEventListener( 'input', onChangeDescription );

			jQuery( document ).on( 'tinymce-editor-init', ( event, editor ) => {
				if ( editor.id !== 'content' ) {
					return;
				}
				editor.on( 'input keyup', onChangeDescription );
			} );
		}
	}, [] );

	subscribe( getText );

	return (
		<Control className={ newClassName } description={ newDescription } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea defaultValue={ std } id={ id } name={ id } rows={ rows } ref={ inputRef } placeholder={ suggest }  onChange={ handleChange } onClick={ handleClick } onBlur = { handleOnBlur } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Description;
