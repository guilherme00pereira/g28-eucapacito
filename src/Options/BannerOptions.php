<?php

namespace G28\Eucapacito\Options;

class BannerOptions 
{
    const HOME_BANNERS_OPTION           = 'eucapacito_home_banners';

    public static function getBanners( $type = null )
    {
        $banners = [];
        $items = get_option(self::HOME_BANNERS_OPTION);
        foreach( $items as $item ) {
            $banner = [
                'id'        => $item->id,
                'image'     => wp_get_attachment_image_src($item->id, "medium")[0],
                'link'      => $item->link,
                'device'    => $item->device
            ];
            array_push( $banners, $banner);
        }
        return $banners;
    }

    public function saveBaners()
    {
        # code...
    }
}