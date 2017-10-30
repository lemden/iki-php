<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\images;

use core\routing\AbstractController;
use Imagick;
use ImagickDraw;
use ImagickException;
use ImagickPixel;

use logic\images\AreasFinderConfig;
use logic\images\PhotoArea;
use logic\process\ProgressCallback;

class AreasFinder
{
    const MAX_DELTA     = 442;

    private $canOverlap = [];
    private $maxY = 0;

    /**
     * @var AreasFinderConfig
     */
    private $config;

    private $imageSource;

    private $progressCallback;

    private $factor = 1;

    public function __construct(AreasFinderConfig $config, Imagick $imageSource, ProgressCallback $progressCallback = null)
    {
        $this->config = $config;
        $this->imageSource = $imageSource;
        $this->progressCallback = $progressCallback;
    }

    public function setFactor($factor)
    {
        $this->factor = $factor;
    }

    /**
     * @return AreasFinderConfig
     */
    private function config ()
    {
        return $this->config;
    }


    public function getAreas ()
    {
        $areas = [];
        $normalizedWidth = $this->getNormalizedSize($this->imageSource->getImageWidth());
        $normalizedHeight = $this->getNormalizedSize($this->imageSource->getImageHeight());

        $P = $normalizedWidth * $normalizedHeight;

        $sum_p = 0;
        $i = 0;

        for ($y = 0;$y < $normalizedHeight;$y += $this->config()->getMinSize())
        {

            for ($x = 0;$x < $normalizedWidth;$x += $this->config()->getMinSize())
            {
                $size = $this->config()->getMinSize();

                $photoArea = new PhotoArea();
                $photoArea->setX($x)
                    ->setY($y)
                    ->setSize($size)
                    ->setIndex($i);

                if ($this->isAreaOverlapWithOther($photoArea))
                    continue;

                $averageColor = $this->getAreaPixel($this->imageSource, $x, $y, $size);
                $resultSize = $this->getMaxPossibleSize($this->imageSource, $x, $y, $size, $averageColor, $areas, $i);

                $photoArea->setAverageColor($averageColor)
                    ->setSize($resultSize);

                $averageColor->destroy();

                $p = pow($resultSize, 2);
                $sum_p += $p;

                $areas [] = $photoArea;
                $i ++;

                if ($resultSize != $this->config()->getMinSize()) {

                    if (!isset($this->canOverlap[$photoArea->getBottomY()]))
                        $this->canOverlap[$photoArea->getBottomY()] = [];

                    $this->maxY = max($this->maxY, $photoArea->getBottomY());
                    $this->canOverlap[$photoArea->getBottomY()][$photoArea->getBottomLeftX()] = $photoArea;
                }

                $x += $resultSize - $this->config()->getMinSize();

                if (null != $this->progressCallback) {
                    $percent = round($sum_p / $P * 100);
                    $this->progressCallback->trigger($percent);
                }
            }
        }

        if ($sum_p != $P)
            throw new \Exception ("FAIL");


        return $areas;
    }

    private function getMaxPossibleSize(Imagick $imageSource, $x, $y, $size, $averageColor, array $areas)
    {
        if ($size == $this->config()->getMaxSize())
            return $size;

        $width = $imageSource->getImageWidth();
        $height = $imageSource->getImageHeight();

        $correct = true;
        for ($i=0;$i<2;$i++)
        {
            for ($j=0;$j<2;$j++)
            {
                if (!$i && !$j)
                    continue;

                $_x = $x + $i*$size;
                $_y = $y + $j*$size;

                $bottom_right_x = $_x + $size;
                $bottom_right_y = $_y + $size;

                if ($bottom_right_x > $width || $bottom_right_y > $height) {
                    $correct = false;
                    break 2;
                }

                $color = $this->getAreaPixel($imageSource, $_x, $_y, $size);
                $notSimilar = !$this->isColorSimilar($averageColor, $color, $delta);
                $color->destroy();
                if ($notSimilar)
                {
                    $correct = false;
                    break 2;
                }
            }
        }

        if ($correct)
        {
            $area = new PhotoArea();
            $area->setX($x)->setY($y)->setSize($size * 2);
            if ($this->isAreaOverlapWithOther($area, $areas)) {
                return $size;
            }

            return $this->getMaxPossibleSize($imageSource, $x, $y, $size * 2, $averageColor, $areas);
        }
        else
        {
            return $size;
        }
    }

    private function isAreaOverlapWithOther (PhotoArea $area)
    {
        for ($y=$area->getY() + $this->config()->getMinSize(); $y <= $this->maxY; $y+=$this->config()->getMinSize())
        {
            if (!isset($this->canOverlap[$y])) {
                continue;
            }

            $areas = $this->canOverlap[$y];
            foreach ($areas as $_area)
            {
                if ($_area instanceof PhotoArea) {
                    if ($_area->isOverlap($area)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }



    /**
     * @param Imagick $imageSource
     * @param $i
     * @param $j
     * @param $size
     * @param $averageColor
     * @return ImagickPixel
     */
    private function getAreaPixel(Imagick $imageSource, $i, $j, $size)
    {
        $cloned = clone($imageSource);
        $cloned->cropImage($size, $size, $i, $j);
        $cloned->scaleImage(1,1);
        $pixel = $cloned->getImagePixelColor(0,0);
        $cloned->destroy();
        return $pixel;
    }

    private function isColorSimilar (ImagickPixel $color1, ImagickPixel $color2, &$delta)
    {
        return PhotoUtils::isColorSimilar($color1,$color2,$delta,$this->config()->getMinPercent());
    }

    private function getNormalizedSize($size)
    {
        $normalizedSize = floor($size / $this->config()->getMinSize());

        if ($size % $this->config()->getMinSize() != 0)
            $normalizedSize+=1;
        return $normalizedSize * $this->config()->getMinSize();
    }
}