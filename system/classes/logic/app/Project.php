<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app;

use logic\images\AreasFinder;
use logic\images\AreasFinderConfig;
use logic\app\configuration\DBConfig;
use logic\app\configuration\AreasConfig;
use logic\images\PhotoArea;
use logic\process\ConsoleProgressCallback;

class Project {

    public function __construct(){
        $this->dbConfig = DBConfig::getInstance();
        $this->areasConfig = AreasConfig::getInstance();
        $pId = $this->areasConfig->getPid();
        if (!$pId)
            $this->pId = sha1(realpath($this->areasConfig->getImage()));
        else
            $this->pId = $pId;
    }

    public function process(){
        $areasFinderConfig = new AreasFinderConfig();
        $areasFinderConfig->
                setMinSize($this->areasConfig->getMinSize())->
                setMaxSize($this->areasConfig->getMaxSize())->
                setMinPercent($this->areasConfig->getMinPercent());
        
        $mainImage = new \Imagick($this->getImageFileName());
        $separator = new AreasFinder(
                            $areasFinderConfig,
                            $mainImage,
                            new ConsoleProgressCallback()
                        );
        $areas = $separator->getAreas();
        $result = json_encode(PhotoArea::toArrays($areas));
        file_put_contents($this->getAreasFileName(), $result);
    }

    public function getAreasFileName(){
        return $this->getProjectFolder() . "areas.json";
    }

    public function getPid(){
        return $this->pId;
    }

    public function getProjectFolder(){
        return $this->dbConfig->getDataFolder() 
                        . $this->getPid() 
                        . "/";
    }

    public function getImageFileName(){
        return $this->getProjectFolder()
                     . "image.jpg";
    }

    public function create(){
        $pDir = $this->getProjectFolder();
        if (!is_dir($pDir)) {
            mkdir($pDir);
        }
        if (!$this->areasConfig->getImage()) {
            throw new \Exception("image path is empty");
        }
        if (!is_file($this->areasConfig->getImage())) {
            throw new \Exception("Main image not found:" . $this->areasConfig->getImage());
        }
        copy($this->areasConfig->getImage(), 
                $this->getImageFileName());
        return $this->getPid();
    }
}