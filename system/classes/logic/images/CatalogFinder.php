<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\images;

use logic\process\ConsoleLogger;
use logic\images\ListOfFilesLoader;
use logic\images\PhotoUtils;

class CatalogFinder { 

    public function __construct(PhotoArea $area, $diversity, $datafolder){
        $this->area = $area;
        $this->datafolder = $datafolder;
        $this->dbFileName = $datafolder . "imagelib/db.json";
        $this->diversity = $diversity;
        $_catalog = json_decode(file_get_contents($this->dbFileName));
        $this->catalog = $_catalog->images;
    }

    private function getSortedByDeltaImages() {
        $result = [];
        foreach ($this->catalog as $idx => $catalogImage) {
            $catalogImageColor = [
                    'r' => $catalogImage->color->r,
                    'g' => $catalogImage->color->g,
                    'b' => $catalogImage->color->b
            ];
            $delta = PhotoUtils::getColorDeltaByArray(
                $this->area->getAverageColor(), $catalogImageColor
            );

            $catalogImageResult = [
                'index' => $idx,
                'delta' => $delta,
                'image' => $catalogImage
            ];
            
            $result []= $catalogImageResult;
        }
        usort(
            $result, function ($r1, $r2) {
                if ($r1['delta'] > $r2['delta'])
                    return 1;
                else if ($r1['delta'] < $r2['delta'])
                    return -1;    
                else
                    return 0;
            }
        );

        return $result;
    }

    public function getImage() {
        $sorted = $this->getSortedByDeltaImages();
        if (!$this->diversity) {
            return $sorted[0]; // min delta
        } else {
            $portion = array_slice($sorted, 0, floor((count($sorted) - 1) * $this->diversity / 100));
            $index = mt_rand(0, count($portion) - 1);
            return $portion[$index];
        }
    }
}