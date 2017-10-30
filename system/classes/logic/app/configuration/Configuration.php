<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\app\configuration;

class Configuration {

    private static $_instance;

    public static function getInstance(){
        if (!self::$_instance)
            self::$_instance = new self();
        return self::$_instance;
    }

    private function __construct(){
        $this->options = getopt("", $this->getOptions());
        $configFile = PROJECT_DIR . "config.ini";
        $this->default_options = [];
        if (is_file($configFile))
            $this->default_options = parse_ini_file($configFile);
    }

    public function getOptions(){
        return [
            // common
            "datafolder:",

            // areas
            "minsize::",
            "maxsize::",
            "minpercent::",
            "image::",

            // mosaic builder
            "pid::",
            "colorize::",
            "useoriginal::",
            "diversity::",
            "factor::",

            // db builder
            "sourcefolder::",
            "thumbsize::",
            "thumbquality::"
        ];
    }

    private function getValue($option, $inCofig = true) {
        return !empty($this->options[$option]) ? 
                        $this->options[$option]: 
                            ($inCofig && !empty($this->default_options[$option])? $this->default_options[$option] : null);
    }

    public function getMinSize(){
        return $this->getValue("minsize");
    }

    public function getMaxSize(){
        return $this->getValue("maxsize");
    }
    
    public function getMinPercent(){
        return $this->getValue("minpercent");
    }
 
    public function getSourceFolder(){
        return $this->getValue("sourcefolder", false);
    }
     
    public function getThumbSize(){
        return $this->getValue("thumbsize");
    }     

    public function getThumbQuality(){
        return $this->getValue("thumbquality");
    }

    public function getPid(){
        return $this->getValue("pid", false);
    }

    public function getColorize(){
        return $this->getValue("colorize");
    }

    public function getUseOriginal(){
        return $this->getValue("useoriginal");
    }

    public function getDiversity(){
        return $this->getValue("diversity");
    }

    public function getFactor(){
        return $this->getValue("factor");
    }

    public function getDataFolder(){
        return $this->getValue("datafolder");
    }

    public function getImage(){
        return $this->getValue("image");
    }

}