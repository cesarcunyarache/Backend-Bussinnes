<?php

namespace App\Controllers;

class Controller
{


    protected static function validateEmail(string $email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }


    protected function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }


    protected function getRoute()
    {
        return $_GET['route'];
    }


    protected function getAttribute()
    {
        $route = $this->getRoute();
        $params = explode('/', $route);
        return $params;
    }


    protected function getHeader(string $header)
    {
        $ContentType = getallheaders();
        return $ContentType[$header];
    }

    protected function getParam()
    {
        if ($this->getHeader('Content-Type') == 'application/json') {
           $param = json_decode(file_get_contents("php://input"), true); 
        } else {
            $param = json_decode(file_get_contents("php://input"), true);
        }
        return $param;
    }

    protected function getCookie(string $cookie){
        return $_COOKIE[$cookie];
    }

    protected function getSession(string $session){
        return $_SESSION[$session];
    }


}
