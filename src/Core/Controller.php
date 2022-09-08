<?php

namespace G28\Eucapacito\Core;

use Exception;
use G28\Eucapacito\Options\BannerOptions;
use G28\Eucapacito\Options\MessageOptions;

class Controller {

    public function __construct()
	{
		add_action('admin_menu', array($this, 'addMenuPage' ));
		add_action( 'admin_enqueue_scripts', [ $this, 'registerStylesAndScripts'] );
		add_action( 'wp_ajax_ajaxAddBanner', [ $this, 'ajaxAddBanner' ] );
        add_action( 'wp_ajax_ajaxGetLog', [ $this, 'ajaxGetLog' ] );
        add_action( 'wp_ajax_ajaxRuAvatar', [ $this, 'ajaxRuAvatar' ] );
        add_filter( 'wp_rest_cache/allowed_endpoints', [ $this, 'registerCacheEndpoints' ], 100, 1 );
        add_action( 'pre_get_posts', [ $this, 'hideUserMediaProfile' ], 10, 1 );
        add_filter( 'ajax_query_attachments_args' , [ $this, 'ajaxhideUserMediaProfile' ], 10, 1 );
	}

    public function addMenuPage()
	{
		add_menu_page(
			'Eu Capacito WebApp',
			'Eu Capacito WebApp',
			'manage_options',
			MessageOptions::OPTIONS_NAME,
			array( $this, 'renderMenuPage' ),
            plugins_url( 'g28-eucapacito/assets/img/admin-menu-icon.jpg' ),
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
            $banners	= json_decode( stripslashes( $_POST['banners'] ) );
            BannerOptions::saveBanners( $banners );
            echo json_encode(['success' => true, 'message' => 'Banners atualizados com sucesso!']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar bannners.']);
        }
        wp_die();
	}

    public function ajaxGetLog()
    {
        try {
            wp_verify_nonce( 'eucap_nonce' );
            $file	    = json_decode( stripslashes( $_GET['filename'] ) );
            $content    = Logger::getInstance()->getLogFileContent( $file );
            echo json_encode(['success' => true, 'message' => $content]);
        } catch (Exception $e) {
            echo json_encode(['error' => false, 'message' => 'Erro ao abrir arquivo de log.']);
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
			'action_saveBanner'	=> 'ajaxAddBanner',
            'action_getLog'     => 'ajaxGetLog',
            'action_runAvatar'  => 'ajaxRuAvatar'
		]);
	}

    public function registerCacheEndpoints( $allowed_endpoints ): array
    {
        if (!isset($allowed_endpoints['ldlms/v2']) || in_array('sfwd-questions', $allowed_endpoints['ldlms/v2'])) {
            $allowed_endpoints['ldlms/v2'][] = 'sfwd-questions';
        }
        if ( isset($allowed_endpoints['wp/v2'])  && ( $key = array_search('users', $allowed_endpoints['wp/v2'] ) ) !== false ) {
            unset( $allowed_endpoints[ 'wp/v2' ][ $key ] );
        }
        return $allowed_endpoints;
    }

    public function hideUserMediaProfile( $query )
    {
        if ( ! is_admin() ) {
            return;
        }
        if ( ! $query->is_main_query() ) {
            return;
        }
        $screen = get_current_screen();
        if ( ! $screen || 'upload' !== $screen->id || 'attachment' !== $screen->post_type ) {
            return;
        }
        $query->set('meta_query', [
            [
                'key'       => 'is_avatar',
                'compare'   => 'NOT EXISTS',
            ]
        ]);
        return $query;
    }

    public function ajaxhideUserMediaProfile( $query )
    {
        if ( ! is_admin() ) {
            return $query;
        }
        if( $query['post_type'] != 'attachment' ) {
            return $query;
        }
        $query['meta_query'] = [
            [
                'key'       => 'is_avatar',
                'compare'   => 'NOT EXISTS',
            ]
        ];
        return $query;
    }

    public function ajaxRuAvatar()
    {
        try {
            wp_verify_nonce( 'eucap_nonce' );
            $count          = 0;
            $subscribers    = DBQueries::getSubscribersIds();
            $authors        = DBQueries::getMediaAuthorIds();
            foreach ($authors as $author){
                if(in_array( $author['author'], $subscribers)) {
                    update_post_meta($author['id'], 'is_avatar', true);
                    $count++;
                }
            }
            echo json_encode(['success' => true, 'message' => $count . " posts processados"]);
        } catch (Exception $e) {
            echo json_encode(['error' => false, 'message' => 'Erro ao atualizar avatars.']);
        }
        wp_die();
    }

}