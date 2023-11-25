import { useSelect } from '@wordpress/data';
import {
    FormTokenField,
    PanelBody,
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
    console.log(themeColors);

    const onChangeAttribute = (key, value) => {
        if (key == 'active_color') {
            const color = themeColors.find((element) => element.color == value);
            value = color;
        }

        var data = {};
        data[key] = value;
        console.log(`onChangeAttribute ${key}=${value}`);
        setAttributes(data);
    };

    const queryArgs = {
        'per_page': -1
    };

    const tags = useSelect((select) => {
        return select('core').getEntityRecords('taxonomy', 'post_tag', queryArgs);
    }, []) || [];

    const selectedTags = attributes.tags || [];

    const resetAll = () => {

    };

    const active_color = typeof attributes.active_color == 'object' ? attributes.active_color.color : undefined;

    return (
        <div {...useBlockProps()}>
            {
                <InspectorControls>
                    <PanelBody title='Settings'>
                        <FormTokenField
                            label="Tags"
                            onChange={(value) => onChangeAttribute('tags', value)}
                            suggestions={tags.map((e) => e.name)}
                            value={attributes.tags || []}
                        />
                    </PanelBody>
                    <ToolsPanel label={__('Active color')} resetAll={resetAll}>
                        <ColorPalette
                            colors={themeColors}
                            value={active_color}
                            onChange={(color) => onChangeAttribute('active_color', color)}
                        />
                    </ToolsPanel>
                </InspectorControls>
            }
            <p>Tags</p>
            <nav>
                <ul>
                    {
                        selectedTags.map((e) => <li key={e}>{e}</li>)
                    }
                </ul>
            </nav>
        </div>
    );
};