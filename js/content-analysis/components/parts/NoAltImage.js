import { useEffect, useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { scrollToImage, getImageDetail, checkFilename } from '../../helper/media';
import WarningIcon from '../parts/WarningIcon';

const NoAltImage = ( { image } ) => {
	const [ imageDetail, setImageDetail ] = useState( [] );
	const filenameGood = checkFilename( imageDetail?.filename );

	const handleClick = imageSrc => e => {
		e.preventDefault();

		scrollToImage( imageSrc );
	};

	useEffect( () => {
		const fetchImageDetail = async () => {
			const imgDetail = await getImageDetail( image );

			setImageDetail( imgDetail );
		};

		fetchImageDetail();
	}, [ image ] );

	return (
		<tr onClick={ handleClick( image.src ) } className="ss-content-analysis-no-alt-image">
			<td><img src={ image.src } /></td>
			<td>{ imageDetail?.width }x{ imageDetail?.height }</td>
			<td>{ imageDetail?.size }</td>
			<td><WarningIcon args={ { good: filenameGood, tooltip: __( '..', 'slim-seo' ) } } /> { imageDetail?.filename }</td>
		</tr>										
	)
};

export default NoAltImage;