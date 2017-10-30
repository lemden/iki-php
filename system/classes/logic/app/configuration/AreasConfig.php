<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app\configuration;

use logic\app\configuration\AppConfigBase;
use logic\app\configuration\Configuration;

class AreasConfig extends AppConfigBase {

    private static $_instance;
    
    public static function getInstance(){
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }

    private function __construct(){
        $this->config = Configuration::getInstance();
    }

    private function _normSize($size) {
        $size = intval($size);
        if ($size < 20)
            return 20;
        else if ($size > 640) {
            return 640;
        } else {
            return $size;
        }
    }

    public function getMinSize(){
        return $this->_normSize(
            $this->config->getMinSize());
    }

    public function getMaxSize(){
        return $this->_normSize(
                $this->config->getMaxSize());
    }

    public function getMinPercent(){
        return $this->getPercent(
            $this->config->getMinPercent()
        );
    }

    public function getImage(){
        return $this->config->getImage();
    }

    public function getPid(){
        return $this->config->getPid();
    }
}