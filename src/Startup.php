<?php

namespace G28\Eucapacito;

use G28\Eucapacito\Api\PartnersEndpoints;
use G28\Eucapacito\Api\UserEndpoints;
use G28\Eucapacito\Core\CustomPostTypes;
use G28\Eucapacito\Core\Plugin;
use G28\Eucapacito\Api\Registrator;
use G28\Eucapacito\Core\Controller;

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
		} );
        add_action( 'rest_api_init', function (){
			new Registrator();
		});
    }

}