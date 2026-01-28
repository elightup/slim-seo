import { Control } from "@elightup/form";
import { useEffect, useRef, useState } from "@wordpress/element";
import { Button, Spinner } from '@wordpress/components';
import { __, sprintf } from "@wordpress/i18n";
import { request, generateMetaWithAI } from "../../functions";
import PropInserter from "./PropInserter";

const wpTitle = document.querySelector( '#name' );
const getTitle = () => wpTitle.value;
const wpDescription = document.querySelector( '#description' );
const getDescription = () => wpDescription.value;

export default ( { id, std = '', features, max = 60, ...rest } ) => {
	let [ value, setValue ] = useState( std );
	let [ preview, setPreview ] = useState( '' );
	let [ placeholder, setPlaceholder ] = useState( '' );
	let [ updateCount, setUpdateCount ] = useState( 0 );
	let [ updateByAICount, setUpdateByAICount ] = useState( 0 );
	let [ previousMetaByAI, setPreviousMetaByAI ] = useState( '' );
	const [ isGenerating, setIsGenerating ] = useState( false );
	const titleRef = useRef( getTitle() );
	const inputRef = useRef();

	const requestUpdate = () => setUpdateCount( prev => prev + 1 );

	const handleChange = e => {
		setValue( e.target.value );
		requestUpdate();
	};

	const handleFocus = () => setValue( prev => prev || placeholder );
	const handleBlur = () => setValue( prev => prev === placeholder ? '' : prev );

	const handleInsertVariables = variable => {
		setValue( prev => {
			// Insert variable at cursor.
			const cursorPosition = inputRef.current.selectionStart;
			return prev.slice( 0, cursorPosition ) + variable + prev.slice( cursorPosition );
		} );
		requestUpdate();
	};

	const refreshPreviewAndPlaceholder = () => {
		request( 'meta-tags/preview/term-title', { ID: ss.id, text: value, title: titleRef.current } ).then( response => {
			setPreview( response.preview );
			setPlaceholder( response.default );
		} );
	};

	const handleTitleChange = () => {
		const title = getTitle();
		if ( titleRef.current === title ) {
			return;
		}
		titleRef.current = title;
		requestUpdate();
	};

	const onGenerateWithAI = () => {
		setUpdateByAICount( prev => {
			const next = prev + 1;

			generateMetaWithAI( {
				type: 'term-title',
				title: titleRef.current,
				content: getDescription(),
				updateCount: next,
				previousMetaByAI,
				setValue,
				setPreview,
				setPreviousMetaByAI,
				setIsGenerating,
			} );

			return next;
		} );
	};

	// Trigger refresh preview and placeholder when anything change.
	// Use debounce technique to avoid sending too many requests.
	useEffect( () => {
		const timer = setTimeout( refreshPreviewAndPlaceholder, 1000 );
		return () => clearTimeout( timer );
	}, [ updateCount ] );

	// Listen for post title changes.
	useEffect( () => {
		wpTitle.addEventListener( 'input', handleTitleChange );

		return () => {
			wpTitle.removeEventListener( 'input', handleTitleChange );
		};
	}, [] );

	const getClassName = () => preview.length > max ? 'ss-input-warning' : 'ss-input-success';
	const getDescription = () => sprintf( __( 'Character count: %s. Recommended length: ≤ 60 characters.', 'slim-seo' ), preview.length );

	return (
		<Control className={ getClassName() } description={ getDescription() } id={ id } label={ __( 'Meta title', 'slim-seo' ) } { ...rest }>
			<div className="ss-input-wrapper">
				<input
					type="text"
					id={ id }
					name={ id }
					value={ value }
					placeholder={ isGenerating ? __( 'Generating with AI...', 'slim-seo' ) : placeholder }
					onChange={ handleChange }
					onFocus={ handleFocus }
					onBlur={ handleBlur }
					ref={ inputRef }
				/>
				{ preview && <div className="ss-preview">{ sprintf( __( 'Preview: %s', 'slim-seo' ), preview ) }</div> }
				{ features.openai &&
					<Button className={ `ss-ai ss-select-image ss-select-textarea ss-inserter ${ isGenerating ? 'is-generating' : '' } ` } onClick={ onGenerateWithAI } label={ __( 'Generate with AI', 'slim-seo' ) } showTooltip={ true }	disabled={ isGenerating } >
						<span class="dashicons dashicons-superhero ss-ai-icon"></span>
						{ isGenerating && <Spinner /> }
					</Button>
				}
				<PropInserter onInsert={ handleInsertVariables } />
			</div>
		</Control>
	);
};
