<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;
use App\Config\Message;
use App\Models\MeseroModel;



class MeseroController extends Controller
{
    final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            if (
                empty($this->getParam()['idColaborador']) ||
                !isset($this->getParam()['estado'])
            ) {
                echo ResponseHttp::status400('Uno o más campos vacios');
            } else {
                if (empty($_FILES)) {
                    echo ResponseHttp::status400('Archivo vacio o nombre incorrecto');
                } else {
                    $obj = new MeseroModel($this->getParam(), $_FILES);
                    $obj::setIdColaborador($this->getParam()['idColaborador']);
                    $res = $obj::postSave();

                    if ($res > 0) {
                        echo ResponseHttp::status200("Creado satisfactoriamente");
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                }
            }
            exit;
        }
    }


    final public function postUpdate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {

            if (
                empty($this->getParam()['idMesero']) ||
                !isset($this->getParam()['estado'])
            ) {

                echo ResponseHttp::status400('Uno o más campos vacios');
            } else {

                if (empty($_FILES)) {
                    echo ResponseHttp::status400('Archivo vacio o nombre incorrecto');
                } else {
                    $obj = new MeseroModel($this->getParam(), $_FILES);
                    $res = $obj::putUpdate($this->getParam()['idMesero']);

           
                    if ($res) {
                        echo ResponseHttp::status200("Actualizado satisfactoriamente");
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                }
            }
            exit;
        }
    }


    final public function getRead(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {

                /*   Security::validateTokenJwt(Security::secretKey()); */

                $data = MeseroModel::read();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }


    final public function getReadById(string $endPoint)
    {
        if ($this->getMethod() == 'get'  && $endPoint == $this->getRoute()) {

            try {
                Security::validateTokenJwt(Security::secretKey());
                $id = $this->getAttribute()[1];
                $data =  MeseroModel::getColaborador($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
}
