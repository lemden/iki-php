<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app\configuration;

use logic\app\configuration\Configuration;

class DBConfig {

    private static $_instance;

    private function __construct(){
        $this->config = Configuration::getInstance();
    }

    public static function getInstance(){
        if (empty(self::$_instance))
            self::$_instance = new self();
        return self::$_instance;
    }

    public function getDataFolder(){
        $datafolder = $this->config->getDataFolder();
        if (!empty($datafolder))
            return $datafolder;
        return PROJECT_DIR . "datafolder/";
    }

    public function getThumbSize(){
        return $this->config->getThumbSize();
    }

    public function getThumbQuality(){
        return $this->config->getThumbQuality();
    }

    public function getSourceFolder(){
        return $this->config->getSourceFolder();
    }
}