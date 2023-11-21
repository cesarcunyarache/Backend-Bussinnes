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
 */
      
                $obj = new MeseroModel($this->getParam(),$_FILES);
           
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

?>