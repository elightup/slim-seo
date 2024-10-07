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