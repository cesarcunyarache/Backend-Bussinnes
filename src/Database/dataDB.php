<?php

use App\Config\ErrorLog;
use App\Config\ResponseHttp;
use App\Database\Connection;

ErrorLog::activateErrorLog();

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__,2));
$dotenv->load();


$data = array(
    'serverDB' => $_ENV['SERVER_DB'],
    'user'     => $_ENV['USER'],
    'password' => $_ENV['PASSWORD'],
    'DB'       => $_ENV['DB'],
    'IP'       => $_ENV['IP'],
    'port'     => $_ENV['PORT']
);

if(empty($data['serverDB']) || empty($data['user']) /* || empty($data['password']) */ || 
   empty($data['DB']) || empty($data['IP']) || empty($data['port']) ){ 
       error_log('Campos de la DB vacios');
       die(json_encode(ResponseHttp::status500('Campos de la DB vacios')));
} else if(strtolower($data['serverDB']) === 'mysql') {
    $user     = $data['user'];
    $password = $data['password'];
    $db       = $data['DB'];
    $ip       = $data['IP'];
    $port     = $data['port'];
    $host     = 'mysql:host='.$ip.";".'port='.$port.';dbname='.$db;    
    $connection = Connection::from($host , $user , $password);
} else if(strtolower($data['serverDB']) === 'sqlserver') {
    $user     = $data['user'];
    $password = $data['password'];
    $db       = $data['DB'];
    $ip       = $data['IP'];
    $port     = $data['port'];                                 
    $host     = 'sqlsrv:server='.$ip.','.$port.';database='.$db;
    $connection = Connection::from($host , $user , $password);
}