<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;
use App\Config\Message;
use App\Models\MeseroModel;

class MeseroController extends Controller
{
    final public function putUpdate(string $endPoint)
    {

        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            /* $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'idColaborador'            => 'required|numeric',
                'estado'                   => 'required',
               
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
 *//* 

            $directorio = __DIR__ . "/public/";

            $archivo = $directorio . basename($_FILES["file"]["name"]);

            $tipoArchivo = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));

            // valida que es imagen
            $checarSiImagen = getimagesize($_FILES["file"]["tmp_name"]);

            //var_dump($size);

            if ($checarSiImagen != false) {

                //validando tamaño del archivo
                $size = $_FILES["file"]["size"];

                if ($size > 500000) {
                    echo "El archivo tiene que ser menor a 500kb";
                } else {

                    //validar tipo de imagen
                    if ($tipoArchivo == "jpg" || $tipoArchivo == "jpeg") {
                        // se validó el archivo correctamente
                        echo $archivo;
                        if (move_uploaded_file($_FILES["file"]["tmp_name"], $archivo)) {
                            echo "El archivo se subió correctamente";
                        } else {
                            echo "Hubo un error en la subida del archivo";
                        }
                    } else {
                        echo "Solo se admiten archivos jpg/jpeg";
                    }
                }
            } else {
                echo "El documento no es una imagen";
            }
 */

            $obj = new MeseroModel($this->getParam(), $_FILES);
            /*      print_r($_FILES); */

            $obj::postSave();

            /* $data  = Security::validateTokenJwt(Security::secretKey());
                $idClient = $data->data->idCliente;

                if (isset($idClient) && !empty($idClient)) {
                    $cliente = new ClienteModel($this->getParam());
                    $cliente::setId($idClient);
                    $res = $cliente::Update();
                    if ($res){
                        echo ResponseHttp::status200('Datos actualizados correctamente');
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                }
 */
            /*     } */
            exit;
        }
    }

    final public function pUpdate(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {

            exit;
        }
    }
}
