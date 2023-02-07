// https://github.com/WordPress/gutenberg/blob/trunk/packages/format-library/src/index.js

import { registerFormatType, unregisterFormatType } from '@wordpress/rich-text';
import { link } from './link';

const { name, ...settings } = link;
unregisterFormatType( name );
registerFormatType( name, settings );