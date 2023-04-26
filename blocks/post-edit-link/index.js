import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import edit from './edit';

const settings = {
    ...metadata,
	edit: edit,
};

registerBlockType(settings.name, settings);