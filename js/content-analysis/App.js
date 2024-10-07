import { render, useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { postContentChanged, postSlugChanged } from './helper/misc';
import { rawText, wordsFromText, getParagraphs } from './helper/text';
import { getImages } from './helper/media';
import Keywords from './components/Keywords';
import Media from './components/Media';
import WordsCount from './components/WordsCount';
import LinksInternal from './components/LinksInternal';
import Slug from './components/Slug';
import Readability from './components/Readability';
import Paragraphs from './components/Paragraphs';

const App = () => {
	const [ postContent, setPostContent ] = useState( '' );
	const [ slug, setSlug ] = useState( '' );
	const rawContent = rawText( postContent );
	const words = wordsFromText( rawContent );
	const paragraphs = getParagraphs( postContent );
	const images = getImages( postContent );

	useEffect( () => {
		postContentChanged( setPostContent );
		postSlugChanged( setSlug );
	}, [] );

	return (
		<>
			<Keywords
				postContent={ postContent }
				rawContent={ rawContent }
				paragraphs={ paragraphs }
				words={ words }
				images={ images }
				slug={ slug } />
			<Media images={ images } />
			<WordsCount words={ words } images={ images } />
			<LinksInternal postContent={ postContent } />
			<Slug slug={ slug } />
			<Readability rawContent={ rawContent } />
			<Paragraphs paragraphs={ paragraphs } />
		</>
	);
};

render( <App />, document.getElementById( 'content-analysis' ) );