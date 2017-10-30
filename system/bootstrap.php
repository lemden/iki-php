<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

use core\classloader\Autoloader;

define ('PROJECT_DIR', dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR);
define ('SYSTEM_DIR', PROJECT_DIR . 'system' . DIRECTORY_SEPARATOR);
define ('CLASSES_DIR', SYSTEM_DIR . 'classes' . DIRECTORY_SEPARATOR);

// 1. SET ERROR REPORTING
ini_set('display_errors', 'on');
error_reporting(E_ALL);

// 2. SET CLASS LOADER
require_once  CLASSES_DIR . "core/classloader/Autloader.php";
spl_autoload_register(function ($class_name) {
    Autoloader::load($class_name);
});