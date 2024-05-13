<?php

use App\Config\ResponseHttp;
use App\Controllers\ProductoController;

$params  = explode('/', $_GET['route']);

$app = new ProductoController();

$app->postCreate("producto/");

$app->getRead("producto/");

$app->postUpdateProducto("producto/update");

$app->getReadById("producto/{$params[1]}");

echo ResponseHttp::status404();
