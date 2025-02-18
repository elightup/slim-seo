import { __ } from '@wordpress/i18n';
import Base from './Base';

const Slug = ( { slug } ) => {
	const slugLength = slug ? slug.split( '-' ).length : 0;

	return (
		<Base title={ __( 'Slug', 'slim-seo' ) } success={ slugLength <= 5 }>
			<p className="description">{ __( 'Slug length should have less than 5 words.', 'slim-seo' ) }</p>
		</Base>
	);
};

export default Slug;