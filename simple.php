#!/usr/bin/env php
<?php

include "vendor/autoload.php";

$config = require ROOT_DIR . '/config/config.php';

$app = new \nextcontact\Simple\Core\Console\Application();

$app->setAppDir(dirname(__FILE__));

$app->init($config);

$app->run();
