<?php
define( "PL_OPTIONS_KEY", "pl_options" );

class PromptsLab {
    protected static $_plugin_file;
    protected static $_instance = null;

    public static function initialize($plugin_file) {
        PromptsLab::$_plugin_file = $plugin_file;
    }

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->includes();
    }

    public function includes() {
        include_once 'functions.php';
        include_once 'class-telegram-api.php';
        include_once 'publish-telegram-hooks.php';
        include_once 'admin/class-pl-admin.php';
    }
}