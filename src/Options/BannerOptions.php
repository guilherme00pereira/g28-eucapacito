<?php

namespace G28\Eucapacito\Options;

use G28\Eucapacito\Core\ImageConverter;

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
                    'image'     => self::getBannerMediaUrl($item['id'], $size, $item['type'], $item['link']),
                    'link'      => $item['link'],
                    'device'    => $item['device'],
                    'type'      => $item['type']
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
                    'device'    => $item->device,
                    'type'      => $item->type
                ];
                $banners[] = $banner;
            }
        }
        update_option( self::HOME_BANNERS_OPTION, $banners );
    }

    private static function getBannerMediaUrl( $id, $size, $type, $link )
    {
        if( $type === "video" )
        {
            if(strpos($link, "youtube")) {
                parse_str( parse_url( $link, PHP_URL_QUERY ), $vars );
                return "https://img.youtube.com/vi/" . $vars['v'] . "/default.jpg";
            }
            //https://img.youtube.com/vi/<insert-youtube-video-id-here>/default.jpg
        }
        return ImageConverter::generetaWebpFile( wp_get_attachment_image_src($id, $size)[0] );
    }
}