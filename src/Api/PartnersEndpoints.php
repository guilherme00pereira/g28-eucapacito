<?php

namespace G28\Eucapacito\Api;

use WP_REST_Response;
use WP_Query;

class PartnersEndpoints
{

    public function getPartners( $request ): WP_REST_Response
    {
        $partners = [];

        $query = new WP_Query([
            'post_type'     => 'partner',
            'post_status'   => 'publish',
            'numberposts'   => -1
        ]);
        while ($query->have_posts()) {
            $query->the_post();
            $category = get_the_terms(get_the_ID(), 'partners_category');
            $partners[] = [
                'name'      => get_the_title(),
                'image'    => wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), "medium")[0],
                'category'  => is_bool($category) ? $category : $category[0]->name
            ];
        }
        wp_reset_postdata();
        return new WP_REST_Response( $partners , 200 );
    }

}