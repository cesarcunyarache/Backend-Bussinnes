<?php

use App\Config\ResponseHttp;
use App\Controllers\ClienteController;


$params  = explode('/', $_GET['route']);

$app = new ClienteController();

$app->putUpdate("cliente/");

$app->getRead("cliente/");




echo ResponseHttp::status404();







