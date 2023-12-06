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

use MercadoPago\Client\Payment\PaymentClient;

use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\MPRequestOptions;
use MercadoPago\Preference;
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

    final public function postPago(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validator->setMessages(Message::getMessages());
            $validation = $validator->validate($this->getParam(), []);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo ResponseHttp::status400($errors->all()[0]);
            } else {
                /*   $data  = Security::validateTokenJwt(Security::secretKey()); */
                MercadoPagoConfig::setAccessToken("TEST-7932145193669245-120613-6b72863bfdf9d271d596aad97c87019b-1579625307");

                

                try {

                 /*    $request = [
                        "transaction_amount" => 100,
                        "token" => "4009 1753 3280 6176", 
                        "description" => "description",
                        "installments" => 1,
                        "payment_method_id" => "visa",
                        "payer" => [
                            "email" => "user@test.com",
                        ]
                    ]; */


                  /*   $payment = $client->create($request); */

        /* 
                  $client = new PreferenceClient();

                  $preference = $client->create([
                    "external_reference" => "teste",
                    "items"=> array(
                      array(
                        "id" => "4567",
                        "title" => "Dummy Title",
                        "description" => "Dummy description",
                        "picture_url" => "http://www.myapp.com/myimage.jpg",
                        "category_id" => "eletronico",
                        "quantity" => 1,
                        "currency_id" => "BRL",
                        "unit_price" => 100
                      )
                    ),
                    "payment_methods" => [
                    "default_payment_method_id" => "master",
                    "excluded_payment_types" => array(
                      array(
                        "id" => "ticket"
                      )
                    ),
                    "installments"  => 12,
                    "default_installments" => 1
                  ]]);


                  echo ResponseHttp::status200($preference); 
                 
                     */
                 

 
                } catch (MPApiException $e) {
                   
                    print_r("Status code: " . $e->getApiResponse()->getStatusCode() . "\n");
                  echo ResponseHttp::status200($e->getApiResponse()->getContent()); 
                    echo ResponseHttp::status400($e->getMessage());
                }
            }
            exit;
        }
    }
}
