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
    private const FROM_NAME = 'Mailer';
    private const FROM_EMAIL = 'webbravazo@gmail.com';

  

    public function __construct()
    {
        // No es necesario inicializar las constantes
    }

    final public static function sendEmail(string $toEmail, string $subject, string $body)
    {
        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = self::HOST;                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = self::USERNAME;                     //SMTP username
            $mail->Password   = self::PASSWORD;                               //SMTP password
            $mail->SMTPSecure = self::ENCRYPTION;            //Enable implicit TLS encryption
            $mail->Port       = self::PORT;                                    //TCP port to connect to

            //Recipients
            $mail->setFrom(self::FROM_EMAIL, self::FROM_NAME);
            $mail->addAddress($toEmail);     //Add a recipient

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
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
        return '<style>
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
                        font-size: 5rem
                    }
                </style>
                </head>
                    <body>
                        <div class="card">
                            <h1 class="title">Verificacion de codigo</h1>
                            <p>Tu codigo de verificacion es:</p>
                            <h5 class="code">'. $otp .'</h5>

                        </div>
                        
                    </body>
                </html>';
    }

    final public static function getBodyResetPassword(string $token): string
    {
        return '<style>
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
                        font-size: 5rem
                    }
                </style>
                </head>
                    <body>
                        <div class="card">
                            <h1 class="title">Recuperacion de cuenta</h1>
                            <p>Clic para restablecer tu contraseña:</p>
                            <a href="http://localhost:3000/resetPassword/?token='. $token .'">Restablecer</a>

                        </div>
                        
                    </body>
                </html>';
    }
}
