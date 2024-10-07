import { __ } from "@wordpress/i18n";
import Image from "./Image";

export default ( {
	description = __( 'Recommended size: 1200x600 px. Should have 2:1 aspect ratio with width ≥ 300 px and ≤ 4096 px. Leave empty to use the Facebook image.', 'slim-seo' ),
	...rest
} ) => <Image label={ __( 'Twitter image', 'slim-seo' ) } description={ description } { ...rest } />;
