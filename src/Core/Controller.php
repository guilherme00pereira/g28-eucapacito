<?php

namespace G28\Eucapacito\Core;


class Controller {

    public function __construct()
	{
		add_action('admin_menu', array($this, 'addMenuPage' ));
		add_action( 'admin_enqueue_scripts', [ $this, 'registerStylesAndScripts'] );
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

		wp_enqueue_style(Plugin::getAssetsPrefix() . 'admin_style');
		wp_enqueue_script( Plugin::getAssetsPrefix() . 'admin-scripts' );
        
		ob_start();
        include sprintf( "%sadmin-settings.php", Plugin::getTemplateDir() );
        $html = ob_get_clean();
        echo $html;
		
	}

	public function registerStylesAndScripts()
	{
		wp_register_style( Plugin::getAssetsPrefix() . 'admin_style', Plugin::getAssetsUrl() . 'css/admin-settings.css' );
		wp_register_script(
            Plugin::getAssetsPrefix() . 'admin-scripts',
            Plugin::getAssetsUrl() . 'js/admin-settings.js',
            array( 'jquery' ),
            null,
            true
        );
	}

}