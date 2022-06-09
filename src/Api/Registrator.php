<?php

namespace G28\Eucapacito\Api;

use G28\Eucapacito\Models\User;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

class Registrator extends WP_REST_Controller {

    protected string $jwt;
    protected string $eucapacito_namespace;

    public function __construct()
    {
        $this->jwt                      = 'jwt-auth/';
        $this->eucapacito_namespace     = 'eucapacito/v1';
        $this->register_routes();
        $this->addFieldsToApi();
    }

    public function register_routes()
	{

        register_rest_route( $this->eucapacito_namespace, '/ping', array(

        ));

        // USER ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, '/register', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'registerUser' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/update-profile', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'updateUser' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/recoverpwd', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'recoverPassword' )
        ) );
        register_rest_route( $this->eucapacito_namespace, '/changepwd', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'changePassword' )
        ) );
        register_rest_route( $this->jwt, $this->eucapacito_namespace . '/avatar', array(
            'methods'       => WP_REST_Server::EDITABLE,
            'callback'      => array( UserEndpoints::class, 'avatar' )
        ) );


        // PARTNERS ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, '/partners', array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( PartnersEndpoints::class, 'getPartners' )
        ) );


        register_rest_route( $this->eucapacito_namespace, "/page/(?P<id>\d+)", array(
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
        add_filter( 'rest_prepare_curso_ec', function( $response, $post, $request ) {
            $response->data[ 'duration' ] = get_post_meta($post->ID, '_learndash_course_grid_duration');
            return $response;
        }, 10, 3 );
    }



    public function getPage( $request )
    {
        $pageId = $request['id'];
        $content = get_post_meta($pageId, 'gdlr-core-page-builder');
        echo $content;

    }

}