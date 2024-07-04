import { Control } from "@elightup/form";

const Text = ( property ) => {
	const { id, label, std, className= '', ...rest } = property;

	return (
		<Control className={ className } label={ label } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<input type="text" id={ id } name={ id } defaultValue={ std } />
			</div>
		</Control>
	);
};

export default Text;
