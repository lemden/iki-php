<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

namespace logic\process;

class ConsoleProgressCallback implements ProgressCallback
{
    private $name;

    private $percent;

    public function __construct($name = '')
    {
        $this->name = $name;
    }

    function trigger($percent)
    {
        if ($this->percent != $percent) {
            $this->percent = $percent;
            $message = $this->name . '[' . date('H:i:s') . "]> DONE: {$percent}%" . PHP_EOL;
            echo $message;
        }
    }
}