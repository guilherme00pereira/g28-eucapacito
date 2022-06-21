<?php

namespace G28\Eucapacito\Options;

class BannerOptions 
{
    const HOME_BANNERS_OPTION           = 'eucapacito_home_banners';

    public static function getBanners( $size = "medium" ): array
    {
        $banners = [];
        $items = get_option(self::HOME_BANNERS_OPTION);
        if($items) {
            foreach ($items as $item) {
                $banner = [
                    'id' => $item->id,
                    'image' => wp_get_attachment_image_src($item->id, $size)[0],
                    'link' => $item->link,
                    'device' => $item->device
                ];
                $banners[] = $banner;
            }
        }
        return $banners;
    }
}