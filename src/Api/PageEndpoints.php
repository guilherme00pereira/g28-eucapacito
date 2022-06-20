<?php

namespace G28\Eucapacito\Api;

use DOMDocument;
use WP_REST_Response;

class PageEndpoints
{
    protected static ?PageEndpoints $_instance = null;

    public function __construct()
    {
    }

    public static function getInstance(): ?PageEndpoints {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getAboutPage( $request ): WP_REST_Response
    {
        $elements   = [];
        $html       = "<div>";
        $aboutPage  = get_post(13125);//macos: 13125 - trt: 13076 - site: 13315
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
        $elements = $this->getQuote($dom, $elements);
        $elements = $this->filterForWho($dom, $elements);
        $elements = $this->filterStatistics("alunos", $dom, $elements);
        $elements = $this->filterStatistics("conclusao", $dom, $elements);
        $elements = $this->filterStatistics("parceiros", $dom, $elements);
        $elements = $this->filterStatistics("empregos", $dom, $elements);

        return new WP_REST_Response( $elements , 200 );
    }

    /**
     * @param DOMDocument $dom
     * @param array $elements
     * @return array
     */
    private function filterForWho(DOMDocument $dom, array $elements): array
    {
        $forWhoElems = [];
        $forWhoNodes = $dom->getElementById("para_quem_e")->childNodes;
        for ($i = 0; $i < count($forWhoNodes); $i++) {
            $forWhoElems[] = $forWhoNodes[$i]->nodeValue;
        }
        $elements["forwho"] = $forWhoElems;
        return $elements;
    }

    /**
     * @param DOMDocument $dom
     * @param array $elements
     * @return array
     */
    private function getQuote(DOMDocument $dom, array $elements): array
    {
        $quote_elements = explode('.', strrev($dom->getElementById("citacao")->nodeValue), 2);
        $elements["quote"] = strrev($quote_elements[1]);
        $elements["quote_author"] = strrev($quote_elements[0]);
        return $elements;
    }

    /**
     * @param DOMDocument $dom
     * @param array $elements
     * @return array
     */
    private function filterStatistics(string $nodeId, DOMDocument $dom, array $elements): array
    {
        $elements[$nodeId . "_title"] = $dom->getElementById($nodeId)->getElementsByTagName('h4')->item(0)->nodeValue;
        $elements[$nodeId . "_info"] = $dom->getElementById($nodeId)->getElementsByTagName('p')->item(0)->nodeValue;
        return $elements;
    }

}