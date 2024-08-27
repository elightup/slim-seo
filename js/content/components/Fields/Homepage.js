import Block from "./Block";

export default Homepage = ( { option } ) => {
	const optionPlaceholder = {
		title: ssContent.homepage.title,
		description:  ssContent.homepage.description,
	};

	const handleFocus = e => e.target.value = e.target.value || e.target.placeholder;
	const handleBlur = e => e.target.value = e.target.value === e.target.placeholder ? '' : e.target.value;

	return (
		<Block
			type="home"
			label="Homepage"
			baseName="slim_seo[home]"
			option={ option || [] }
			optionPlaceholder={ optionPlaceholder }
			onFocus={ handleFocus }
			onBlur={ handleBlur }
		/>
	);
};
