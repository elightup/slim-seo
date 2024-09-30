import Description from "./Description";
import FacebookImage from "./FacebookImage";
import Title from "./Title";
import TwitterImage from "./TwitterImage";

export default ( {
	baseName,
	option,
	defaultMetaData = [],
	label,
	descriptionInstruction = '',
	facebookImageInstruction = '',
} ) => (
	<>
		<h3>{ label }</h3>
		<Title
			id={ `${ baseName }[title]` }
			std={ option.title || '' }
			placeholder={ defaultMetaData.title || '' }
		/>
		<Description
			id={ `${ baseName }[description]` }
			std={ option.description || '' }
			placeholder={ defaultMetaData.description || '' }
			description={ descriptionInstruction }
		/>
		<FacebookImage
			id={ `${ baseName }[facebook_image]` }
			std={ option.facebook_image || '' }
			description={ facebookImageInstruction }
		/>
		<TwitterImage
			id={ `${ baseName }[twitter_image]` }
			std={ option.twitter_image || '' }
		/>
	</>
);