<?php

use App\Config\ResponseHttp;
use App\Controllers\ProductoController;

$params  = explode('/', $_GET['route']);

$app = new ProductoController();

$app->postCreate("producto/");

echo ResponseHttp::status404();
