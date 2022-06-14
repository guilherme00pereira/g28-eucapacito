<?php

namespace G28\Eucapacito\Api;

use WP_REST_Response;
use WP_Query;

class SearchEndpoints
{
    public function getFilters()
    {
        $filters = [
            'nivel'                 => [],
            'avaliao'               => [],
            'categoria_de_curso_ec' => [],
            'parceiro_ec'           => []
        ];
        $taxonomies = get_object_taxonomies( 'curso_ec' );
        foreach( $taxonomies as $taxonomy ){
            $terms = get_terms(array(
                'taxonomy' => $taxonomy,
                'hide_empty' => false,
            ));
            foreach( $terms as $term ){
                array_push($filters[$term->taxonomy], [
                    'id'        => $term->term_id,
                    'name'      => $term->name,
                    'count'     => $term->count,
                ]);
            }
        }
        return new WP_REST_Response( $filters , 200 );
    }
}