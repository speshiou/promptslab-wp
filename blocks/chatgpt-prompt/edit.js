import {
    useBlockProps,
} from '@wordpress/block-editor';

export default ( { attributes, setAttributes } ) => {
    return (
        <div { ...useBlockProps() }>
            SD Prompt content
        </div>
    );
};