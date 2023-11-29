<?php

use App\Config\ResponseHttp;
use App\Controllers\UsuarioColaboradorController;

$params  = explode('/', $_GET['route']);


$app = new UsuarioColaboradorController();

$app->postLogin("colaboradorAuth/login");

$app->getRead("colaboradorAuth/");

$app->getReadById("colaboradorAuth/{$params[1]}");

$app->postRegister("colaboradorAuth/register");

$app->postSendOTP("colaboradorAuth/sendOtp");

$app->postForgetPassword("colaboradorAuth/forgetPassword");

$app->putResetPassword("colaboradorAuth/resetPassword");

$app->postSendOtpUpdateEmail("colaboradorAuth/sendOtpUpdateEmail");

$app->putUpdateEmail("colaboradorAuth/updateEmail");

$app->putUpdatePassword("colaboradorAuth/updatePassword"); 

$app->getProfile("colaboradorAuth/profile/");

/*
$app->postLogout("clienteAuth/logout");
$app->getVerify("clienteAuth/verify");
$app->postReSendOTP("clienteAuth/resendOtp");

*/

echo ResponseHttp::status404();
