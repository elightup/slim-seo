import Block from "./Block";

export default Homepage = ( { option } ) => {
	const optionPlaceholder = {
		title: ss.homepage.title,
		description:  ss.homepage.description,
	};

	return (
		<Block
			type="home"
			label="Homepage"
			baseName="slim_seo[home]"
			option={ option || [] }
			optionPlaceholder={ optionPlaceholder }
		/>
	);
};
