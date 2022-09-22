<?php

namespace G28\Eucapacito\Options;

class PageOptions 
{

    const PAGES_OPTION           = 'eucapacito_page_relationship';

    public static function getPagesRelationship()
    {
        $pages = [];
        $items = get_option(self::PAGES_OPTION);

    }

}