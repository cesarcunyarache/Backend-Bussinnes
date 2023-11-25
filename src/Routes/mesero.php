<?php

use App\Config\ResponseHttp;
use App\Controllers\MeseroController;


$params  = explode('/', $_GET['route']);


$app = new MeseroController();

$app->getRead("mesero/");

$app->postReadMeseroForReserva("mesero/reserva");

$app->postCreate("mesero/");

$app->postUpdate("mesero/update");

$app->getReadById("mesero/{$params[1]}");

echo ResponseHttp::status404();







