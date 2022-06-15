<?php

namespace G28\Eucapacito\Api;

class PageEndpoints
{

    public function getAboutPage( $request )
    {
        $aboutPage = get_post(13076);
        $content = apply_filters( 'the_content', $aboutPage->post_content );
        echo $content;

    }

}