<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;;

use App\Config\Message;
use App\Models\ClienteUsuarioModel;
use App\Models\ClienteModel;

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
        }
        exit();
    }


    final public function getProfile(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            /* $resend = Resend::client('re_jQDcy41o_DaKJyvRyjhG2C8CwvZNP3LFx');

            $resend->emails->send([
                'from' => 'Acme <onboarding@resend.dev>',
                'to' => ['delivered@resend.dev'],
                'subject' => 'hello world',
                'html' => '<strong>it works!</strong>',
            ]); */
        }
        exit();
    }





    /**********************Consultar todos los usuarios*********************/
    /* final public function getAll(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            echo json_encode(UserModel::getAll());
            exit;
        }    
    } */

    /**********************Consultar un usuario por DNI*******************************/
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

    /***************************************Registrar usuario*************************************************/
    /*  final public function postSave(string $endPoint)
    {
       if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
       // Security::validateTokenJwt(Security::secretKey()); 

        $validator = new Validator;
        
        $validation = $validator->validate($this->getParam(), [
            'name'               => 'required|regex:/^[a-zA-Z ]+$/',
            'dni'                => 'required|numeric',
            'email'              => 'required|email',            
            'rol'                => 'required|numeric|min:1|regex:/^[12]+$/',
            'password'           => 'required|min:8',
            'confirmPassword'    => 'required|same:password'   
        ]);      

        if ($validation->fails()) {            
            $errors = $validation->errors();            	
            echo json_encode(ResponseHttp::status400($errors->all()[0]));
        } else {            
            new UserModel($this->getParam());
            echo json_encode(UserModel::postSave());
        }              
                          
        exit;
       }
    }    */

    /***************************************************Actualizar contraseña de usuario*********************************************/
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

    /****************************************Eliminar usuario******************************/
    /* final public function deleteUser(string $endPoint)
    {
        if ($this->getMethod() == 'delete' && $this->getRoute() == $endPoint){
            Security::validateTokenJwt(Security::secretKey());

            if (empty($this->getParam()['IDToken'])) {
                echo json_encode(ResponseHttp::status400('Todos los campos son requeridos'));
            } else {
                UserModel::setIDToken($this->getParam()['IDToken']);
                echo json_encode(UserModel::deleteUser());
            }
        exit;
        }
    } */
}
