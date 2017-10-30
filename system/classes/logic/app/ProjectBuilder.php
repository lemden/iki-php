<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app;

use logic\images\MosaicBuilder;

use logic\app\configuration\DBConfig;
use logic\app\configuration\BuilderConfig;

use logic\process\ConsoleLogger;
use logic\process\ConsoleProgressCallback;

class ProjectBuilder {
    public function __construct(){
        $this->dbConfig = DBConfig::getInstance();
        $this->builderConfig = BuilderConfig::getInstance();
    }

    public function process(){
        $builder = new MosaicBuilder(
            $this->builderConfig->getPid(),
            $this->dbConfig->getDataFolder(),
            $this->builderConfig->getColorize(),
            $this->builderConfig->getUseOriginal(),
            $this->builderConfig->getDiversity(),
            $this->builderConfig->getFactor(),
            new ConsoleLogger()
        );
        $builder->build();
        return $builder->getResultFileName();
    }
}