<?php

namespace G28\Eucapacito\Core;

class ImageConverter
{
    public static function generetaWebpFile($image_url)
    {
        $imageSubPath = substr( $image_url, strpos($image_url, "upload"));
        $upload_dir = wp_upload_dir();
        echo $upload_dir['basedir'] . $imageSubPath;

        // Get the image type and mime type
//        $image_type = exif_imagetype($image_url);
//        $mime_type = image_type_to_mime_type($image_type);
//        // Check if the image is supported by WebP
//        if ($mime_type == 'image/jpeg' || $mime_type == 'image/png') {
//            // Create a new image resource
//            $image = imagecreatefromstring(file_get_contents($image_url));
//            // Save the image as a WebP image
//            imagewebp($image, $image_url . '.webp');
//            // Free up memory
//            imagedestroy($image);
//            return $image_url . '.webp';
//        }
    }
}
