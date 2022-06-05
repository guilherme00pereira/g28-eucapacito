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
        $jwt            = 'jwt-auth/';
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
        register_rest_route( $namespace, '/changepwd', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( $this, 'changePassword' )
        ) );
        register_rest_route( $namespace, "/page/(?P<id>\d+)", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( $this, 'getPage' )
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

    public function recoverPassword( $request ): WP_REST_Response
    {
        if (is_email($request['email'])) {
            if (!email_exists($request['email'])) {
                return new WP_REST_Response("E-mail não cadastrado", 500);
            } else {
                $user       = new User();
                $newPwd     = $user->setUserByEmail( $request['email'] )->generateNewPassword();
                wp_mail(
                    $request['email'],
                    "Eu Capacito - Recuperação de senha",
                    "Sua nova senha: ${newPwd}"
                );
                return new WP_REST_Response( "Nova senha enviada para e-mail informado" , 200 );
            }
        } else {
            return new WP_REST_Response("E-mail inválido.", 500);
        }
    }

    public function changePassword( $request ): WP_REST_Response
    {
        $user_id = $request['id'];
        $user = get_user_by( 'id', $user_id );
        $old = $request['oldPassword'];
        $new = $request['newPassword'];
        $hash = $user->data->user_pass;
        if( wp_check_password( $old, $hash ) ){
            wp_set_password( $new, $user_id );
            return new WP_REST_Response( "Senha alterada com sucesso!" , 200 );
        }else {
            return new WP_REST_Response("Senha atual inválida.", 500);
        }
    }

    public function getPage( $request )
    {
        $pageId = $request['id'];
        $content = get_post_meta($pageId, 'gdlr-core-page-builder');
        echo $content;

    }

}