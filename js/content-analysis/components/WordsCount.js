import { __, sprintf } from '@wordpress/i18n';
import { wordsFromText } from '../helper/text';
import Base from './Base';

const WordsCount = ( { words, images } ) => {
	let wordsCount = words.length;

	if ( images.length ) {
		images.forEach( image => {
			wordsCount += wordsFromText( image.alt ).length;
		} );
	}
	
	return (
		<Base title={ sprintf( __( 'Words count: %d', 'slim-seo' ), wordsCount ) } success={ wordsCount >= 500 } hiddenFieldName="good_words_count">
			{
				wordsCount > 0
				? ''
				: <p>{ __( 'No content.', 'slim-seo' ) }</p>
			}
			<p className="description">{ __( 'Good content should have more than 500 words.', 'slim-seo' ) }</p>
		</Base>
	);
};

export default WordsCount;