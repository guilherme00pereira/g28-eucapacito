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
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( $this, 'ping' )
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

        // SEARCH ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, '/search', array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( SearchEndpoints::getInstance(), 'getSearch' )
        ) );

        // PAGES ENDPOINTS
        register_rest_route( $this->eucapacito_namespace, "/aboutpage", array(
            'methods'       => WP_REST_Server::READABLE,
            'callback'      => array( PageEndpoints::class, 'getAboutPage' )
        ) );
	}

    public function addFieldsToApi()
    {
        add_filter( 'rest_prepare_user', function( $response, $user, $request ) {
            $response->data[ 'email' ]      = $user->user_email;
            $meta                           = get_user_meta( $user->ID, 'avatar_id' );
            $response->data[ 'avatar' ]     = wp_get_attachment_image_url( $meta[0] );
            return $response;
        }, 10, 3 );
        add_filter( 'rest_prepare_curso_ec', function( $response, $post, $request ) {
            $response->data[ 'duration' ] = get_post_meta($post->ID, '_learndash_course_grid_duration');
            return $response;
        }, 10, 3 );
    }

    public function ping( $request )
    {
        //echo get_user_meta( 52351, 'avatar_id' )[0] . PHP_EOL . wp_get_attachment_image_url( 13076 );
        //echo get_post_meta( 10429, 'responsavel')[0]['guid'];
        $t = SearchEndpoints::getInstance()->getTaxonomies();
        $tr = wp_get_post_terms( 10429, $t );
        $res = array_column($tr, 'term_id');
        echo array_intersect([232], $res)[0];
    }

    

}