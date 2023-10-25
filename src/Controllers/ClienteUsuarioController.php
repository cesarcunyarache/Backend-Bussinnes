<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;;

use App\Config\Message;
use App\Models\ClienteUsuarioModel;
use App\Models\ClienteModel;
use \Resend;



class ClienteUsuarioController extends Controller
{

    final public function postLogin(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {

            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'correo'               => 'required|email',
                'contrasena'           => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                $user = new ClienteUsuarioModel($this->getParam());
                $data = $user::login();

                if (count($data) > 0) {
                    $res = ClienteModel::getClientByIdUser($data['id']);

                    if (count($res) > 0) {
                        $payload = ['idCliente' => $res['id']];
                        $token = Security::createTokenJwt(Security::secretKey(), $payload);

                        setcookie("token", $token, time() + (60 * 60 * 6), "/");
                        echo json_encode($res);
                    }
                }
            }
            exit();
        }
    }

    final public function postRegister(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;

            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'nombres'              => 'required|alpha_spaces',
                'apellidos'            => 'required|alpha_spaces',
                'correo'               => 'required|email',
                'contrasena'           => 'required|min:6',
                'confirmContrasena'    => 'required',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                if ($this->getParam()['contrasena'] === $this->getParam()['confirmContrasena']) {
                    $arrUser = [
                        'correo'      => $this->getParam()['correo'],
                        'contrasena'  => $this->getParam()['contrasena']
                    ];
                    $user = new ClienteUsuarioModel($arrUser);
                    $idUsuario  = $user::createUser();

                    if ($idUsuario != 0) {
                        $arrClient = [
                            'nombres'    => $this->getParam($this)['nombres'],
                            'apellidos'  => $this->getParam($this)['apellidos'],
                            'idUsuario'  => $idUsuario
                        ];

                        $cliente  = new ClienteModel($arrClient);
                        $idCliente = $cliente::create();

                        if ($idCliente != 0) {
                            $payload = [
                                'idCliente'  => $idCliente,
                            ];
                            $token = Security::createTokenJwt(Security::secretKey(), $payload);
                            setcookie('token', $token, time() + (60 * 60 * 6), '/');
                            echo ResponseHttp::status200("Cuenta creada satisfactoriamente");
                        } else {
                            echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                        }
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                } else {
                    echo ResponseHttp::status400("Las contraseñas no coincien");
                }
            }
            exit();
        }
    }

    final public function postForgetPassword(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {

            try {

                $resend = Resend::client('re_F5feTq5Q_LycTnkXJjTDndCuYYuaQJT4s');
                $result = $resend->emails->send([
                    'from' => 'Acme <onboarding@resend.dev>',
                    'to' => ['cesarcunyarache@gmail.com'],
                    'subject' => 'recuperar contraseña',
                    'html' => '<!DOCTYPE html>
                    <html lang="en">
                    <head>
                        
                        <style>
                            body {
                                background-color: white;
                            }
                            
                            * {
                                color: black;
                                font-family: Arial, Helvetica, sans-serif;
                            }
                    
                            .title {
                                text-align: center;
                    
                            }
                            .card {
                                background-color: #ffffff;
                                border-radius: 10px;
                                padding: 1rem;
                                border: 1px solid #ccc;
                            }
                            .code {
                                font-weight: bold;
                                font-size: 2rem
                            }
                    
                        </style>
                    </head>
                    <body>
                        <div class="card">
                            <h1 class="title">Verificacion de codigo</h1>
                            <p>Tu codigo de verificacion es:</p>
                            <h5 class="code">2309</h5>
                    
                            <p>Your account cant be accessed without this verification code, even if you didn’t submit this request.
                    
                                To keep your account secure, we recommend using a unique password for your Adobe account or using the Adobe Account Access app to sign in. Adobe Account Access’ two-factor authentication makes signing in to your account easier, without needing to remember or change passwords.
                                
                                Learn more and download the Adobe Account Access app.</p>
                        </div>
                        
                    </body>
                    </html>',
                ]);
                echo ResponseHttp::status200($result);
            } catch (\Exception $e) {
                echo ResponseHttp::status500('Error: ' . $e->getMessage());
            }


            /* echo ResponseHttp::status200($result->toJson()); */
            /*  try {

                $resend = Resend::client('re_F5feTq5Q_LycTnkXJjTDndCuYYuaQJT4s');
                
                $result = $resend->emails->send([
                    'from' => 'Acme <onboarding@resend.dev>',
                    'to' => ['cesarcunyarache@gmail.com'],
                    'subject' => 'Hello world',
                    'html' => '<strong>It works!</strong>',
                ]);
                echo ResponseHttp::status200($result->toJson());
            } catch (\Exception $e) {
                exit('Error: ' . $e->getMessage());
                echo ResponseHttp::status400($e->getMessage());
            } */
            exit();
        }
    }


    final public function getProfile(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {
                $data  = Security::validateTokenJwt(Security::secretKey());
                $idClient = $data->data->idCliente;

                if (isset($idClient) && !empty($idClient)) {
                    $cliente = ClienteModel::getClientById($idClient);
                    echo ResponseHttp::status200($cliente);
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function getLogout(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            try {
                Security::validateTokenJwt(Security::secretKey());
                setcookie("token", "", time() - 3600, "/");
                echo ResponseHttp::status200("Logout");
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit;
        }
    }

    final public function getVerify(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            try {
                $data = Security::validateTokenJwt(Security::secretKey());
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit;
        }
    }



    /* final public function getAll(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            echo json_encode(UserModel::getAll());
            exit;
        }    
    } */

    
    /* final public function getUser(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $dni = $this->getAttribute()[1];
            if (!isset($dni)) {
                echo json_encode(ResponseHttp::status400('El campo DNI es requerido'));
            }else if (!preg_match(self::$validate_number, $dni)) {
                echo json_encode(ResponseHttp::status400('El DNI soo admite números'));
            } else {
                UserModel::setDni($dni);
                echo json_encode(UserModel::getUser());
                exit;
            }  
            exit;
        }    
    } */

    /*    final public function patchPassword(string $endPoint)
    {        
        if ($this->getMethod() == 'patch' && $this->getRoute() == $endPoint){            
            Security::validateTokenJwt(Security::secretKey());

            $jwtUserData = Security::getDataJwt();                  

            if (empty($this->getParam()['oldPassword']) || empty($this->getParam()['newPassword']) || empty($this->getParam()['confirmNewPassword'])) {
                echo json_encode(ResponseHttp::status400('Todos los campos son requeridos'));
            } else if (!UserModel::validateUserPassword($jwtUserData['IDToken'],$this->getParam()['oldPassword'])) {
                echo json_encode(ResponseHttp::status400('La contraseña antigua no coincide'));
            } else if (strlen($this->getParam()['newPassword']) < 8 || strlen($this->getParam()['confirmNewPassword']) < 8 ) {
                echo json_encode(ResponseHttp::status400('La contraseña debe tener un minimo de 8 caracteres'));
            }else if ($this->getParam()['newPassword'] !== $this->getParam()['confirmNewPassword']){
                echo json_encode(ResponseHttp::status400('Las contraseñas no coinciden'));
            } else {
                UserModel::setIDToken($jwtUserData['IDToken']);
                UserModel::setPassword($this->getParam()['newPassword']); 
                echo json_encode(UserModel::patchPassword());
            } 
            exit;
        }        
    }  */
}
