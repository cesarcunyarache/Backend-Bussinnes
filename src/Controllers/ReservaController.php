<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use Rakit\Validation\Validator;;

use App\Config\Message;
use App\Models\ColaboradorModel;
use App\Models\ClienteModel;
use App\Models\ReservaModel;
use App\Models\MesaModel;

class ReservaController extends Controller
{

    final public function postStatusMesas(string $endPoint)
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
                    $data = MesaModel::read();
                    $reservas = ReservaModel::getIdReservabyFecha($fecha);

                    if (!empty($reservas)) {
                        foreach ($reservas as $reserva) {
                            $horaReserva = $reserva['hora'];
                            $horaInicio = date('H:i', strtotime("-3 hours", strtotime($horaReserva)));
                            $horaFin = date('H:i', strtotime("+3 hours", strtotime($horaReserva)));
                            if ($horaIngresada > $horaInicio && $horaIngresada < $horaFin) {
                                $index = array_search($reserva['idMesa'], array_column($data, 'idMesa'));
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

    final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'idCliente'            => 'required|numeric',
                'cantComensales'       => 'required',
                'fecha'                => 'required|',
                'hora'                 => 'required|',
                'nivel'                => 'required|',
                'comentario'           => '',
                'mesas'                => 'required',

            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {

                /*   $data  = Security::validateTokenJwt(Security::secretKey());
                $idClient = $data->data->idCliente;

                if (isset($idClient) && !empty($idClient)) { */
                $reserva = new ReservaModel($this->getParam());

                $arr = $this->getParam()['mesas'];



                /*   $cliente::setId($idClient); */
                $res = $reserva::create();
          

                if ($res > 0) {
                    foreach ($arr as $mesa) {

                        $reserva::createMesasReserva($res, $mesa['idMesa']);
                    }


                    echo ResponseHttp::status200('Creado satisfactoriamente');
                } else {
                    echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                }
                /*  } */
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

                $data  = Security::validateTokenJwt(Security::secretKey());
                $idClient = $data->data->idCliente;

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
                Security::validateTokenJwt(Security::secretKey());

                $data = ColaboradorModel::read();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
}
