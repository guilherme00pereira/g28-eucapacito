<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Models\User;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class Registrator extends WP_REST_Controller {

    public function __construct()
    {
        $this->register_routes();
        $this->addFieldsToApi();
    }

    public function register_routes()
	{
		$namespace      = 'eucapacito/v1';
        register_rest_route( $namespace, '/ping', array(

        ));
		register_rest_route( $namespace, '/register', array(
			'methods'       => WP_REST_Server::EDITABLE,
			'callback'      => array( $this, 'registerUser' )
		) );
        register_rest_route( $namespace, '/update-profile', array(
			'methods'       => WP_REST_Server::EDITABLE,
			'callback'      => array( $this, 'updateUser' )
		) );
        register_rest_route( $namespace, '/recoverpwd', array(
			'methods'       => WP_REST_Server::EDITABLE,
			'callback'      => array( $this, 'recoverPassword' )
		) );
	}

    public function addFieldsToApi()
    {
        add_filter( 'rest_prepare_user', function( $response, $user, $request ) {
            $response->data[ 'email' ] = $user->user_email;
            return $response;
        }, 10, 3 );
    }

    public function registerUser( $request ): WP_REST_Response
    {
        $user = new User();
        $user->setEmail($request['email'])
            ->setName($request['name'])
            ->setPassword($request['password']);
        list($created, $response) = $user->createWPUser();
        if( $created ) {
            return new WP_REST_Response( $response, 200 );
        }
        return new WP_REST_Response( $response , 500 );
    }

    public function updateUser( $request ): WP_REST_Response
    {
        $user = new User();
        $user->setId( $request['id'])
                ->setEmail( $request['email'] )
                ->setName( $request['full_name'] )
                ->setBirthdate( $request['b_day'], $request['b_month'], $request['b_year'] )
                ->setPhone( $request['phone_ddd'], $request['phone_number'] )
                ->setCountry( $request['country'] )
                ->setState( $request['state'] )
                ->setCity( $request['city'] );
        list($created, $response) = $user->updateWPUser();
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