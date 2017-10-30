<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app\configuration;
use logic\app\configuration\Configuration;
use logic\app\configuration\AppConfigBase;

class BuilderConfig extends AppConfigBase {
    private static $_instance;

    public static function getInstance(){
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }

    private function __construct(){
        $this->config = Configuration::getInstance();
    }

    public function getColorize(){
        return $this->getPercent(
            $this->config->getColorize()
        );
    }

    public function getFactor(){
        $factor = $this->config->getFactor();
        if (!$factor)
            return 1;
        $factor = intval($factor);
        if ($factor < 0)
            return 1;
        return $factor;
    }

    public function getDiversity(){
        return $this->getPercent(
            $this->config->getDiversity()
        );
    }

    public function getUseOriginal(){
        return $this->getPercent(
            $this->config->getUseOriginal()
        );
    }

    public function getPid(){
        return $this->config->getPid();
    }
}