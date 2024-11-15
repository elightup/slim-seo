import { link } from './link';

const { registerFormatType, unregisterFormatType } = wp.richText;

function registerFormats() {
	[ link ].forEach( ( { name, ...settings } ) => {
		if ( name ) {
			unregisterFormatType('core/link');
			registerFormatType( name, settings );
		}
	} );
}

registerFormats();