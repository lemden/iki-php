<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\images;

use logic\process\ConsoleLogger;
use logic\images\ListOfFilesLoader;
use logic\images\PhotoUtils;

class DBMaker {

    /**
     * @var ConsoleLogger
     */
    private $logger;

    /**
     * @var string
     */
    private $sourceDir;

    public function __construct($sourceDir, $dataDir, ConsoleLogger $logger = null){
        if (!is_dir($sourceDir))
            throw new \Exception("Cannot find source folder. This is not a dir: \"" . $sourceDir . '"');
        if (!is_dir($dataDir)) {
            if (!mkdir($dataDir))
                throw new \Exception("Cannot find or create data folder. This is not a dir: \"" . $dataDir . '"');
        }
           
        $this->dataDir = $dataDir;
        $this->sourceDir = $sourceDir;
        $this->logger = $logger;
        $this->images = [];

        $this->libDir = $this->dataDir . "imagelib/";
        $this->fileNamesIndexes = [];
    }

    private function log($message){
        if ($this->logger) {
            $this->logger->log($message);
        }
    }


    private function areSettingsTheSame($originalSettings, $newSettings){
        if (!$originalSettings)
            return null;
        return $originalSettings->thumbsize === $newSettings->thumbsize
                    &&
                $originalSettings->quality === $newSettings->quality;
    }

    public function create($thumbSize = 400, $quality = 60){
        $originalSettings = null;
        if (!is_dir($this->libDir)) {
            mkdir($this->libDir);
            $db_file = (object) ['images' => (object) []];
        } else {
            if (is_file($this->getDBFileName())) {
                $db_file = json_decode(
                    file_get_contents($this->getDBFileName())
                );
                $originalSettings = $db_file->settings;
            } else {
                $db_file = (object) ['images' => (object) []];
            }
        }

        $db_file->settings = (object) [
            'thumbsize' => $thumbSize,
            'quality' => $quality
        ];

        $areSettingsTheSame = $this->areSettingsTheSame($originalSettings, 
                                                $db_file->settings);

        $listOfFilesLoader = new ListOfFilesLoader($this->sourceDir, $this->logger);
        $images = $listOfFilesLoader->getListOfImages();
       

        foreach ($images as $idx => $image) {
            $fileNameHash = '_' . sha1($image);
            
            if (isset($db_file->images->$fileNameHash)
                        && $areSettingsTheSame) {
                continue;
            }

            $sourceOfImage = new \Imagick($image);
            $scaledAndCroppedImage = PhotoUtils::createThumb($sourceOfImage, $thumbSize);
            if ($scaledAndCroppedImage) {
                
                $avgColor = PhotoUtils::getPhotoColor($scaledAndCroppedImage)
                                            ->getColor();
                $fileName = $this->libDir . implode("_", [sha1($image),
                                "size",
                                $thumbSize,
                                "rgb", $avgColor["r"], 
                                $avgColor["g"], 
                                $avgColor["b"]]) . ".jpg";
                
                $scaledAndCroppedImage->setImageCompressionQuality($quality);
                $scaledAndCroppedImage->setImageFormat("jpeg");
                $scaledAndCroppedImage->writeImage($fileName);
                $scaledAndCroppedImage->destroy();

                $percent = $idx / count($images) * 100;

                $image_info = [
                    "color" => $avgColor,
                    "file" => realpath($fileName),
                    "size" => $thumbSize
                ];

                $db_file->images->$fileNameHash=$image_info;

                $this->log("DONE: " . round($percent, 2) . "%");
            }

            if (count($db_file->images) % 10) {
                $this->saveDBFile($db_file);
            }
        }

        $this->saveDBFile($db_file);
    }

    private function getDBFileName(){
        return $this->libDir . "db.json";
    }

    private function saveDBFile($db_file){
        $db_filename = $this->getDBFileName();
        file_put_contents($db_filename, json_encode($db_file));
    }
}