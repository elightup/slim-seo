import { __, _n, sprintf } from '@wordpress/i18n';
import { scrollToText } from '../helper/text';
import { getSentences, wordsFromText, shortenParagraph } from '../helper/text';
import Base from './Base';

const Paragraphs = ( { paragraphs } ) => {
	const paragraphsLength = paragraphs.length;
	const badParagraphs = [];
	const MIN_SENTENCES = 2;
	const MAX_SENTENCES = 10;
	const MAX_WORDS = 200;
	
	paragraphs.forEach( paragraph => {
		if ( '' === paragraph ) {
			return;
		}

		const sentences = getSentences( paragraph );
		const sentencesCount = sentences.length;
		const wordsCount = wordsFromText( paragraph ).length;

		if ( sentencesCount < MIN_SENTENCES || sentencesCount > MAX_SENTENCES || wordsCount > MAX_WORDS ) {
			badParagraphs.push( {
				text: paragraph,
				short: shortenParagraph( paragraph ),
				firstSentence: sentences[0],
				sentencesCount,
				wordsCount
			} );
		}
	} );

	const badParagraphsLength = badParagraphs.length;

	const handleClick = paragraph => e => {
		e.preventDefault();

		scrollToText( paragraph.firstSentence );
	};

	return (
		<Base title={ __( 'Paragraphs', 'slim-seo' ) } sectionClassName='ss-content-analysis-paragraphs' success={ paragraphsLength > 0 && 0 === badParagraphsLength }>
			{
				paragraphsLength > 0
				? <>
					{
						badParagraphsLength > 0
						? <>
							<h4>{ sprintf( _n( '%d paragraph not good.', '%d paragraphs not good.', badParagraphsLength, 'slim-seo' ), badParagraphsLength ) }</h4>
							<ol>
							{
								badParagraphs.map( ( paragraph, index ) => {
									return <li key={ index }>
										<a href="#" onClick={ handleClick( paragraph ) }>{ paragraph.short }</a>									
										<p className="description">
											{
												paragraph.sentencesCount < MIN_SENTENCES
												? __( 'This paragraph does not have enough sentences. Please write more! ', 'slim-seo' )
												: ''
											}
											{
												paragraph.sentencesCount > MIN_SENTENCES
												? __( 'This paragraph has so many sentences. Please write less! ', 'slim-seo' )
												: ''
											}
											{
												paragraph.wordsCount > MAX_WORDS
												? __( 'This paragraph has so many words. Please write less!', 'slim-seo' )
												: ''
											}
										</p>
									</li>
								} )
							}
							</ol>
						</>
						: ''
					}
				</>
				: <p>{ __( 'No paragraph.', 'slim-seo' ) }</p>
			}
		</Base>
	);
};

export default Paragraphs;