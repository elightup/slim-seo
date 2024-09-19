import { __, sprintf } from "@wordpress/i18n";
import Image from "./Image";

export default ( {
	description = '',
	...rest
} ) => {
	description = sprintf( __( 'Recommended size: 1200x630 px. Should have 1.91:1 aspect ratio with width ≥ 600 px. %s', 'slim-seo' ), description );

	return <Image label={ __( 'Facebook image', 'slim-seo' ) } description={ description } { ...rest } />;
};
