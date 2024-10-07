import { Tooltip } from '@wordpress/components';

const WarningIcon = ( { args, children } ) => (
	<>
		<Tooltip text={ args.tooltip }>
			<span className={ args.good ? 'ss-success' : 'ss-warning' }></span>
		</Tooltip> { children }
	</>
);

export default WarningIcon;