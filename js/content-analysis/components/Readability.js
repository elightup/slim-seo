import { __, sprintf } from '@wordpress/i18n';
import { getFleschData } from '../helper/misc';
import Base from './Base';

const Readability = ( { rawContent } ) => {
	const fleschData = getFleschData( rawContent );

	return (
		<Base title={ __( 'Readability', 'slim-seo' ) } success={ fleschData.fleschScore >= 60 }>
			{
				fleschData.fleschLevel
				? <p>{ sprintf( '%s %s %s', __( 'Content is', 'slim-seo' ), fleschData.fleschLevel.text, __( 'to read', 'slim-seo' ) ) }</p>
				: ''
			}
		</Base>
	);
};

export default Readability;