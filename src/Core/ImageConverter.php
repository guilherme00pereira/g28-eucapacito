<?php

namespace G28\Eucapacito\Core;

class ImageConverter
{
    public static function generetaWebpFile($image_url)
    {
        $imageSubPath = substr( $image_url, strpos($image_url, "uploads"));
        $upload_dir = wp_upload_dir();
        $imageFile = $upload_dir['basedir'] . str_replace('uploads', '', $imageSubPath) . ".webp";
        if( file_exists( $imageFile ) ) 
        {
            return $imageFile;
        }
        else
        {
            $image_type = exif_imagetype($image_url);
            $mime_type = image_type_to_mime_type($image_type);
            if ($mime_type == 'image/jpeg' || $mime_type == 'image/png') {
                $image = imagecreatefromstring(file_get_contents($image_url));
                $saved = imagewebp($image, $imageFile);
                imagedestroy($image);
                if($saved) {
                    return $image_url . '.webp';
                } else {
                    return "";
                }
                
            }
        }
    }
}
