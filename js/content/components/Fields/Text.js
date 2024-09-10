import { Control } from "@elightup/form";
import { useRef } from "@wordpress/element";
import PropInserter from "./PropInserter";

const Text = ( { id, std, placeholder, onFocus, onBlur, ...rest } ) => {
	const inputRef = useRef();

	return (
		<Control id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } ref={ inputRef } onFocus={ onFocus } onBlur={ onBlur } placeholder={ placeholder } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Text;
