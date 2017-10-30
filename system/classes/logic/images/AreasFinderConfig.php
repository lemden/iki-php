<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\images;

class AreasFinderConfig
{

    private $minSize;
    private $maxSize;
    private $minPercent;

    /**
     * @return mixed
     */
    public function getMinSize()
    {
        return $this->minSize;
    }

    /**
     * @param mixed $minSize
     */
    public function setMinSize($minSize)
    {
        $this->minSize = $minSize;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * @param mixed $maxSize
     */
    public function setMaxSize($maxSize)
    {
        $this->maxSize = $maxSize;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinPercent()
    {
        return $this->minPercent;
    }

    /**
     * @param mixed $minPercent
     */
    public function setMinPercent($minPercent)
    {
        $this->minPercent = $minPercent;
        return $this;
    }
}