<?php

use App\Config\ErrorLog;
use App\Config\ResponseHttp;

require './vendor/autoload.php';

ResponseHttp::headerHttpPro($_SERVER['REQUEST_METHOD'],$_SERVER['HTTP_ORIGIN']);//CORS Producción
// ResponseHttp::headerHttpDev($_SERVER['REQUEST_METHOD']); //CORS Desarrollo
ErrorLog::activateErrorLog();

if (isset($_GET['route'])) {

    $params = explode('/', $_GET['route']);
    $list = ['cliente', 'clienteAuth', 'reserva'];
    $file = './src/Routes/' . $params[0] . '.php';

    if (!in_array($params[0], $list)) {
        echo ResponseHttp::status400();
        exit;
    }


    if (is_readable($file)) {
        require $file;
        exit;
    } else {
        echo ResponseHttp::status500('El archivo de la ruta no esta creado');
    }
} else {
    echo ResponseHttp::status500();
    exit;
}
