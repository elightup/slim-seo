import { Control } from "@elightup/form";

const Textarea = ( property ) => {
	const { id, label, std, className = '', rows = 2, ...rest } = property;

	return (
		<Control className={ className } label={ label } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<textarea defaultValue={ std } id={ id } name={ id } rows={ rows } />
			</div>
		</Control>
	);
};

export default Textarea;
