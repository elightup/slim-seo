import { useEffect, useState } from '@wordpress/element';
import { FormTokenField } from '@wordpress/components';
import { __, _n, sprintf } from '@wordpress/i18n';
import { postTitleChanged, postExcerptChanged } from '../helper/misc';
import { getHeadings, wordsFromText, countWordsFromText } from '../helper/text';
import Base from './Base';
import WarningIcon from './parts/WarningIcon';

const Keywords = ( { postContent, rawContent, paragraphs, words, images, slug } ) => {
	const [ postTitle, setPostTitle ] = useState( '' );
	const [ postExcerpt, setPostExcerpt ] = useState( '' );
	const [ mainKeyword, setMainKeyword ] = useState( SSContentAnalysis.mainKeyword );
	const [ keywords, setKeywords ] = useState( SSContentAnalysis.keywords ? SSContentAnalysis.keywords.split( ';' ) : [] );
	const keywordsList = [];
	const headings = getHeadings( postContent );
	const wordsCount = words.length;
	const MIN_DENSITY = 0.5;
	const MAX_DENSITY = 3;
	let success = true;

	keywords.forEach( keyword => {
		keyword = keyword.trim().toLowerCase();

		if ( '' === keyword ) {
			return;
		}

		const criteria = {};
		const keywordLength = wordsFromText( keyword ).length;
		const count = countWordsFromText( keyword, rawContent );
		const density = count > 0 ? ( wordsCount >= 100 ? ( keywordLength * count / wordsCount ) * 100 : 1 ) : 0;

		criteria.density = {
			density: density,
			count: count,
			good: density >= MIN_DENSITY && density <= MAX_DENSITY,
			tooltip: __( 'Density should be in 0.5% to 3%', 'slim-seo' )
		};

		const appearInTitle = countWordsFromText( keyword, postTitle ) > 0;

		criteria.title = {
			good: appearInTitle,
			tooltip: appearInTitle ? __( 'Keyword is in the title', 'slim-seo' ) : __( 'Keyword is not in the title', 'slim-seo' )
		}

		const appearInSlug = countWordsFromText( keyword, slug ) > 0;

		criteria.slug = {
			good: appearInSlug,
			tooltip: appearInSlug ? __( 'Keyword is in the slug', 'slim-seo' ) : __( 'Keyword is not in the slug', 'slim-seo' )
		}

		const appearInExcerpt = countWordsFromText( keyword, postExcerpt ) > 0;

		criteria.excerpt = {
			good: appearInExcerpt,
			tooltip: appearInExcerpt ? __( 'Keyword is in the excerpt', 'slim-seo' ) : __( 'Keyword is not in the excerpt', 'slim-seo' )
		}

		const appearInFirstParagraph = paragraphs.length > 0 && countWordsFromText( keyword, paragraphs[0] ) > 0;

		criteria.firstParagraph = {
			good: appearInFirstParagraph,
			tooltip: appearInFirstParagraph ? __( 'Keyword is in the first paragraph', 'slim-seo' ) : __( 'Keyword is not in the first paragraph', 'slim-seo' )
		}

		const appearInHeadings = headings.length > 0 && headings.filter( heading => countWordsFromText( keyword, heading ) > 0 ).length > 0;

		criteria.headings = {
			good: appearInHeadings,
			tooltip: appearInHeadings ? __( 'Keyword is in the headings', 'slim-seo' ) : __( 'Keyword is not in the headings', 'slim-seo' )
		}

		const imagesLength = images.length;

		if ( imagesLength > 0 ) {
			const imagesAltCount = images.filter( image => countWordsFromText( keyword, image.alt ) > 0 ).length;

			if ( 0 === imagesAltCount ) {
				criteria.imageAlt = {
					good: false,
					tooltip: __( 'Keyword is not in image alt', 'slim-seo' )
				}
			} else if ( imagesLength > 1 && imagesLength === imagesAltCount ) {
				criteria.imageAlt = {
					good: false,
					tooltip: sprintf( __( 'Keyword is in %d/%d images alt', 'slim-seo' ), imagesAltCount, imagesLength )
				}
			} else {
				criteria.imageAlt = {
					good: true,
					tooltip: sprintf( __( 'Keyword is in %d/%d images alt', 'slim-seo' ), imagesAltCount, imagesLength )
				}
			}
		}

		for ( const [ criteriaKey, criteriaValue ] of Object.entries( criteria ) ) {
			if ( keyword === mainKeyword ) {
				if ( 'imageAlt' !== criteriaKey ) {
					success = success && criteriaValue.good;
				}
			} else {
				if ( 'density' === criteriaKey ) {
					success = success && criteriaValue.good;
				}
			}
		}

		keywordsList.push( {
			text: keyword,
			criteria
		} );
	} );

	success = success && keywordsList.length > 0;

	const handleKeywordsChange = newKeywords => {
		setKeywords( newKeywords );
	};

	const changeMainKeyword = kw => e => {
		setMainKeyword( prev => kw );
	};

	useEffect( () => {
		postTitleChanged( setPostTitle );
		postExcerptChanged( setPostExcerpt );
	}, [] );

	return (
		<Base title={ __( 'Keywords', 'slim-seo' ) } defaultOpen={ true } success={ success } hiddenFieldName="good_keywords">
			<FormTokenField
				value={ keywords }
				onChange={ handleKeywordsChange }
				__experimentalShowHowTo={ false }
				tokenizeOnSpace={ true } />

			<input type="hidden" name="slim_seo[content_analysis][keywords]" value={ keywords.join( ';' ) } />

			<p className='description'>{ __( 'Separate keyword by Enter', 'slim-seo' ) }</p>

			{
				keywordsList.length
				? <table className='ss-content-analysis-table'>
					<thead>
						<tr>
							<th>{ __( 'Keyword', 'slim-seo' ) }</th>
							<th>{ __( 'Density', 'slim-seo' ) }</th>
							<th>{ __( 'Title', 'slim-seo' ) }</th>
							<th>{ __( 'Slug', 'slim-seo' ) }</th>
							<th>{ __( 'Description', 'slim-seo' ) }</th>
							<th>{ __( 'First paragraph', 'slim-seo' ) }</th>
							<th>{ __( 'Headings', 'slim-seo' ) }</th>
							<th>{ __( 'Image alt', 'slim-seo' ) }</th>
							<th>{ __( 'Main keyword', 'slim-seo' ) }</th>
						</tr>
					</thead>
					<tbody>
						{
							keywordsList.map( ( keyword, index ) => {
								return (
									<tr key={ index }>
										<td><strong>{ keyword.text }</strong></td>
										<td><WarningIcon args={ keyword.criteria.density }>{ keyword.criteria.density.density.toFixed( 2 ) }% - { sprintf( _n( '%d time', '%d times', keyword.criteria.density.count, 'slim-seo' ), keyword.criteria.density.count ) }</WarningIcon></td>
										<td><WarningIcon args={ keyword.criteria.title } /></td>
										<td><WarningIcon args={ keyword.criteria.slug } /></td>
										<td><WarningIcon args={ keyword.criteria.excerpt } /></td>
										<td><WarningIcon args={ keyword.criteria.firstParagraph } /></td>
										<td><WarningIcon args={ keyword.criteria.headings } /></td>
										<td>{ keyword.criteria.hasOwnProperty( 'imageAlt' ) ? <WarningIcon args={ keyword.criteria.imageAlt } /> : '' }</td>
										<td>
											<label className='ss-toggle'>
												<input type='checkbox' value='1' checked={ keyword.text === mainKeyword } onChange={ changeMainKeyword( keyword.text ) } />
												<div className='ss-toggle__switch'></div>
											</label>
											<input type="hidden" name="slim_seo[content_analysis][main_keyword]" value={ mainKeyword } />
										</td>
									</tr>
								);
							} )
						}
					</tbody>
				</table>
				: ''
			}
		</Base>
	);
};

export default Keywords;