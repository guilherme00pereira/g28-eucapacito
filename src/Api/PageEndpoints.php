<?php

namespace G28\Eucapacito\Api;

use DOMDocument;
use WP_REST_Response;

class PageEndpoints
{

    public function getAboutPage( $request ): WP_REST_Response
    {
        $elements   = [];
        $html       = "<div>";
        $aboutPage  = get_post(13125);
        $blocks     = parse_blocks($aboutPage->post_content);
        foreach ( $blocks as $block ){
            $html .= render_block($block);
        }
        $html .= "</div>";
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="iso-8859-1">' . $html );
        $elements["video"]      = $dom->getElementsByTagName("figure")->item(0)->textContent;
        $elements["bloco1"]     = $dom->getElementById("bloco1")->nodeValue;
        $elements["texto"]      = $dom->saveHTML($dom->getElementById("texto"));
        return new WP_REST_Response( $elements , 200 );
    }

}