<?php
abstract class PL_AdminPage {
    private $page_title;
    private $menu_title;
    private $menu_id;

    public function __construct($page_title, $menu_title, $menu_id) {
        $this->page_title = $page_title;
        $this->menu_title = $menu_title;
        $this->menu_id = $menu_id;
		$this->hooks();
	}

    private function hooks() {
        add_action( 'admin_init', [ $this, 'settings_init' ] );
        add_action( 'admin_menu', [ $this, 'options_page' ] );
    }
    
    /**
     * Add the top level menu page.
     */
    function options_page() {
        add_menu_page(
            $this->page_title,
            $this->menu_title,
            'manage_options',
            $this->menu_id,
            [ $this, 'options_page_html' ],
        );
    }
    
    
    /**
     * abstract functions
     */
    abstract function settings_init();
    abstract function options_page_html();
}