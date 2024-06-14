import { RawHTML } from "@wordpress/element";
import Tooltip from "./Tooltip";

const DivRow = ( {
	children,
	label,
	description,
	tooltip,
	className = '',
	htmlFor = '',
	keyValue = '',
	required = false,
	error,
} ) => {
	return (
		<div className={ `ss-field ${ className }` } key={ keyValue }>
			{
				label &&
				<label className="ss-label" htmlFor={ htmlFor }>
					<RawHTML>{ label }</RawHTML>
					{ tooltip && <Tooltip id={ htmlFor } content={ tooltip } /> }
				</label>
			}
			<div className="ss-input">
				{ children }
				{ description && <RawHTML className="ss-description">{ description }</RawHTML> }
				{ error && <p className="og-error">{ error }</p> }
			</div>
		</div>
	);
};

export default DivRow;