import DivRow from './DivRow';
import { useToggle } from '../hooks/useToggle';

const Checkbox = ( property ) => {
	const { id, label, std, className= '', ...rest } = property;
	const toggle = useToggle( id );

	return <DivRow label={ label } className={ `ss-field--checkbox ${ className }` } htmlFor={ id } { ...rest }>
		<label className="ss-toggle">
			<input type="checkbox" id={ id } name={ id } onChange={ toggle } defaultChecked={ std } value={ true } />
			<div className="ss-toggle__switch"></div>
		</label>
	</DivRow>;
};
export default Checkbox;