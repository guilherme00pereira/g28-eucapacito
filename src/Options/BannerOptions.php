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
                    'id'        => $item['id'],
                    'hash'      => $item['hash'],
                    'image'     => wp_get_attachment_image_src($item['id'], $size)[0],
                    'link'      => $item['link'],
                    'device'    => $item['device']
                ];
                $banners[] = $banner;
            }
        }
        return $banners;
    }

    public static function saveBanners( $items )
    {
        $banners = [];
        if($items) {
            foreach ($items as $item) {
                $banner = [
                    'hash'      => hash('crc32', rand(0,9999) . time()),
                    'id'        => $item->id,
                    'link'      => $item->link,
                    'device'    => $item->device
                ];
                $banners[] = $banner;
            }
        }
        update_option( self::HOME_BANNERS_OPTION, $banners );
    }
}