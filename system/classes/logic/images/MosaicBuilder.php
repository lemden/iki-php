<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\images;


use logic\images\PhotoArea;
use logic\images\CatalogFinder;
use logic\process\ConsoleLogger;


class MosaicBuilder {

    public function __construct($pId, $dataFolder, $colorize, $useOriginal, $diversity, $factor = 1, ConsoleLogger $logger = null){

        $this->pId = $pId;
        $this->colorize = $colorize;
        $this->dataFolder = $dataFolder;
        $this->useOriginal = $useOriginal;
        $this->diversity = $diversity;
        $this->factor = $factor;
        $this->projectDir = $this->dataFolder . $this->pId . "/";
        $this->originalImage = $this->projectDir . "image.jpg";
        $this->areasFileName = $this->projectDir . "areas.json";

        $imageSize = getimagesize($this->originalImage);
        $this->originalImageHandler = null;

        $this->originalImageSize = ['width' => $imageSize[0], 'height' => $imageSize[1]];
        $this->imageSize = ['width' => $imageSize[0] * $this->factor, 'height' => $imageSize[1] * $this->factor];
        $this->resultFileName = $this->projectDir 
                                    . "result_colorize_" 
                                    . $colorize 
                                    . "_use_original_" . $useOriginal 
                                    . "_diversity_" 
                                    . $this->diversity 
                                    . "_factor_" 
                                    . $this->factor . "_.jpg";

        $this->logger = $logger;
        
        $this->result = imagecreatetruecolor(
            $this->imageSize["width"],
            $this->imageSize["height"]
        );
    }

    private function log($message){
        if ($this->logger) {
            $this->logger->log($message);
        }
    }

    private function parseAreas(){
        $areas = json_decode(
            file_get_contents($this->areasFileName)
        );
        $result = [];
        foreach ($areas as $index => $_area) {
            
            $area = new PhotoArea();
            $area->setIndex($index)
                  ->setX($_area->x)
                  ->setY($_area->y)
                  ->setSize($_area->size)
                  ->setAverageColorArr([
                      'r' => $_area->color->r,
                      'g' => $_area->color->g,
                      'b' => $_area->color->b,
                      'a' => $_area->color->a,
                    ]);
            $result []= $area;
        }
        $this->areas = $result;
    }

    public function build(){
        $this->parseAreas();

        foreach ($this->areas as $idx => $area) {
            $catalogFinder = new CatalogFinder($area, $this->diversity,
                                    $this->dataFolder);
            $foundImage = $catalogFinder->getImage();
            $this->addImageToTheResult($area, $foundImage);

            $percent = round($idx / count($this->areas) * 100, 2);
            $this->log("DONE: " . $percent . "%");
        }
        if ($this->useOriginal) {
            $originalImageHandler = imagecreatefromjpeg(
                $this->originalImage
            );
            $scaledImage = imagecreatetruecolor(
                $this->imageSize["width"],
                $this->imageSize["height"]
            );

            imagecopyresized($scaledImage, $originalImageHandler, 0, 0, 0, 0, 
                                    $this->imageSize["width"],
                                    $this->imageSize["height"], $this->originalImageSize['width'], 
                                    $this->originalImageSize['height']);
            imagedestroy($originalImageHandler);
            imagecopymerge($this->result, 
                $scaledImage, 0, 0, 0, 0, 
                $this->imageSize['width'] * $this->factor,
                $this->imageSize['height'] * $this->factor,
                $this->useOriginal);
            imagedestroy($scaledImage);
        }

        imagejpeg($this->result, $this->resultFileName, 80);
    }

    public function getResultFileName(){
        return $this->resultFileName;
    }

    private function addImageToTheResult($area, $foundImage){
        $colorImage = imagecreatetruecolor($foundImage["image"]->size, $foundImage["image"]->size);
        imagefill($colorImage,0,0,imagecolorallocate($colorImage, $area->getR(), $area->getG(), $area->getB()));
        
        $smallImageFileHandler = imagecreatefromjpeg($foundImage["image"]->file);
        imagecopymerge($smallImageFileHandler, 
            $colorImage, 0, 0, 0, 0, 
            $foundImage["image"]->size, 
            $foundImage["image"]->size, 
            $this->colorize);

        imagecopyresized($this->result, $smallImageFileHandler, 
                    $area->getX() * $this->factor, $area->getY() * $this->factor, 0, 0, 
                    $area->getSize() * $this->factor, $area->getSize() * $this->factor, 
                    $foundImage["image"]->size, 
                    $foundImage["image"]->size);

        imagedestroy($smallImageFileHandler);
        imagedestroy($colorImage);
    }
}