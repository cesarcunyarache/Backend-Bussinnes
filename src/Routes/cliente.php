<?php

use App\Config\ResponseHttp;
use App\Controllers\ClienteController;


$params  = explode('/', $_GET['route']);

$app = new ClienteController();

$app->postCreate("cliente/");
$app->postCreatePatch("cliente/patch/");
$app->putUpdate("cliente/");
$app->getRead("cliente/");
$app->getReadById("cliente/{$params[1]}");
$app->postClientByNumeroDoc("cliente/numero-doc/");

echo ResponseHttp::status404();







