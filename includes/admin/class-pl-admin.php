<?php
class PL_Admin {
    public function __construct() {
		$this->includes();
	}

    public function includes() {
        include_once __DIR__ . '/admin-functions.php';
        include_once __DIR__ . '/class-pl-admin-page.php';
        include_once __DIR__ . '/class-pl-option-setting.php';
    }
}

new PL_Admin();