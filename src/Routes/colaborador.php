<?php

use App\Config\ResponseHttp;
use App\Controllers\ColaboradorController;

$params  = explode('/', $_GET['route']);

$app = new ColaboradorController();

$app->postCreate("colaborador/");

$app->getRead("colaborador/");

echo ResponseHttp::status404();
