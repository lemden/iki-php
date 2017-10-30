<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\images;

use ImagickPixel;

class PhotoArea
{
    private $index;
    private $x;
    private $y;
    private $size;

    private $factor = 1;

    /**
     * @var array
     */
    private $averageColor;

    /**
     * @return mixed
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param mixed $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getX()
    {
        return $this->x * $this->factor;
    }

    /**
     * @param mixed $x
     */
    public function setX($x)
    {
        $this->x = $x;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getY()
    {
        return $this->y * $this->factor;
    }

    /**
     * @param mixed $y
     */
    public function setY($y)
    {
        $this->y = $y;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size * $this->factor;
    }

    public function setFactor($factor)
    {
        $this->factor = $factor;
        return $this;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return array
     */
    public function getAverageColor()
    {
        return $this->averageColor;
    }

    public function getTopLeftX()
    {
        return $this->x;
    }

    public function getTopLeftY()
    {
        return $this->y;
    }

    public function getBottomRightX()
    {
        return $this->x + $this->size;
    }

    public function getBottomRightY()
    {
        return $this->y + $this->size;
    }

    public function getBottomLeftX()
    {
        return $this->x;
    }

    public function getBottomLeftY()
    {
        return $this->y + $this->size;
    }

    public function getTopRightX()
    {
        return $this->x + $this->size;
    }

    public function getTopRightY()
    {
        return $this->y;
    }

    /**
     * @param ImagickPixel $averageColor
     */
    public function setAverageColor(ImagickPixel $averageColor)
    {
        $this->averageColor = $averageColor->getColor();
        return $this;
    }

    public function setAverageColorArr(array $averageColor)
    {
        $this->averageColor = $averageColor;
        return $this;
    }

    public function getR()
    {
        return $this->averageColor['r'];
    }

    public function getG()
    {
        return $this->averageColor['g'];
    }

    public function getB()
    {
        return $this->averageColor['b'];
    }

    public function isInArea($x, $y)
    {
        return $x > $this->getTopLeftX() && $x < $this->getBottomRightX()
                && $y > $this->getTopLeftY() && $y < $this->getBottomRightY();
    }

    public function isOverlap(PhotoArea $area)
    {
        if ($this->equals($area))
            return true;
        if ($this->isOverlapInner($area))
            return true;
        if ($area->isOverlapInner($this))
            return true;
        return false;
    }

    private function isOverlapInner(PhotoArea $area)
    {
        if ($this->isInArea($area->getTopLeftX(), $area->getTopLeftY()))
            return true;
        if ($this->isInArea($area->getTopRightX(), $area->getTopRightY()))
            return true;
        if ($this->isInArea($area->getBottomLeftX(),$area->getBottomLeftY()))
            return true;
        if ($this->isInArea($area->getBottomRightX(), $area->getBottomRightY()))
            return true;
        return false;
    }

    public function equals(PhotoArea $area)
    {
        return $this->x == $area->x && $this->y == $area->y && $this->size == $area->size;
    }

    public function getBottomY()
    {
        return $this->y + $this->size;
    }

    public function toArray()
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'size' => $this->size,
            'index' => $this->index,
            'color' => $this->getAverageColor()
        ];
    }

    public static function toArrays (array $areas)
    {
        $result = [];
        foreach ($areas as $area)
        {
            if ($area instanceof PhotoArea)
            {
                $result[]=$area->toArray();
            }
        }
        return $result;
    }
}