<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app;

use logic\process\ConsoleLogger;
use logic\images\DBMaker;
use logic\app\configuration\DBConfig;

class DB {
    public function __construct(){
        $this->dbConfig = DBConfig::getInstance();
    }

    public function create(){
        $dbMaker = new DBMaker(
                $this->getFolder($this->dbConfig->getSourceFolder()),
                $this->dbConfig->getDataFolder(),
                new ConsoleLogger());
        $dbMaker->create($this->dbConfig->getThumbSize(), 
                    $this->dbConfig->getThumbQuality());
    }

    private function getFolder($folder){
        if (!is_dir($folder)) {
            return realpath(getcwd() . "/" . $folder . '/');
        }
        return realpath($folder) . '/';
    }
}