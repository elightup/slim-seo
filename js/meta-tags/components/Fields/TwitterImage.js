import { __, sprintf } from "@wordpress/i18n";
import Image from "./Image";

export default ( {
	description = '',
	...rest
} ) => {
	description = sprintf( __( 'Recommended size: 1200x600 px. Should have 2:1 aspect ratio with width ≥ 300 px and ≤ 4096 px. %s', 'slim-seo' ), description );

	return <Image label={ __( 'X image', 'slim-seo' ) } description={ description } { ...rest } />;
};
