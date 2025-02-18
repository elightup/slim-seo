import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { checkFilename, getImageDetail } from '../../helper/media';
import WarningIcon from '../parts/WarningIcon';

const FeaturedImage = ( { featuredImage } ) => {
	const [ featuredImageDetail, setFeaturedImageDetail ] = useState( [] );
	const filenameGood = checkFilename( featuredImageDetail?.filename );

	useEffect( () => {
		const fetchFeaturedImageDetail = async () => {
			const imgDetail = await getImageDetail( { id: featuredImage, src: '' } );

			setFeaturedImageDetail( imgDetail );
		};

		fetchFeaturedImageDetail();
	}, [ featuredImage ] );

	return (
		<>
			<h4>{ __( 'Featured image', 'slim-seo' ) }</h4>
			<p>{ __( 'Dimension (px)', 'slim-seo' ) }: { featuredImageDetail?.width }x{ featuredImageDetail?.height }</p>
			<p>{ __( 'Size (KB)', 'slim-seo' ) }: { featuredImageDetail?.size }</p>
			<p><WarningIcon args={ { good: filenameGood, tooltip: __( '..', 'slim-seo' ) } } /> { __( 'Filename', 'slim-seo' ) }: { featuredImageDetail?.filename }</p>
		</>
	);
};

export default FeaturedImage;