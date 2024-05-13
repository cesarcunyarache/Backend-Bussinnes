<?php

use App\Config\ResponseHttp;
use App\Controllers\CategoriaController;

$params  = explode('/', $_GET['route']);

$app = new CategoriaController();

$app->postCreate("categoria/");

//$app->putUpdate("categoria/");

$app->getRead("categoria/");

$app->getReadById("categoria/{$params[1]}");



echo ResponseHttp::status404();