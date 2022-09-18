<?php

namespace G28\Eucapacito\Api;

use WP_REST_Response;

class TagsEndpoints
{
    protected static ?TagsEndpoints $_instance = null;

    public function __construct()
    {
    }

    public static function getInstance(): ?TagsEndpoints {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function savePostTags( $request ): WP_REST_Response
    {
        $postId     = $request['id'];
        $tags       = $request['tags'];
        $action     = wp_set_post_tags( $postId, $tags );
        if( is_wp_error( $action )) {
            return new WP_REST_Response($action->get_error_message(), 500);
        } else {
            return new WP_REST_Response("sucesso", 200);
        }
    }
}