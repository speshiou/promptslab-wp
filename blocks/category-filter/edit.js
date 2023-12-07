import { useSelect } from '@wordpress/data';
import {
    ColorPalette,
    __experimentalToolsPanel as ToolsPanel,
} from '@wordpress/components';

import {
    InspectorControls,
    useBlockProps,
} from '@wordpress/block-editor';

import { __ } from '@wordpress/i18n';

export default ({ attributes, setAttributes }) => {
    const settings = wp.data.select('core/block-editor').getSettings();
    const themeColors = settings.colors;

    const onChangeAttribute = (key, value) => {
        if (key == 'active_color') {
            const color = themeColors.find((element) => element.color == value);
            value = color;
        }

        var data = {};
        data[key] = value;
        setAttributes(data);
    };

    const resetAll = () => {

    };

    const active_color = typeof attributes.active_color == 'object' ? attributes.active_color.color : undefined;

    return (
        <div {...useBlockProps()}>
            {
                <InspectorControls>
                    <ToolsPanel label={__('Active color')} resetAll={resetAll}>
                        <ColorPalette
                            colors={themeColors}
                            value={active_color}
                            onChange={(color) => onChangeAttribute('active_color', color)}
                        />
                    </ToolsPanel>
                </InspectorControls>
            }
            <p>Category Filter</p>
        </div>
    );
};