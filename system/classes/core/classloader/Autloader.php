<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace core\classloader;

abstract class Autoloader 
{
    public static function load ($class_name) 
    {
        $tokens = explode ('\\', $class_name);
        $class_path = implode (DIRECTORY_SEPARATOR, $tokens);
        $full_class_path = CLASSES_DIR . $class_path . '.php';
        if (file_exists ($full_class_path)) 
        {
            require_once ($full_class_path);
        }
    }
}