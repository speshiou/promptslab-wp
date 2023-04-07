<?php
class PL_Admin {
    public function __construct() {
		$this->includes();
	}

    public function includes() {
        include_once 'admin-page-hooks.php';
    }
}

new PL_Admin();