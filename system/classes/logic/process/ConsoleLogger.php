<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\process;

class ConsoleLogger {

    public function log($message) {
        echo "[" . date("Y-m-d H:i:s") . "]> " . $message . PHP_EOL;
    }

}