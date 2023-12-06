<?php

use App\Config\ResponseHttp;
use App\Controllers\ReservaController;

$params  = explode('/', $_GET['route']);

$app = new ReservaController();

$app->postStatusMesas("reserva/mesas");

$app->postCreate("reserva/");

$app->postPago("reserva/pago");

$app->getRead("reserva/");

$app->getReadMesas("reserva/mesas");

$app->getReadById("reserva/id/{$params[2]}");

$app->putUpdate("reserva/update");

$app->getReservasTotal("reserva/total");

$app->getTotales("reserva/totalRCC");

$app->getMesasOcupadas("reserva/mesasOcupadas");

$app->getReadByIdUser("reserva/{$params[1]}");

$app->getReadPuntosByIdUser("reserva/puntos/{$params[2]}");

echo ResponseHttp::status404();
