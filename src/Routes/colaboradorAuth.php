<?php

use App\Config\ResponseHttp;
use App\Controllers\UsuarioColaboradorController;

$params  = explode('/', $_GET['route']);

$app = new UsuarioColaboradorController();

$app->postLogin("colaboradorAuth/login");

$app->postRegister("colaboradorAuth/register");

$app->postSendOTP("colaboradorAuth/sendOtp");

$app->postForgetPassword("colaboradorAuth/forgetPassword");

$app->putResetPassword("colaboradorAuth/resetPassword");


/*
$app->getProfile("clienteAuth/profile");

$app->postLogout("clienteAuth/logout");

$app->getVerify("clienteAuth/verify");

$app->postReSendOTP("clienteAuth/resendOtp");

$app->putUpdateEmail("clienteAuth/updateEmail");

$app->postSendOtpUpdateEmail("clienteAuth/sendOtpUpdateEmail");

$app->putUpdatePassword("clienteAuth/updatePassword"); */



echo ResponseHttp::status404();
