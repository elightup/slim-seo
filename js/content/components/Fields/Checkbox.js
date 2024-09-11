import { Control } from "@elightup/form";

const Checkbox = ( { id, std, onChange, ...rest } ) => {
	return (
		<Control id={ id } { ...rest }>
			<div className="ss-input-wrapper">
				<label className="ss-toggle">
					<input type="checkbox" id={ id } name={ id } defaultChecked={ std } value={ true } onChange={ onChange } />
					<div className="ss-toggle__switch"></div>
				</label>
			</div>
		</Control>
	);
};
export default Checkbox;