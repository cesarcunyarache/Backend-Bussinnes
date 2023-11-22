<?php

use App\Config\ResponseHttp;
use App\Controllers\ReservaController;

$params  = explode('/', $_GET['route']);

$app = new ReservaController();

$app->postStatusMesas("reserva/mesas");

$app->postCreate("reserva/");

echo ResponseHttp::status404();
