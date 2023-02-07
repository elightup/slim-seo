// https://github.com/WordPress/gutenberg/blob/trunk/packages/format-library/src/index.js

import { dispatch } from '@wordpress/data';
import { registerFormatType } from '@wordpress/rich-text';
import { link } from './link';

const { name, ...settings } = link;
dispatch( 'core/rich-text' ).removeFormatTypes( name );
registerFormatType( name, settings );