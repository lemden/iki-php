<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app\configuration;

abstract class AppConfigBase{
    protected function getPercent($value){
        if ($value < 0)
            return 0;
        else if ($value > 100)
            return 100;
        else
            return intval($value);
    }
}