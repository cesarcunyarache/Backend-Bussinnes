<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;
use App\Config\Message;
use App\Models\ProductoModel;
use App\Models\MeseroModel;
use App\Models\Model;

use function PHPSTORM_META\type;

class ProductoController extends Controller
{
    final public function getRead(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            try {
                $data = ProductoModel::read();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    /*final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            if (
                empty($this->getParam()['nombre']) || empty($this->getParam()['descripcion']) ||
                !isset($this->getParam()['precio']) || !isset($this->getParam()['estado']) || !isset($this->getParam()['idCategoria'])
            ) {
                echo ResponseHttp::status400('Uno o más campos vacios');
            } else {
                    try {

                        if (!empty($_FILES) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                            $imagenUrl = Security::uploadImage($_FILES['imagen'], 'imagen')['path'];
                            $obj = new ProductoModel($this->getParam(), $imagenUrl);
                        } else {
                            $obj = new ProductoModel($this->getParam(), "");
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
            }
            exit;
        }*/
    final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            if (
                !isset($this->getParam()['nombre']) || !isset($this->getParam()['descripcion']) ||
                !isset($this->getParam()['precio']) || !isset($this->getParam()['estado']) || !isset($this->getParam()['idCategoria'])
            ) {
                echo ResponseHttp::status400('Uno o más campos vacíos');
            } else {
                try {

                    if (!empty($_FILES)) {
                        $imagenUrl = Security::uploadImage($_FILES, 'imagen', 'public/Images/productos/')['path'];
                        $obj = new ProductoModel($this->getParam(), $imagenUrl);
                    } else {
                        $obj = new ProductoModel($this->getParam(), null);
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
                !isset($this->getParam()['nombre']) || 
                !isset($this->getParam()['descripcion']) ||
                !isset($this->getParam()['precio']) || 
                !isset($this->getParam()['estado']) || 
                !isset($this->getParam()['idCategoria']) || 
                !isset($this->getParam()['nombre'])
            ) {
                echo ResponseHttp::status400('Uno o más campos vacíos');
            } else {
                try {
                    $res = false;
                    if (!empty($_FILES)) {

                        $obj = new ProductoModel($this->getParam(), $_FILES);
                        $obj::putUpdate($this->getParam()['idCategoria']);
                        $res = $obj::putUpdateImage($this->getParam()['idProducto']);

                    } else {
                        $obj = new ProductoModel($this->getParam(), '');
                        $res = $obj::putUpdate((int) $this->getParam()['idProducto']);
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




    final public function postUpdateProducto(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            if (
                empty($this->getParam()['idProducto']) ||
                !isset($this->getParam()['nombre']) ||
                !isset($this->getParam()['estado']) || !isset($this->getParam()['precio']) || !isset($this->getParam()['costoPuntos'])
            ) {
                echo ResponseHttp::status400('Uno o más campos vacios');
            } else {
                if (empty($_FILES)) {
                    echo ResponseHttp::status400('Archivo vacio o nombre incorrecto');
                } else {

                    try {

                        $obj = new ProductoModel($this->getParam(), $_FILES);

                        $res = $obj::putUpdateProductos($this->getParam()['idProducto']);

                        if ($res > 0) {
                            echo ResponseHttp::status200("Actualizado satisfactoriamente");
                        } else {
                            echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                        }
                    } catch (\Exception $e) {
                        echo ResponseHttp::status500($e->getMessage());
                    }
                }
            }
            exit;
        }
    }

    final public function postReadMeseroForReserva(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'fecha'      => 'required|date:Y-m-d',
                'hora'       => 'required'
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {

                $fecha = $this->getParam()['fecha'];
                $horaIngresada = $this->getParam()['hora'];

                if (preg_match(/* '/^(?:[01]\d|2[0-3]):[0-5]\d$/' */
                    '/^(0?[1-9]|1[0-2]):[0-5][0-9]$/',
                    $horaIngresada
                )) {
                    $data = MeseroModel::read();
                    $reservas = MeseroModel::getReadMeseroForReserva($fecha, $horaIngresada);

                    if (!empty($reservas)) {
                        foreach ($reservas as $reserva) {
                            if ($reserva['cantidadReservas'] == 3) {
                                $index = array_search($reserva['idMesa'], array_column($data, 'idMesero'));
                                $data[$index]['estado'] = 2;
                            }
                        }
                    }
                    echo ResponseHttp::status200($data);
                } else {
                    echo ResponseHttp::status400('Formato de hora inválido');
                }
            }
            exit;
        }
    }

    final public function getReadById(string $endPoint)
    {
        if ($this->getMethod() == 'get'  && $endPoint == $this->getRoute()) {

            try {
                Security::validateTokenJwt(Security::secretKey());
                $id = $this->getAttribute()[1];
                $data = ProductoModel::getProducto($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
}
