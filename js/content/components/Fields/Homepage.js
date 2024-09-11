import { __ } from "@wordpress/i18n";
import Block from "./Block";

export default ( { option } ) => {
	const optionPlaceholder = {
		title: ss.homepage.title,
		description: ss.homepage.description,
	};

	return (
		<Block
			label="Homepage"
			baseName="slim_seo[home]"
			option={ option || [] }
			optionPlaceholder={ optionPlaceholder }
			descriptionInstruction={ __( 'Recommended length: 50-160 characters.', 'slim-seo' ) }
		/>
	);
};
