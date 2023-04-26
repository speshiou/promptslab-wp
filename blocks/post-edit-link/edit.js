import {
    useBlockProps,
} from '@wordpress/block-editor';

export default ( { attributes, setAttributes } ) => {
    return (
        <div { ...useBlockProps() }>
            Edit Link
        </div>
    );
};