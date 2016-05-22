<?php
use MWPackagist\Repository;

require_once __DIR__.'/vendor/autoload.php';
//header('Content-Type: application/json');

$repo = new Repository();
echo $repo->getJSON();
