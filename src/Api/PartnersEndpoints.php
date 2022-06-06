<?php

namespace G28\Eucapacito\Api;

use WP_REST_Response;

class PartnersEndpoints
{

    public function getPartners( $request ): WP_REST_Response
    {

        return new WP_REST_Response( "Senha alterada com sucesso!" , 200 );

    }

}