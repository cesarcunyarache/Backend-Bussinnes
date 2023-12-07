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
use App\Models\MeseroModel;

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

    final public function getReservasByCliente(string $endPoint)
    {
        if ($this->getMethod() == 'get' /*  && $endPoint == $this->getRoute() */) {

            try {
                $id = $this->getAttribute()[1];
                $data = ResevaModel::getReservabyId($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function postCreate(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
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

                $data  = Security::validateTokenJwt(Security::secretKey());
                $idClient = $data->data->idCliente;
                $idMesero = $this->getParam()['idMesero'];


                if (isset($idClient) && !empty($idClient)) {

                    $reserva = new ReservaModel($this->getParam());
                    $arr = $this->getParam()['mesas'];

                    if (isset($idMesero) && !empty($idMesero)) {
                        $reserva::setidMesero($idMesero);
                    }

                    $reserva::setIdCliente((int)$idClient);
                    $res = $reserva::create();

                    if ($res > 0) {
                        foreach ($arr as $mesa) {
                            $reserva::createMesasReserva($res, $mesa['idMesa']);
                        }

                        $payload = ['tokenReserva' => $res];
                        $token = Security::createTokenReserva(Security::secretKey(), $payload);
                        echo ResponseHttp::status200($token);
                    } else {
                        echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                    }
                } else {
                    echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
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
                'idReserva'                   => 'required|numeric',
                'estado'                      => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                /* Security::validateTokenJwt(Security::secretKey()); */

                $idReserva = $this->getParam()['idReserva'];
                $estado = $this->getParam()['estado'];

                $res = ReservaModel::updateEstadoReserva($idReserva, $estado);

                if ($res) {
                    echo ResponseHttp::status200('Datos actualizados correctamente');
                } else {
                    echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                }
            }
            exit;
        }
    }

    final public function getRead(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {
                //Security::validateTokenJwt(Security::secretKey());

                $data = ReservaModel::read();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function getReservasTotal(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {
                /*    Security::validateTokenJwt(Security::secretKey()); */

                $data = ReservaModel::readReservaTotal();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

//
final public function getReadByIdUser(string $endPoint)
    {
        if ($this->getMethod() == 'get'  && $endPoint == $this->getRoute()) {

            try {
                //Security::validateTokenJwt(Security::secretKey());
                $id = $this->getAttribute()[1];
                $data = ReservaModel::getReservaByIdUser($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
    //
    final public function getReadPuntosByIdUser(string $endPoint)
    {
        if ($this->getMethod() == 'get'  && $endPoint == $this->getRoute()) {

            try {
                Security::validateTokenJwt(Security::secretKey());
                $id = $this->getAttribute()[2];
                $data = ReservaModel::getPuntosByIdUser($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
    //
    final public function getTotales(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {
                /*    Security::validateTokenJwt(Security::secretKey()); */

                $data = ReservaModel::totales();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function getMesasOcupadas(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {
                /*    Security::validateTokenJwt(Security::secretKey()); */

                $data = ReservaModel::MesasOcupadas();
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function getReadMesas(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {
                //Security::validateTokenJwt(Security::secretKey());

                $data = ReservaModel::readMesas();
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
                /*  Security::validateTokenJwt(Security::secretKey()); */
                $token = $this->getAttribute()[2];

                if (is_numeric($token)) {
                    $idReserva = $token;
                } else {
                    $data = Security::validateToken($token, Security::secretKey());
                    $idReserva = $data->data->tokenReserva;
                }

                $reserva = ReservaModel::getReadById($idReserva);

                if (count($reserva) > 0) {
                    if ($reserva['idMesero'] !== null) {
                        $mesero = MeseroModel::getMeseroById($reserva['idMesero']);
                        if (count($mesero) > 0) {
                            $reserva['mesero'] = $mesero;
                        }
                    }
                    $mesas = ReservaModel::getReadMesasReserva($idReserva);
                    if (count($mesas) > 0) {

                        $reserva['mesas'] = $mesas;
                    }

                    echo ResponseHttp::status200($reserva);
                }
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function getReadProductos(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {

            try {
                //Security::validateTokenJwt(Security::secretKey());  // Habilitar la validación del token

                // Log o impresiones para debugging
                error_log("Antes de llamar a ReservaModel::readProductos");

                $data = ReservaModel::readProductos();

                // Log o impresiones para debugging
                error_log("Después de llamar a ReservaModel::readProductos");

                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                // Log o impresiones para debugging
                error_log("Error en getReadProductos: " . $e->getMessage());

                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }

    final public function putUpdatePuntos(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), [
                'idProducto'                   => 'required|numeric',
                'idCliente'                   => 'required|numeric',
                'idUsuario'                   => 'required|numeric',
                'puntosCliente'                      => 'required',
                'puntosProducto'                      => 'required',
                'fecha'                             => 'required',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                /* Security::validateTokenJwt(Security::secretKey()); */
                $idProducto = $this->getParam()['idProducto'];
                $idCliente = $this->getParam()['idCliente'];
                $idUsuario = $this->getParam()['idUsuario'];
                $puntosCliente = $this->getParam()['puntosCliente'];
                $puntosProducto = $this->getParam()['puntosProducto'];
                $fecha = $this->getParam()['fecha'];

                $res = ReservaModel::updatePuntosCanje($idProducto, $idCliente, $idUsuario, $puntosCliente, $puntosProducto, $fecha);

                if ($res) {
                    echo ResponseHttp::status200('Puntos actualizados correctamente');
                } else {
                    echo ResponseHttp::status400("Algo salió mal. Por favor, inténtelo nuevamente más tarde.");
                }
            }
            exit;
        }
    }
    //
    final public function getReadProductosByIdUser(string $endPoint)
    {
        if ($this->getMethod() == 'get'  && $endPoint == $this->getRoute()) {

            try {
                //Security::validateTokenJwt(Security::secretKey());
                $id = $this->getAttribute()[2];
                $data = ReservaModel::getProductosByIdCliente($id);
                echo ResponseHttp::status200($data);
            } catch (\Exception $e) {
                echo ResponseHttp::status500($e->getMessage());
            }
            exit();
        }
    }
}
