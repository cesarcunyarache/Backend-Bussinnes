<?php

use App\Config\ResponseHttp;
use App\Controllers\ClienteUsuarioController;


$params  = explode('/', $_GET['route']);

$app = new ClienteUsuarioController();

$app->postLogin("clienteAuth/login");

$app->postRegister("clienteAuth/register");

$app->postForgetPassword("clienteAuth/forgetPassword");

$app->getProfile("clienteAuth/profile");

$app->postLogout("clienteAuth/logout");

$app->getVerify("clienteAuth/verify");

$app->postSendOTP("clienteAuth/sendOtp");

$app->postReSendOTP("clienteAuth/resendOtp");

$app->putResetPassword("clienteAuth/resetPassword");

$app->putUpdateEmail("clienteAuth/updateEmail");

$app->postSendOtpUpdateEmail("clienteAuth/sendOtpUpdateEmail");

$app->putUpdatePassword("clienteAuth/updatePassword");


echo ResponseHttp::status404();
