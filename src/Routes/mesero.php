<?php

use App\Config\ResponseHttp;
use App\Controllers\MeseroController;


$params  = explode('/', $_GET['route']);


$app = new MeseroController();

$app->putUpdate("mesero/");


echo ResponseHttp::status404();







