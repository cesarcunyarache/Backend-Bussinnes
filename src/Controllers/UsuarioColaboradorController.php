<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;;

use App\Config\Message;
use App\Config\Mail;
use App\Models\ClienteUsuarioModel;
use App\Models\UsuarioColaboradorModel;
use App\Models\ColaboradorModel;
use App\Models\ClienteModel;


class UsuarioColaboradorController extends Controller
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
                $correo = $this->getParam()['correo'];
                $contrasena = $this->getParam()['contrasena'];

                $data = UsuarioColaboradorModel::login($correo, $contrasena);

                if (count($data) > 0) {
                    $res = ColaboradorModel::getClientByIdUser($data['id']);

                    if (count($res) > 0) {
                        $payload = ['idColaborador' => $res['id'], 'idRol' => $data['idRol']];
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
                'idColaborador'        => 'required|numeric',
                'correo'               => 'required|email',
                'contrasena'           => 'required|min:6',
                'confirmContrasena'    => 'required',
                'idRol'                => 'required|numeric',
                'otp'                  => 'required|min:4'
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                if ($this->getParam()['contrasena'] === $this->getParam()['confirmContrasena']) {

                    $otp = (int) $this->getParam()['otp'];
                    $email = $this->getParam()['correo'];

                    $isValidateOTP = Security::validateOTP($otp, $email);

                    if ($isValidateOTP) {
                        $user = new UsuarioColaboradorModel($this->getParam());
                        $idUsuario  = $user::createUser();

                        if ($idUsuario != 0) {
                            $idColaborador = $this->getParam()['idColaborador'];
                            $res = ColaboradorModel::updateIdUser($idColaborador, $idUsuario);

                            if ($res) {
                                echo ResponseHttp::status200("Creado satisfactoriamente");
                            } else {
                                echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                            }
                        } else {
                            echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                        }
                    } else {
                        echo ResponseHttp::status400("Codigo de verificación o correo erroneos");
                    }
                } else {
                    echo ResponseHttp::status400("Las contraseñas no coincien");
                }
            }
            exit();
        }
    }


    final public function postSendOTP(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            try {
                $validator = new Validator;

                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'idColaborador'        => 'required|numeric',
                    'correo'               => 'required|email',
                    'contrasena'           => 'required|min:6',
                    'confirmContrasena'    => 'required',
                    'idRol'                => 'required|numeric',

                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {
                    if ($this->getParam()['contrasena'] === $this->getParam()['confirmContrasena']) {

                        $correo = $this->getParam()['correo'];
                        $isExists = UsuarioColaboradorModel::validateCorreo($correo);

                        if ($isExists) {
                            $otp =  Security::createOTP($correo);
                            if (isset($otp)) {
                                Mail::sendEmail($correo, "OTP", Mail::getBodyOTP($otp));
                                echo ResponseHttp::status200('OTP Enviado Correctamente');
                            }
                        }
                    } else {
                        echo ResponseHttp::status400("Las contraseñas no coincien");
                    }
                }

                exit;
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
        }
    }


    final public function postForgetPassword(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {

            try {
                $validator = new Validator;
                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'correo'               => 'required|email',
                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {
                    $correo = $this->getParam()['correo'];
                    $data = UsuarioColaboradorModel::getUserByCorreo($correo);

                    if (count($data) > 0) {
                        $payload = ['id' => $data['id'], 'correo' => $data['correo']];
                        $token = Security::createTokenJwt(Security::secretKey(), $payload, 600);

                        Mail::sendEmail($correo, "Restablecer Contraseña", Mail::getBodyResetPasswordAdmin($token));
                        echo ResponseHttp::status200("Se ha enviado un mensaje para restablecer tu contraseña al correo: " . $correo);
                    }
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500('Error: ' . $e->getMessage());
            }
            exit();
        }
    }

    final public function putResetPassword(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            try {
                $validator = new Validator;

                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'token'                => 'required',
                    'contrasena'           => 'required|min:6',
                    'confirmContrasena'    => 'required',
                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {

                    if ($this->getParam()['contrasena'] === $this->getParam()['confirmContrasena']) {

                        $contrasena = $this->getParam()['contrasena'];
                        $data = Security::validateToken($this->getParam()['token'], Security::secretKey());
                        $idUser = $data->data->id;

                        if (isset($idUser) && !empty($idUser)) {

                            $res = UsuarioColaboradorModel::UpdatePassword($idUser, $contrasena);

                            if ($res) {
                                echo ResponseHttp::status200("Actualizado correctamente");
                            } else {
                                echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                            }
                        }
                    } else {
                        echo ResponseHttp::status400("Las contraseñas no coincien");
                    }
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit;
        }
    }

    final public function getRead(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {

                Security::validateTokenJwt(Security::secretKey());
                $data = UsuarioColaboradorModel::read();
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
                $data = UsuarioColaboradorModel::getUserColaborador($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }


    final public function postSendOtpUpdateEmail(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            try {
                $validator = new Validator;

                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'id'                   => 'required|',
                    'correo'               => 'required|email',
                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {
                    $id = $this->getParam()['id'];
                    $correo = $this->getParam()['correo'];
                   /*  $contrasena = $this->getParam()['contrasena']; */

                    Security::validateTokenJwt(Security::secretKey());
                    Security::validateTokenJwt(Security::secretKey());
                    $user = UsuarioColaboradorModel::getUserById($id);

                    if (count($user) > 0) {
                        if ($user['correo'] !== $correo) {
                            UsuarioColaboradorModel::validateCorreo($correo);
                            /*   if (Security::validatePassword($contrasena, $user['contrasena'])) { */
                            $otp =  Security::createOTP($correo);
                            if (isset($otp)) {
                                Mail::sendEmail($correo, "OTP", Mail::getBodyOTP($otp));
                                echo ResponseHttp::status200('Codigo enviado correctamente');
                            }
                        /* } else {
                            echo ResponseHttp::status400("La contraseña es incorrecta");
                        } */
                    } else {
                        echo ResponseHttp::status400("El correo debe ser diferente al ya registrado");
                    }
                    } else {
                        echo ResponseHttp::status400("El usuario no existe");
                    } 
                }

                exit;
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
        }
    }


    final public function putUpdateEmail(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            try {
                $validator = new Validator;

                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'id'                    => 'required',
                    'correo'                => 'required',
                 /*    'contrasena'            => 'required', */
                    'otp'                   => 'required|min:4'
                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {
                    $id = $this->getParam()['id'];
                    $correo = $this->getParam()['correo'];
                   /*  $contrasena = $this->getParam()['contrasena']; */
                    $otp = (int) $this->getParam()['otp'];

                    Security::validateTokenJwt(Security::secretKey());

                    $user = UsuarioColaboradorModel::getUserById($id);

                    if (count($user) > 0) {
                        if ($user['correo'] !== $correo) {
                            UsuarioColaboradorModel::validateCorreo($correo);
                          /*   if (Security::validatePassword($contrasena, $user['contrasena'])) { */
                                $isValidateOTP = Security::validateOTP($otp, $correo);
                                if ($isValidateOTP) {
                                    $res = UsuarioColaboradorModel::UpdateEmail($user['id'], $correo);
                                    if ($res) {
                                        echo ResponseHttp::status200("Actualizado correctamente");
                                    } else {
                                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                                    }
                                } else {
                                    echo ResponseHttp::status400("Codigo de verificación o correo erroneos");
                                }
                            /* } else {
                                echo ResponseHttp::status400("La contraseña es incorrecta");
                            } */
                        } else {
                            echo ResponseHttp::status400("El correo debe ser diferente al ya registrado");
                        }
                    } else {
                        echo ResponseHttp::status400("El usuario no existe");
                    }
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit;
        }
    }



    final public function getProfile(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {
                $data  = Security::validateTokenJwt(Security::secretKey());

                if (isset($data) && !empty($data)) {
                    $idClient = $data->data->idCliente;
                    $cliente = ClienteModel::getClientById($idClient);
                    echo ResponseHttp::status200($cliente);
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function putUpdatePassword(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            try {
                $validator = new Validator;

                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'contrasena'            => 'required', 
                    'nuevaContrasena'       => 'required|min:6',
                    'confirmContrasena'     => 'required'
                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {
                    $contrasena = $this->getParam()['contrasena']; 
                    $nuevaContrasena = $this->getParam()['nuevaContrasena'];
                    $confirmContrasena = $this->getParam()['confirmContrasena'];

                    if ($nuevaContrasena === $confirmContrasena) {
                        $data  = Security::validateTokenJwt(Security::secretKey());
                        if (isset($data) && !empty($data)) {
                            $idColaborador = $data->data->idColaborador;
                            $cliente = UsuarioColaboradorModel::getUserById($idColaborador);
                             if (Security::validatePassword($contrasena, $cliente['contrasena'])) {
                                $res = UsuarioColaboradorModel::UpdatePassword($cliente['id'], $nuevaContrasena);
                                if ($res) {
                                    echo ResponseHttp::status200("Actualizado correctamente");
                                } else {
                                    echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                                }
                             } else {
                                echo ResponseHttp::status400("La contraseña es incorrecta");
                            } 
                        }
                    } else {
                        echo ResponseHttp::status400("Las contraseñas no coincien");
                    }
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit;
        }
    }


    final public function postLogout(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
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

    final public function postReSendOTP(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            try {
                $validator = new Validator;

                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'correo'               => 'required|email',
                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {
                    $correo = $this->getParam()['correo'];
                    $isExists = ClienteUsuarioModel::validateCorreo($correo);

                    if ($isExists) {
                        $otp =  Security::createOTP($correo);
                        if (isset($otp)) {
                            Mail::sendEmail($correo, "OTP", Mail::getBodyOTP($otp));
                            echo ResponseHttp::status200('Codigo enviado correctamente');
                        }
                    }
                }

                exit;
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
        }
    }


    final public function post(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {

            $fecha = $this->getParam()['fecha'];

            $res = ClienteModel::search($fecha);

            echo ResponseHttp::status200($res);
            exit();
        }
    }
}
