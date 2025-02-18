import { isBlockEditor } from './misc';

export const rawText = text => {
	const rawText = jQuery( '<div>' + text + '</div>' ).find( 'script, style' ).remove().end().text().trim();

	return rawText;
};

export const wordsFromText = text => {
	const words = text.match( /\S+/g );

	return Array.isArray( words ) ? words : [];
};

export const getParagraphs = text => {
	const regex = /<p\b[^>]*>(.*?)<\/p>/gi;
    
	let paragraphs = [];
    let match;

    while ( null !== ( match = regex.exec( text ) ) ) {
        paragraphs.push( match[0] );
    }

	if ( 0 === paragraphs.length ) {
		return [];
	}

	const paragraphsRawText = paragraphs.map( paragraph => rawText( paragraph ) );

	return paragraphsRawText.filter( paragraph => '' !== paragraph.trim() );
};

export const getSentences = text => {
	text = text.replace( /\n/g, ' ' );

	return text.split( /(?<=[.!?])\s+(?=[A-Z])/ ).filter( Boolean );
};

export const linksFromText = text => {
	const regex = /<a\s+(?:[^>]*?\s+)?href="([^"]*)"/gi;
	const matches = [ ...text.matchAll( regex ) ];
	const links = matches.map( match => match[1] );
	
	return links;
};

export const countWordsFromText = ( word, text ) => {
	return text.trim().toLowerCase().split( word ).length - 1;
};

export const getHeadings = text => {
	const headings = text.match( /<h[1-6][^>]*>(.*?)<\/h[1-6]>/g );
	
	if ( null === headings || 0 === headings.length ) {
		return [];
	}

	return headings.map( heading => rawText( heading ) );
};

export const shortenParagraph = ( text, maxLength = 100 ) => {
	if ( text.length > maxLength ) {
		return text.substring( 0, maxLength ) + '...';
	}

	return text;
}

export const scrollToText = text => {
	text = text.trim();

	// Classic Editor
	if ( !isBlockEditor() ) {
		const iframe = document.querySelector( '#content_ifr' );
		const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;

		if ( !iframeDocument ) {
			return;
		}

		const elements = iframeDocument.querySelectorAll( 'h1, h2, h3, h4, h5, h6, span, p, li' );
		const targetElement = Array.from( elements ).find( element => element.textContent.includes( text ) );

		if ( !targetElement ) {
			return;
		}

		targetElement.scrollIntoView( {
			behavior: 'smooth',
			block: 'center',
		} );

		return;
	}

	// Block Editor in WP < 6.7
	const $ = jQuery;
	const blockEditorContainer = $( '.block-editor-block-list__layout.is-root-container' );

	if ( blockEditorContainer.length ) {
		const windowHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
		const content = blockEditorContainer.contents();
		let element = content.find( '*:contains("' + text + '"):last' );
		let found = false;

		// If we couldn't find the block with the first check, Go over the elements a different way to try and find the block
		if ( element.length < 1 ) {
			content.each( function ( index, block ) {
				const sentenceCheck = $( block ).text().includes( text );

				if ( !found && false !== sentenceCheck ) {
					element = $( block );
					found = true;
				}
			}
			);
		}

		if ( element.length ) {
			let scrollPoint = $( '.edit-post-visual-editor' ).offset().top - $( element[ 0 ] ).offset().top;

			scrollPoint = scrollPoint - 61 + ( windowHeight / 2 );

			$( '.interface-interface-skeleton__content' ).animate( {
				scrollTop: Math.abs( parseInt( scrollPoint ) )
			}, 1000 );
		}

		return;
	}
};