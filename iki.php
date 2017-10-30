<?php
// Copyright 2017 Denys S Lemeshko
// Licensed under the MIT license

ini_set('memory_limit','-1');

use logic\app\App;
require_once dirname(__FILE__) . '/system/bootstrap.php';

$app = new App();
$app->run();