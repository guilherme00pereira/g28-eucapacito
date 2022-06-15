<?php

namespace G28\Eucapacito\Api;

use WP_REST_Response;
use WP_Query;

class SearchEndpoints
{
    public function getFilters(): WP_REST_Response
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
                $filters[$term->taxonomy][] = [
                    'id' => $term->term_id,
                    'name' => $term->name,
                    'count' => $term->count,
                ];
            }
        }
        return new WP_REST_Response( $filters , 200 );
    }

    public function getSearch( $request ): WP_REST_Response
    {
        $courses = [];
        $args = [
            'post_type'         => 'curso_ec',
            'posts_per_page'    => 15,
            'paged'              => $request['page'],
            's'                 => $request['search']
        ];
//        $args['tax_query'] = [
//            [
//                'taxonomy'  => 'nivel',
//                'field'     => 'name',
//                'terms'     => 'Intermediario'
//            ]
//        ];
        $query = new WP_Query( $args );
        while ($query->have_posts()) {
            $query->the_post();
            $postId = get_the_ID();
            $partnerId = wp_get_post_terms( $postId, 'parceiro_ec');
            $courses[] = [
                'id'                => $postId,
                'slug'              => basename(get_permalink($postId)),
                'title'             => $query->post->post_title,
                'type'              => 'curso_ec',
                'logo'              => get_post_meta( $postId, 'responsavel')[0]['guid']
            ];
        }
        $response = new WP_REST_Response( [
            'courses'   => $courses,
            'total'     => $query->found_posts
        ]);
        $response->set_status(200);
        $response->header( 'x-wp-totalpages', $query->max_num_pages);
        wp_reset_postdata();
        return $response;
    }
}