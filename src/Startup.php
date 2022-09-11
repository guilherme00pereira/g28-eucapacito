<?php

namespace G28\Eucapacito;

use G28\Eucapacito\Core\CustomPostTypes;
use G28\Eucapacito\Core\Plugin;
use G28\Eucapacito\Api\EndpointRegistrator;
use G28\Eucapacito\Core\Controller;
use G28\Eucapacito\Options\MessageOptions;

class Startup {

    protected static ?Startup $_instance = null;

	public static function getInstance(): ?Startup {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    public function run( string $root ) {
		
        add_action( 'plugins_loaded', function () use ( $root ) {
			Plugin::getInstance($root);
			new Controller();
            new CustomPostTypes();
			new MessageOptions();
		} );
        add_action( 'rest_api_init', function (){
			new EndpointRegistrator();
		});
		add_action( 'admin_init', function() {
			$subscriber = get_role('subscriber');
			$subscriber->add_cap('upload_files');
		});
    }

}