import { Control } from "@elightup/form";
import { Button } from "@wordpress/components";

const Image = ( property ) => {
	const { id, label, std, className = '', mediaPopupTitle, ...rest } = property;

	return (
		<Control className={ className } label={ label } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } />
				<Button icon="format-image" className="ss-insert-image" />
			</div>
		</Control>
	);
};

export default Image;
