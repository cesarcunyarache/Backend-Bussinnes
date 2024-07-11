<?php

use App\Config\ResponseHttp;
use App\Controllers\VentasController;


$params  = explode('/', $_GET['route']);

$app = new VentasController();

$app->postCreate("ventas/");
$app->getRead("ventas/");


echo ResponseHttp::status400('No se especificó ninguna ruta');
