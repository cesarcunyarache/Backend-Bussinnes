<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;

use App\Config\Message;
use App\Models\ClienteModel;
use App\Models\ColaboradorModel;
use Resend\Collection;

class ColaboradorController extends Controller
{
    final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'idTipoDoc'            => 'required|numeric|in:1,2,3,4',
                'numeroDoc'            => 'required|',
                'nombres'              => 'required|alpha_spaces',
                'apellidos'            => 'required|alpha_spaces',
                'telefono'             => 'required|digits:9',
                'fechaNacimiento'      => 'required|date:Y-m-d',
                'genero'               => 'required',
                'direccion'            => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                Security::validateTokenJwt(Security::secretKey());
                /* $idClient = $data->data->idCliente; */

                $idTipoDoc = $this->getParam()['idTipoDoc'];
                $numeroDoc = $this->getParam()['numeroDoc'];
                $data = Security::SchemaValidation( (int) $idTipoDoc, $numeroDoc);

                if ($data['isValidate']) {
                    $colaborador = new ColaboradorModel($this->getParam());
                    $res = $colaborador::create();
                    if ($res > 0) {
                        echo ResponseHttp::status200('Creado satisfactoriamente');
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                } else {
                    echo ResponseHttp::status400($data['message']);
                }
            }

            exit;
        }
    }

    final public function putUpdate(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'idTipoDoc'            => 'required|numeric|in:1,2,3,4',
                'numeroDoc'            => 'required',
                'nombres'              => 'required|alpha_spaces',
                'apellidos'            => 'required|alpha_spaces',
                'telefono'             => 'required|digits:9',
                'fechaNacimiento'      => 'required|date:Y-m-d',
                'genero'               => 'required',
                'direccion'            => 'required',
                'id'                   => 'required'
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {

                /* Security::validateTokenJwt(Security::secretKey()); */
                /*$idClient = $data->data->idCliente; */

                $id = $this->getParam()['id'];
                $idTipoDoc = $this->getParam()['idTipoDoc'];
                $numeroDoc = $this->getParam()['numeroDoc'];
                $data = Security::SchemaValidation( (int) $idTipoDoc, $numeroDoc);

                if ($data['isValidate']) {
                    $colaborador = new ColaboradorModel($this->getParam());
                    $colaborador::setId((int) $id);
                    $res = $colaborador::update();
                    if ($res) {
                        echo ResponseHttp::status200('Actualizado satisfactoriamente');
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                } else {
                    echo ResponseHttp::status400($data['message']);
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

                $data = ColaboradorModel::read();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function getReadNoMesero(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {

                /*   Security::validateTokenJwt(Security::secretKey()); */

                $data = ColaboradorModel::readNoMesero();
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
                $data = ColaboradorModel::getColaborador($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
}
