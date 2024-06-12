import { Control } from "@elightup/form";
import { memo, useRef } from "@wordpress/element";
import PropInserter from "./PropInserter";

const Text = ( property ) => {
console.log( 'property ', property );
	const inputRef = useRef();
	const { id, label, std, className= '' } = property;

	return (
		<Control className={ className } { ...property }>
			<div className="sss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } ref={ inputRef } />
				<PropInserter inputRef={ inputRef } />
			</div>
		</Control>
	);
};

export default Text;
