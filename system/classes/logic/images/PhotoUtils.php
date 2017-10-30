<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\images;

use Imagick;
use ImagickPixel;

abstract class PhotoUtils
{

    const MAX_DELTA     = 442;

    /**
     * @param Imagick $image
     * @return \ImagickPixel
     */
    public static function getPhotoColor (Imagick $image)
    {
        $cloned = clone($image);
        $cloned->scaleImage(1,1);
        $pixel = $cloned->getImagePixelColor(0,0);
        $cloned->destroy();
        return $pixel;
    }

    public static function createThumb(Imagick $image, $targetWidth = 400){
        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        if ($width >= $targetWidth && $height >= $targetWidth) {
            $maxWidth = min($width, $height);

            $x = floor($width > $height ? ($width - $height) / 2 : 0);
            $y = floor($height > $width ? ($height - $width) / 2 : 0);

            $image->cropImage($maxWidth, $maxWidth, $x, $y);
            $image->scaleImage($targetWidth, $targetWidth);

            return $image;
        }
        return null;
    }

    /**
     * @param array $rgb
     * @return string
     */
    public static function rgbToHex(array $rgb)
    {
        $hex = "#";
        $hex .= str_pad(dechex($rgb['r']), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb['g']), 2, "0", STR_PAD_LEFT);
        $hex .= str_pad(dechex($rgb['b']), 2, "0", STR_PAD_LEFT);
        return $hex;
    }

    /**
     * @param ImagickPixel $color
     * @return string
     */
    public static function getHexColor (ImagickPixel $color)
    {
        return self::rgbToHex($color->getColor());
    }

    /**
     * @param ImagickPixel $color1
     * @param ImagickPixel $color2
     * @param $delta
     * @param int $minPercent
     * @return bool
     */
    public static function isColorSimilar (ImagickPixel $color1, ImagickPixel $color2, &$delta, $minPercent = 3)
    {
        return ($delta = self::getColorDelta($color1, $color2)) < $minPercent;
    }

    /**
     * @param ImagickPixel $color1
     * @param ImagickPixel $color2
     * @return float
     */
    public static function getColorDelta (ImagickPixel $color1, ImagickPixel $color2)
    {
        $c1 = $color1->getColor();
        $c2 = $color2->getColor();
        return self::getColorDeltaByArray($c1, $c2);
    }

    /**
     * @param array $color1
     * @param array $color2
     * @return float
     */
    public static function getColorDeltaByArray (array $c1, array $c2)
    {
        $delta = sqrt(pow($c1['r'] - $c2['r'],2) + pow($c1['g'] - $c2['g'],2) + pow($c1['b'] - $c2['b'],2));
        return $delta / self::MAX_DELTA * 100;
    }
}