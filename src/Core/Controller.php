<?php

namespace G28\Eucapacito\Core;

use Exception;
use G28\Eucapacito\Options\BannerOptions;

class Controller {

    public function __construct()
	{
		add_action('admin_menu', array($this, 'addMenuPage' ));
		add_action( 'admin_enqueue_scripts', [ $this, 'registerStylesAndScripts'] );
		add_action( 'wp_ajax_ajaxAddBanner', [ $this, 'ajaxAddBanner' ] );
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

	public function ajaxAddBanner()
	{
		try {
            wp_verify_nonce( 'eucap_nonce' );
            $bannerList = [];
            $banners	= json_decode( stripslashes( $_POST['banners'] ) );
            foreach( $banners as $banner ) $bannerList[] = $banner;
            update_option( BannerOptions::HOME_BANNERS_OPTION, $bannerList );
            echo json_encode(['success' => true, 'message' => 'Banners atualizados com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar bannners.']);
        }
        wp_die();
	}

	public function registerStylesAndScripts()
	{
		wp_register_style( Plugin::getAssetsPrefix() . 'admin_style', Plugin::getAssetsUrl() . 'css/admin-settings.css' );
		wp_register_script(
            Plugin::getAssetsPrefix() . 'admin-scripts',
            Plugin::getAssetsUrl() . 'js/admin-settings.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            null,
            true
        );
		wp_localize_script( Plugin::getAssetsPrefix() . 'admin-scripts', 'ajaxobj', [
			'ajax_url'        	=> admin_url( 'admin-ajax.php' ),
			'eucap_nonce'		=> wp_create_nonce( 'eucap_nonce' ),
			'action_saveBanner'	=> 'ajaxAddBanner'
		]);
	}

}