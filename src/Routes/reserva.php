<?php

use App\Config\ResponseHttp;
use App\Controllers\ReservaController;

$params  = explode('/', $_GET['route']);

$app = new ReservaController();

$app->postStatusMesas("reserva/mesas");

$app->postCreate("reserva/");

$app->getRead("reserva/");

$app->getReadMesas("reserva/mesas");

$app->getReadById("reserva/id/{$params[2]}");

$app->putUpdate("reserva/update");

echo ResponseHttp::status404();
