<?php

use App\Config\ResponseHttp;
use App\Controllers\ClienteUsuarioController;


$params  = explode('/', $_GET['route']);

$app = new ClienteUsuarioController();

$app->postLogin("clienteAuth/login");

$app->postRegister("clienteAuth/register");

$app->postForgetPassword("clienteAuth/forgetPassword");

$app->getProfile("clienteAuth/profile");

$app->getLogout("clienteAuth/logout");

$app->getVerify("clienteAuth/verify");


echo json_encode(ResponseHttp::status404());
