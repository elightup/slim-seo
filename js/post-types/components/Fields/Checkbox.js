import { Control } from "@elightup/form";

const Checkbox = ( property ) => {
	const { id, label, std, className= '', ...rest } = property;

	return (
		<Control className={ className } label={ label } id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<label className="ss-toggle">
					<input type="checkbox" id={ id } name={ id } defaultChecked={ std } value={ true } />
					<div className="ss-toggle__switch"></div>
				</label>
			</div>
		</Control>
	);
};
export default Checkbox;