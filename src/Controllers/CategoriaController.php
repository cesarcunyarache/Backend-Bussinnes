<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;

use App\Config\Message;
use App\Models\CategoriaModel;
use Resend\Collection;

class CategoriaController extends Controller
{
    final public function getRead(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            try {
                $data = CategoriaModel::read();
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
                $data = CategoriaModel::getCategoriaById($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
    final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            if (
                empty($this->getParam()['categoria']) || empty($this->getParam()['descripcion']) ||
                !isset($this->getParam()['estado'])
            ) {
                echo ResponseHttp::status400('Uno o más campos vacíos');
            } else {
                try {
                    if (!empty($_FILES) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                        $imagenUrl = Security::uploadImage($_FILES, 'imagen', 'public/Images/categorias/')['path'];

                        $obj = new CategoriaModel($this->getParam(), $imagenUrl);
                    } else {
                        $obj = new CategoriaModel($this->getParam(), "");
                    }
                    $res = $obj::create();

                    if ($res > 0) {
                        echo ResponseHttp::status200("Creado satisfactoriamente");
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                } catch (\Exception $e) {
                    echo ResponseHttp::status500($e->getMessage());
                }
            }
            exit;
        }
    }

    final public function postUpdate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            if (
                !isset($this->getParam()['idCategoria'])  ||
                !isset($this->getParam()['descripcion']) ||
                !isset($this->getParam()['estado']) || 
                !isset($this->getParam()['categoria']) 
            ) {
               
               
                echo ResponseHttp::status400('Uno o más campos vacios');
            } else {   
                    try {
                        $res = false;
                        if (!empty($_FILES)) { 
                            $obj = new CategoriaModel($this->getParam(), $_FILES);
                            $obj::putUpdate($this->getParam()['idCategoria']);
                            $res = $obj::putUpdateImage($this->getParam()['idCategoria']);
                        } else {
                        
                           
                            $obj = new CategoriaModel($this->getParam(), '');
                      
                            
                            $res = $obj::putUpdate( (int) $this->getParam()['idCategoria']);    
                            
                        }
                        
                        if ($res > 0) {
                            echo ResponseHttp::status200("Actualizado satisfactoriamente");
                        } else {
                            echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                        }
                    } catch (\Exception $e) {
                        echo ResponseHttp::status500($e->getMessage());
                    }
                
            }
            exit;
        }
    }
}
