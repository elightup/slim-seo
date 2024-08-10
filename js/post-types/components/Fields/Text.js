import { Control } from "@elightup/form";
import { useRef } from "@wordpress/element";
import PropInserter from "./PropInserter";

const Text = ( { id, label, std, className= '', ...rest } ) => {
	const inputRef = useRef();

	return (
		<Control className={ className } label={ label } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } ref={ inputRef } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Text;
