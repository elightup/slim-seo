import { Control } from "@elightup/form";
import { memo, useRef } from "@wordpress/element";
import PropInserter from "./PropInserter";

const Textarea = ( property ) => {
	const inputRef = useRef();
	const { id, label, std, className = '', rows = 2 } = property;

	return (
		<Control className={ className } label={ label } id={ id }>
			<div className="ss-input-wrapper">
				<textarea defaultValue={ std } id={ id } name={ id } rows={ rows } ref={ inputRef } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Textarea;
