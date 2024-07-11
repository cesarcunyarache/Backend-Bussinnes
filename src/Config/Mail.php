<?php

namespace App\Config;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Config\ResponseHttp;


class Mail
{
    private const HOST = 'smtp.gmail.com';
    private const PORT = 465;
    private const USERNAME = 'webbravazo@gmail.com';
    private const PASSWORD = 'grhbwnxmqyehcfhh';
    private const ENCRYPTION = PHPMailer::ENCRYPTION_SMTPS;
    private const FROM_NAME = 'Restaurante';
    private const FROM_EMAIL = 'webbravazo@gmail.com';

  

    public function __construct()
    {
       
    }

    final public static function sendEmail(string $toEmail, string $subject, string $body)
    {
        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();                                         
            $mail->Host       = self::HOST;               
            $mail->SMTPAuth   = true;                               
            $mail->Username   = self::USERNAME;                    
            $mail->Password   = self::PASSWORD;                            
            $mail->SMTPSecure = self::ENCRYPTION;            
            $mail->Port       = self::PORT;                       

            //Recipients
            $mail->setFrom(self::FROM_EMAIL, self::FROM_NAME);
            $mail->addAddress($toEmail);     //Add a recipient

            //Content
            $mail->isHTML(true);                       
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->CharSet = 'UTF-8'; 

            $mail->send();
        } catch (Exception $e) {
            if ($e->getCode() ==0){
                echo ResponseHttp::status400("No se pudo enviar el correo debido a un problema de conexión.");
                
            } else {
                echo ResponseHttp::status500("Algo salió mal al enviar el Correo. Por favor, inténtelo nuevamente más tarde.");
                error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            }
            exit;   
        }
    }

    final public static function sendOTP (){
      
    }


    final public static function getBodyOTP(string $otp): string
    {
        return '<html>
                    <body>
                    <div class="card"
                    style="width:400px; text-align: center; margin:auto; border-radius: 10px; border: 1px solid #000; background-color: black;">
                    <h1 class="title" style="color:white; padding: 1rem;">Verificacion
                        de codigo</h1>
                    <div style="background-color: white; padding-bottom: 5px; padding-top: 5px;">
                        <p>Tu codigo de verificacion es:</p>
                        <h5 class="code" style="font-weight: bold;">'. $otp .'</h5>
                    </div>
                </div>
                    </body>
                </html>';
    }

    final public static function getBodyResetPassword(string $token): string
    {
        return '<html>
                    <body>
                    <div class="card"
                    style="width:400px; text-align: center; margin:auto; border-radius: 10px; border: 1px solid #000; background-color: black;">
                    <h1 class="title" style=" color:white; padding: 1   rem;">Recuperacion de cuenta</h1>
                    <div style="background-color: white; padding-bottom: 5px; padding-top: 5px;">
                        <p>Clic para restablecer tu contraseña:</p>
                        <a style="font-weight: bold; color: red;"
                            href="http://localhost:3000/resetPassword/?token='. $token .'">Restablecer</a>
                    </div>
                </div>
                    </body>
                </html>';
    }


    final public static function getBodyResetPasswordAdmin(string $token): string
    {
        return '<html>
                    <body>
                    <div class="card"
            style="width:400px; text-align: center; margin:auto; border-radius: 10px; border: 1px solid #000; background-color: black;">
            <h1 class="title" style=" color:white; padding: 1   rem;">Recuperacion de cuenta</h1>
            <div style="background-color: white; padding-bottom: 5px; padding-top: 5px;">
                <p>Clic para restablecer tu contraseña:</p>
                <a style="font-weight: bold; color: red;"
                    href="http://localhost:3001/resetPassword/?token='. $token .'">Restablecer</a>
            </div>
        </div>
                    </body>
                </html>';
    }

    final public static function getBodyContact(string $cabecera, string $info, string $nombres, string $apellidos, string $tipoDoc, string $documento, string $telefono, string $correo, string $motivo, string $mensaje)
    {
        return
        '<html>
        <body>
        <div
            style="width:400px; background-color: #ffff; border-radius: 10px; padding: 3rem; border: 1px solid #ccc; color: black; margin:auto;">
            <h1 style="text-align: left;">'.$cabecera.'</h1>
            <p style="margin-bottom: 5px; font-weight:bold;">'.$info.'</p>
            <p style="margin-bottom: 5px; font-weight:bold;">Nombres:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'. $nombres .'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Apellidos:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$apellidos.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Tipo de documento:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$tipoDoc.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Numero de documento:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$documento.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Telefono:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$telefono.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Correo:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$correo.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Motivo:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$motivo.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Mensaje:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$mensaje.'</span>
            </p>
        </div>
    </body>
        </html>';
    }

    final public static function getBodyBook(string $cabecera, string $info, string $nombres, string $apellidos, string $tipoDoc, string $documento, string $telefono, string $correo, string $fecha, string $motivo, string $mensaje)
    {
        return
        '<html>
        <body>
        <div
            style="width:400px; background-color: #ffff; border-radius: 10px; padding: 3rem; border: 1px solid #ccc; color: black; margin:auto;">
            <h1 style="text-align: left;">'.$cabecera.'</h1>
            <p style="margin-bottom: 5px; font-weight:bold;">'.$info.'</p>
            <p style="margin-bottom: 5px; font-weight:bold;">Nombres:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'. $nombres .'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Apellidos:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$apellidos.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Tipo de documento:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$tipoDoc.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Numero de documento:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$documento.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Telefono:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$telefono.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Correo:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$correo.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Fecha del suceso:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$fecha.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Motivo:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$motivo.'</span>
            </p>
            <p style="margin-bottom: 5px; font-weight:bold;">Mensaje:
                <span style="font-size: 1rem; margin-top: 0; font-weight:normal;">'.$mensaje.'</span>
            </p>
        </div>
    </body>
        </html>';
    }
}
