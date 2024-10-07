import { useEffect, useState } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';
import { featuredImageChange } from '../helper/media';
import Base from './Base';
import NoAltImage from './parts/NoAltImage';
import FeaturedImage from './parts/FeaturedImage';

const Media = ( { images } ) => {
	const [ featuredImage, setFeaturedImage ] = useState( '' );

	const noAltImages = 0 === images.length ? [] : images.filter( image => {
		return '' === image.alt;
	} );

	const noAltImagesLength = noAltImages.length;

	useEffect( () => {
		featuredImageChange( setFeaturedImage );
	}, [] );

	return (
		<Base title={ __( 'Media', 'slim-seo' ) } success={ featuredImage && 0 === noAltImages.length } hiddenFieldName="good_media">
			{
				featuredImage
				? <FeaturedImage featuredImage={ featuredImage } />
				: <p className='description'>{ __( 'Featured image is not set', 'slim-seo' ) }</p>
			}

			{
				noAltImagesLength
				? <>
					<h4>{ sprintf( __( 'Images have no alt text: %d', 'slim-seo' ), noAltImagesLength ) }</h4>
					<table className='ss-content-analysis-table'>
						<thead>
							<tr>
								<th>{ __( 'Image', 'slim-seo' ) }</th>
								<th>{ __( 'Dimension (px)', 'slim-seo' ) }</th>
								<th>{ __( 'Size (KB)', 'slim-seo' ) }</th>
								<th>{ __( 'Filename', 'slim-seo' ) }</th>
							</tr>
						</thead>
						<tbody>
							{
								noAltImages.map( ( image, index ) => <NoAltImage key={ index } image={ image } /> )
							}
						</tbody>
					</table>					
				</>
				: ''
			}
		</Base>
	);
};

export default Media;