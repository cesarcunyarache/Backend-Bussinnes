<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;;

use App\Config\Message;
use App\Config\Mail;
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

                            ClienteModel::setNombres($this->getParam($this)['nombres']);
                            ClienteModel::setApellidos($this->getParam($this)['apellidos']);
                            ClienteModel::setApellidos($this->getParam($this)['apellidos']);
                            ClienteModel::setIdUsuario($idUsuario);

                            $idCliente = ClienteModel::create();

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
                        echo ResponseHttp::status400("Codigo de verificación o correo erroneos");
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
                    $data = ClienteUsuarioModel::getUserByCorreo($correo);

                    if (count($data) > 0) {
                        $payload = ['id' => $data['id'], 'correo' => $data['correo']];
                        $token = Security::createTokenJwt(Security::secretKey(), $payload);

                        Mail::sendEmail($correo, "Restablecer Contraseña", Mail::getBodyResetPassword($token));
                        echo ResponseHttp::status200("Se ha enviado un mensaje para restablecer tu contraseña al correo: " . $correo);
                    }
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500('Error: ' . $e->getMessage());
            }
            exit();
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

    final public function postSendOTP(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            try {
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

                        $correo = $this->getParam()['correo'];
                        $isExists = ClienteUsuarioModel::validateCorreo($correo);

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

                            $res = ClienteUsuarioModel::UpdatePassword($idUser, $contrasena);

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

    final public function putUpdateEmail(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            try {
                $validator = new Validator;

                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'correo'                => 'required',
                    'contrasena'            => 'required',
                    'otp'                   => 'required|min:4'
                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {
                    $correo = $this->getParam()['correo'];
                    $contrasena = $this->getParam()['contrasena'];
                    $otp = (int) $this->getParam()['otp'];

                    $data  = Security::validateTokenJwt(Security::secretKey());

                    if (isset($data) && !empty($data)) {
                        $idClient = $data->data->idCliente;
                        $cliente = ClienteModel::getClientById($idClient);

                        if ($cliente['correo'] !== $correo) {
                            ClienteUsuarioModel::validateCorreo($correo);

                            if (Security::validatePassword($contrasena, $cliente['contrasena'])) {
                                $isValidateOTP = Security::validateOTP($otp, $correo);
                                if ($isValidateOTP) {
                                    $res = ClienteUsuarioModel::UpdateEmail($cliente['idUsuario'], $correo);
                                    if ($res) {
                                        echo ResponseHttp::status200("Actualizado correctamente");
                                    } else {
                                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                                    }
                                } else {
                                    echo ResponseHttp::status400("Codigo de verificación o correo erroneos");
                                }
                            } else {
                                echo ResponseHttp::status400("La contraseña es incorrecta");
                            }
                        } else {
                            echo ResponseHttp::status400("El correo debe ser diferente al ya registrado");
                        }
                    }
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit;
        }
    }

    final public function postSendOtpUpdateEmail(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            try {
                $validator = new Validator;

                $validator->setMessages(Message::getMessages());
                $validation = $validator->validate($this->getParam(), [
                    'correo'               => 'required|email',
                    'contrasena'           => 'required'
                ]);
                if ($validation->fails()) {
                    $errors = $validation->errors();
                    echo ResponseHttp::status400($errors->all()[0]);
                } else {
                    $correo = $this->getParam()['correo'];
                    $contrasena = $this->getParam()['contrasena'];

                    $data  = Security::validateTokenJwt(Security::secretKey());

                    if (isset($data) && !empty($data)) {
                        $idClient = $data->data->idCliente;
                        $cliente = ClienteModel::getClientById($idClient);

                        if ($cliente['correo'] !== $correo) {

                            ClienteUsuarioModel::validateCorreo($correo);

                            if (Security::validatePassword($contrasena, $cliente['contrasena'])) {
                                $otp =  Security::createOTP($correo);
                                if (isset($otp)) {
                                    Mail::sendEmail($correo, "OTP", Mail::getBodyOTP($otp));
                                    echo ResponseHttp::status200('Codigo enviado correctamente');
                                }
                            } else {
                                echo ResponseHttp::status400("La contraseña es incorrecta");
                            }
                        } else {
                            echo ResponseHttp::status400("El correo debe ser diferente al ya registrado");
                        }
                    }
                }

                exit;
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
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
                            $idClient = $data->data->idCliente;
                            $cliente = ClienteModel::getClientById($idClient);
                            if (Security::validatePassword($contrasena, $cliente['contrasena'])) {
                                $res = ClienteUsuarioModel::UpdatePassword($cliente['idUsuario'], $nuevaContrasena);
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
