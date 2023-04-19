import { useSelect } from '@wordpress/data';
import { 
    FormTokenField,
    PanelBody,
} from '@wordpress/components';

import {
    InspectorControls,
    useBlockProps,
} from '@wordpress/block-editor';

export default ( { attributes, setAttributes } ) => {
    const onChangeAttribute = ( key, value ) => {
        var data = {};
        data[key] = value;
        setAttributes( data );
    };

    const tags = useSelect( ( select ) => {
        return select( 'core' ).getEntityRecords( 'taxonomy', 'post_tag' );
    }, [] ) || [];

    const selectedTags = attributes.tags || [];

    return (
        <div { ...useBlockProps() }>
            {
                <InspectorControls>
                    <PanelBody title='Settings'>
                        <FormTokenField
                            label="Tags"
                            onChange={ (value) => onChangeAttribute('tags', value) }
                            suggestions={tags.map((e) => e.name)}
                            value={attributes.tags || []}
                        />
                    </PanelBody>
                </InspectorControls>
            }
            <p>Tags</p>
            <nav>
                <ul>
                    {
                        selectedTags.map((e) => <li>{e}</li>)
                    }
                </ul>
            </nav>
        </div>
    );
};