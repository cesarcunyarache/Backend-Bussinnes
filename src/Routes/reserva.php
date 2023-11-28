<?php

use App\Config\ResponseHttp;
use App\Controllers\ReservaController;

$params  = explode('/', $_GET['route']);

$app = new ReservaController();

$app->postStatusMesas("reserva/mesas");

$app->postCreate("reserva/");

$app->getRead("reserva/");

$app->getRead("reserva/");

$app->getReadById("reserva/{$params[1]}");

echo ResponseHttp::status404();
