import { __ } from '@wordpress/i18n';
import rs from 'text-readability';

export const isBlockEditor = () => document.body.classList.contains( 'block-editor-page' );

export const postContentChanged = callback => {
	if ( isBlockEditor() ) {
		const editor = wp.data.select( 'core/editor' );

		callback( editor.getEditedPostContent() );
		
		wp.data.subscribe( function() {
			callback( editor.getEditedPostContent() );
		} );

		return;
	}

	jQuery( document ).on( 'tinymce-editor-init', ( event, editor ) => {
		if ( 'content' !== editor.id ) {
			return;
		}

		callback( editor.getContent() );
		
		editor.on( 'input keyup mouseenter change', e => {
			callback( editor.getContent() );
		} );
	} );

	if ( 'undefined' !== typeof EasyMDE ) {
		callback( ' ' );
	}
};

const blockEditorPostAttributeChanged = ( attr, callback ) => {
	const editor = wp.data.select( 'core/editor' );
	const value = editor.getEditedPostAttribute( attr );

	if ( value ) {
		callback( value );
	}		

	wp.data.subscribe( function() {
		const value = editor.getEditedPostAttribute( attr );

		if ( value ) {
			callback( value );
		}
	} );
};

export const postSlugChanged = callback => {
	if ( isBlockEditor() ) {
		blockEditorPostAttributeChanged( 'slug', callback );

		return;
	}

	callback( jQuery( '#post_name' ).val() );

	jQuery( document ).on( 'click', '#edit-slug-buttons .save', e => {
		callback( jQuery( '#post_name' ).val() );
	} );
}

export const postTitleChanged = callback => {
	if ( isBlockEditor() ) {
		blockEditorPostAttributeChanged( 'title', callback );

		return;
	}

	callback( jQuery( '#title' ).val() );

	jQuery( document ).on( 'input', '#title', e => {
		callback( jQuery( '#title' ).val() );
	} );
}

export const postExcerptChanged = callback => {
	if ( isBlockEditor() ) {
		blockEditorPostAttributeChanged( 'excerpt', callback );

		return;
	}

	callback( jQuery( '#excerpt' ).val() );

	jQuery( document ).on( 'input', '#excerpt', e => {
		callback( jQuery( '#excerpt' ).val() );
	} );
}

export const getFleschData = text => {
	const fleschLevels = [
		{
			min: 90,
			max: 122,
			text: __( 'very easy', 'slim-seo' )
		},
		{
			min: 80,
			max: 89,
			text: __( 'easy', 'slim-seo' )
		},
		{
			min: 70,
			max: 79,
			text: __( 'fairly easy', 'slim-seo' )
		},
		{
			min: 60,
			max: 69,
			text: __( 'standard', 'slim-seo' )
		},
		{
			min: 50,
			max: 59,
			text: __( 'fairly difficult', 'slim-seo' )
		},
		{
			min: 30,
			max: 49,
			text: __( 'difficult', 'slim-seo' )
		},
		{
			min: 0,
			max: 29,
			text: __( 'very confusing', 'slim-seo' )
		}
	];
	const fleschScore = rs.fleschReadingEase( text );
	const fleschLevel = fleschLevels.find( lv => lv.max >= fleschScore && lv.min <= fleschScore );

	return {
		fleschScore,
		fleschLevel
	};
};

export const fetcher = ( apiName, parameters = {}, method = 'GET' ) => {
	let options = {
		method,
		headers: { 'X-WP-Nonce': SSContentAnalysis.nonce, 'Content-Type': 'application/json' },
	};
	let url = `${ SSContentAnalysis.rest }/slim-seo-content-analysis/${ apiName }`;

	if ( 'POST' === method ) {
		options.body = JSON.stringify( parameters );
	} else {
		const query = ( new URLSearchParams( parameters ) ).toString();

		if ( query ) {
			url += SSContentAnalysis.rest.includes( '?' ) ? `&${ query }` : `?${ query }`;
		}
	}

	return fetch( url, options ).then( response => response.json() );
};