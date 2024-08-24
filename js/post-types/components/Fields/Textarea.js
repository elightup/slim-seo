import { Control } from "@elightup/form";
import { useRef } from "@wordpress/element";
import PropInserter from "./PropInserter";

const Textarea = ( { id, std, placeholder, rows = 3, onFocus, onBlur, ...rest } ) => {
	const inputRef = useRef();

	return (
		<Control id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea defaultValue={ std } id={ id } name={ id } rows={ rows } ref={ inputRef } onFocus={ onFocus } onBlur={ onBlur } placeholder={ placeholder } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Textarea;
