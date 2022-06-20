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
        $aboutPage  = get_post(13315);//macos: 13125 - trt: 13076 - site: 13315
        $blocks     = parse_blocks($aboutPage->post_content);
        foreach ( $blocks as $block ){
            $html .= render_block($block);
        }
        $html .= "</div>";
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="iso-8859-1">' . $html );
        
        $elements["video"]          = $dom->getElementsByTagName("figure")->item(0)->textContent;
        $elements["lide"]           = $dom->getElementById("lide")->nodeValue;
        $elements["full_text"]      = $dom->saveHTML($dom->getElementById("full_text"));
        
        $quote_elements             = explode( '.', strrev( $dom->getElementById("citacao")->nodeValue ), 2 );
        $elements["quote"]          = strrev( $quote_elements[1] );
        $elements["quote_author"]   = strrev( $quote_elements[0] );

        /* $items                      = $dom->getElementById("icons")->childNodes;
        foreach( $items as $item )
        {
            $elements["icons"][]          = $item;
        } */
        
        return new WP_REST_Response( $elements , 200 );
    }

}