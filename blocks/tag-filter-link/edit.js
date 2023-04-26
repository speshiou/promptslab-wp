import { useSelect } from '@wordpress/data';
import { 
    SelectControl,
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

    const queryArgs = {
        'per_page': -1
    };

    const categories = useSelect( ( select ) => {
        return select( 'core' ).getEntityRecords( 'taxonomy', 'category', queryArgs );
    }, [] ) || [];

    const categoryOptions = categories.map((e) => {
        return {
            'label': e.name,
            'value': e.slug,
        }
    });

    categoryOptions.unshift({
        'label': 'select a category',
        'value': ''
    });

    const tags = useSelect( ( select ) => {
        return select( 'core' ).getEntityRecords( 'taxonomy', 'post_tag', queryArgs );
    }, [] ) || [];

    const tagOptions = tags.map((e) => {
        return {
            'label': e.name,
            'value': e.slug,
        }
    });

    tagOptions.unshift({
        'label': 'select a tag',
        'value': ''
    });

    const selectedCategory = attributes.category || '';
    const selectedTag = attributes.tag || '';

    return (
        <div { ...useBlockProps() }>
            {
                <InspectorControls>
                    <PanelBody title='Settings'>
                        <SelectControl
                            label="Category"
                            onChange={ (value) => onChangeAttribute('category', value) }
                            options={categoryOptions}
                            value={selectedCategory}
                        />
                        <SelectControl
                            label="Tag"
                            onChange={ (value) => onChangeAttribute('tag', value) }
                            options={tagOptions}
                            value={selectedTag}
                        />
                    </PanelBody>
                </InspectorControls>
            }
            <p>Category: {selectedCategory}, Tag: {selectedTag}</p>
        </div>
    );
};