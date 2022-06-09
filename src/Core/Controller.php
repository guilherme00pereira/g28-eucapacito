<?php

namespace G28\Eucapacito\Core;


class Controller {

    public function __construct()
	{
		add_action('admin_menu', array($this, 'addMenuPage' ));
	}

    public function addMenuPage()
	{
		add_menu_page(
			'Eu Capacito WebApp',
			'Eu Capacito WebApp',
			'manage_options',
			OptionsManager::OPTIONS_NAME,
			array( $this, 'renderMenuPage' ),
            plugins_url( 'g28-eucapacito/assets/img/admin-menu-icon.jpg' ),//'dashicons-dashboard',
            58
		);
	}

	public function renderMenuPage()
	{
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ob_start();
        include sprintf( "%sadmin-settings.php", Plugin::getTemplateDir() );
        $html = ob_get_clean();
        echo $html;
		
	}

}