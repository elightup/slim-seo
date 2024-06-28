import DivRow from './DivRow';

const Checkbox = ( property ) => {
	const { id, label, std, className= '', ...rest } = property;

	return <DivRow label={ label } className={ `ss-field--checkbox ${ className }` } htmlFor={ id } { ...rest }>
		<label className="ss-toggle">
			<input type="checkbox" id={ id } name={ id } defaultChecked={ std } value={ true } />
			<div className="ss-toggle__switch"></div>
		</label>
	</DivRow>;
};
export default Checkbox;