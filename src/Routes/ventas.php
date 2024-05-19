<?php

use App\Config\ResponseHttp;
use App\Controllers\VentasController;

require './vendor/autoload.php';

if (!isset($_GET['route'])) {
    echo ResponseHttp::status400('No se especificó ninguna ruta');
    exit();
}

$params = explode('/', $_GET['route']);
$controller = $params[0] ?? '';
$id = $params[1] ?? null;

$ventasController = new VentasController();

try {
    switch ($controller) {
        case 'ventas':
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'POST':
                    $ventasController->postCreate("ventas/");
                    break;
                case 'GET':
                    if ($id) {
                        $ventasController->getReadById("ventas/{$id}");
                    } else {
                        $ventasController->getRead("ventas/");
                    }
                    break;
            }
            break;
        default:
            echo ResponseHttp::status404('Ruta no encontrada');
            break;
    }
} catch (\Exception $e) {
    echo ResponseHttp::status500($e->getMessage());
}
