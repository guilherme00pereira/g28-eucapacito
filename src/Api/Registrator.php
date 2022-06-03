<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Models\User;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class Endpoints extends WP_REST_Controller {

    public function register_routes()
	{
		$namespace      = 'eucapacito/v1';
		register_rest_route( $namespace, '/register', array(
			'methods'       => WP_REST_Server::EDITABLE,
			'callback'      => array( $this, 'registerUser' )
		) );
        register_rest_route( $namespace, '/recoverpass', array(
			'methods'       => WP_REST_Server::EDITABLE,
			'callback'      => array( $this, 'recoverPassword' )
		) );
	}

    public function registerUser( $request ): WP_REST_Response
    {
        $user = new User(
            $request['email'],
            $request['name'],
            $request['password']
        );
        list($created, $response) = $user->createWPUser();
        if( $created ) {
            return new WP_REST_Response( $response, 200 );
        }
        return new WP_REST_Response( $response , 500 );
    }

    public function recoverPassword( $request )
    {
        $email      = $request['email'];
        $user       = get_user_by( 'email ', $email );
    }

}