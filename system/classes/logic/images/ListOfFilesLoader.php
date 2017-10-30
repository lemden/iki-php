<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\images;

use logic\process\ConsoleLogger;

class ListOfFilesLoader {

    /**
     * @var ProgressCallback
     */
    private $logger;

    /**
     * @var string
     */
    private $sourceDir;

    public function __construct($sourceDir, ConsoleLogger $logger){
        if (!is_dir($sourceDir))
            throw new \Exception("This is not a dir:", $sourceDir);
        $this->sourceDir = $sourceDir;
        $this->logger = $logger;
        $this->images = [];
    }

    private function log($message){
        if ($this->logger) {
            $this->logger->log($message);
        }
    }

    public function getListOfImages(){
        $this->log("Parsing folder:" . $this->sourceDir);
        $images = $this->_getListOfImages();
        $this->log("Found: " . count($images) . " images");  
        return $images;
    }

    private function _getListOfImages($folder = null){
        if (!$folder) {
            $folder = $this->sourceDir;
        }
        if (!is_dir($folder)) {
            return;
        }

        $files = scandir($folder);
        foreach($files as $file) {
            if (!in_array($file, [".", ".."])) {
                $fullPath = $folder . "/" . $file;
                
                if (is_dir($fullPath)) {
                    $this->_getListOfImages($fullPath);
                } else if ($this->canAddToList($fullPath)) {
                    $this->images[]=$fullPath;
                }
            }
        }

        return $this->images;
    }

    private function canAddToList($fileName) {
        $template = "/\.(jpg|jpeg)$/i";
        return preg_match($template, $fileName);
    }
}