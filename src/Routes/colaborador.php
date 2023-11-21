<?php

use App\Config\ResponseHttp;
use App\Controllers\ColaboradorController;

$params  = explode('/', $_GET['route']);

$app = new ColaboradorController();

$app->postCreate("colaborador/");

$app->putUpdate("colaborador/");

$app->getRead("colaborador/");

$app->getReadById("colaborador/{$params[1]}");

echo ResponseHttp::status404();
