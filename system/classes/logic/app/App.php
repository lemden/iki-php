<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app;

use logic\app\DB;
use logic\app\Project;
use logic\app\ProjectBuilder;
use logic\app\configuration\DBConfig;

class App {

    public function __construct(){
        $this->options = getopt("", $this->getOptions());
    }

    public function run(){
        try {
            $this->_run();
        } catch (\Exception $ex) {
            echo "Error: " . $ex->getMessage() . PHP_EOL;
            $this->wrongCommandExit();
        }
    }

    private function _run(){
        $command = isset($this->options["command"]) ? 
                        $this->options["command"]: false;
        if (!$command) {
            $this->wrongCommandExit();
        }
        switch ($command) {
            case "db_create":
                $db = new DB();
                $db->create();
                break;

            case "create":
                $project = new Project();
                $pId = $project->create();
                echo "next: php iki.php --command=areas --pid=" . $pId . PHP_EOL;
                break;

            case "areas":
                $project = new Project();
                $project->process();
                echo "next: php iki.php --command=build --pid=" . $project->getPid() . PHP_EOL;                
                break;

            case "build":
                $builder = new ProjectBuilder();
                $fileName = $builder->process();
                echo "next: open \"" . $fileName . "\"" . PHP_EOL;
                break;

            default:
                $this->wrongCommandExit();        
        }
    }

    private function wrongCommandExit(){
        die ($this->getHelpText());
    }

    private function getOptions(){
        return [
            "command:",
        ];
    }

    private function getHelpText(){
        $help = [
            "--command:",
            "\tdb_create - create db",            
            "\tcreate - create project",
            "\tareas - find all similar areas",
            "\tbuild - build mosaic",

            PHP_EOL . "--sourcefolder - source folder with images",
            "--thumbsize - thubm size",
            "--thumbquality - 0 ... 100",

            PHP_EOL . "--image - path to big image"
        ];

        return PHP_EOL . implode($help, PHP_EOL) . PHP_EOL . PHP_EOL;
    }
}