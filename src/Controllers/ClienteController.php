<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;;

use App\Config\Message;
use App\Models\ClienteModel;
use \Resend;

use function PHPSTORM_META\type;

class ClienteController extends Controller
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
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                Security::validateTokenJwt(Security::secretKey());

                $idTipoDoc = $this->getParam()['idTipoDoc'];
                $numeroDoc = $this->getParam()['numeroDoc'];
                $data = Security::SchemaValidation((int) $idTipoDoc, $numeroDoc);

                if ($data['isValidate']) {
                    $colaborador = new ClienteModel($this->getParam());
                    $res = $colaborador::createfullParams();
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


    final public function postCreatePatch(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'idTipoDoc'            => 'required|numeric|in:1,2,3,4',
                'numeroDoc'            => 'required|',
                'nombres'              => 'required|alpha_spaces',
                'apellidos'            => 'required|alpha_spaces',

            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                Security::validateTokenJwt(Security::secretKey());

                $idTipoDoc = $this->getParam()['idTipoDoc'];
                $numeroDoc = $this->getParam()['numeroDoc'];
                $nombres = $this->getParam()['nombres'];
                $apellidos = $this->getParam()['apellidos'];
                $data = Security::SchemaValidation((int) $idTipoDoc, $numeroDoc);

                if ($data['isValidate']) {

                    $res = ClienteModel::createPatchParams($idTipoDoc, $numeroDoc, $nombres, $apellidos);
                    if ($res > 0) {
                        echo ResponseHttp::status200(['idCliente' => $res]);
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
                'idCliente'             => 'required|numeric',
                'idTipoDoc'            => 'required|numeric',
                'numeroDoc'            => 'required',
                'nombres'              => 'required|alpha_spaces',
                'apellidos'            => 'required|alpha_spaces',
                'telefono'             => 'required',
                'fechaNacimiento'      => 'required|date:Y-m-d',
                'genero'               => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {

                Security::validateTokenJwt(Security::secretKey());

                $idClient = $this->getParam()['idCliente'];

                if (isset($idClient) && !empty($idClient)) {
                    $cliente = new ClienteModel($this->getParam());
                    $cliente::setId($idClient);
                    $res = $cliente::Update();
                    if ($res) {
                        echo ResponseHttp::status200('Datos actualizados correctamente');
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

                $data = ClienteModel::read();
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
                $data = ClienteModel::getSearchClienteById($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function getClienteByNumeroDoc(string $endPoint)
    {
        if ($this->getMethod() == 'get'  && $endPoint == $this->getRoute()) {

            try {
                Security::validateTokenJwt(Security::secretKey());
                $numeroDoc = $this->getAttribute()[2];
                $data = ClienteModel::getSearchClienteByNumeroDoc($numeroDoc);

                if (count($data) > 0) {
                    echo ResponseHttp::status200($data);
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function postClientByNumeroDoc(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'numeroDoc'            => 'required|',
                'idTipoDoc'            => 'required|'
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                Security::validateTokenJwt(Security::secretKey());
                $numeroDoc = $this->getParam()['numeroDoc'];
                $idTipoDoc = (int) $this->getParam()['idTipoDoc'];
                $data = ClienteModel::getSearchClienteByNumeroDoc($numeroDoc);


                if (count($data)  === 0) {

                    if ($idTipoDoc === 1 or $idTipoDoc === 4) {
                        $value =  $idTipoDoc === 1 ? "dni" : "ruc";
                        $url = "https://dniruc.apisperu.com/api/v1/$value/$numeroDoc?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6ImNlc2FyY3VueWFyYWNoZWNhc3RpbG9AZ21haWwuY29tIn0.6CnvBomU_obgdAGrrN_EVB4GHjdlVpm6dGaPxGit-ws";

                        $response = file_get_contents($url);

                        if ($response === FALSE) {
                            echo ResponseHttp::status400("El cliente no se encuentra registrado Y/O recuperacion de informacion no exitosa");
                        } else {
                            $resApi = json_decode($response, true);

                            if ($resApi['success'] === true) {
                                $result = [
                                    'numeroDoc' => $resApi['dni'],
                                    'nombres' =>  mb_convert_case($resApi['nombres'], MB_CASE_TITLE, "UTF-8"),
                                    'apellidos' =>  mb_convert_case($resApi['apellidoPaterno'] . ' ' . $resApi['apellidoMaterno'],  MB_CASE_TITLE, "UTF-8"),
                                    'idTipoDoc' => $idTipoDoc,
                                    'isNew' => true
                                ];

                                echo ResponseHttp::status200($result);
                            } else {
                                echo ResponseHttp::status400($resApi['message']);
                            }
                        }
                    } else {
                        echo ResponseHttp::status400("El cliente no se encuentra registrado Y/O recuperacion de información no exitosa");
                    }
                } else {

                    echo ResponseHttp::status200($data);
                }
            }

            exit;
        }
    }
}
