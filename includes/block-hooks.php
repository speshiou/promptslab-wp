<?php
function register_pl_blocks() {
    $dirs = pl_find_block_dirs(pl_plugin_dir_filename('build/blocks'));
    foreach ($dirs as $dir) {
        register_block_type_from_metadata($dir);

        if (file_exists($dir . '/index.php')) {
            require_once $dir . '/index.php';
        }
    }
}
add_action( 'init', 'register_pl_blocks' );