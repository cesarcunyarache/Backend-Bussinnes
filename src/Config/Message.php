<?php

namespace App\Config;

class Message
{
    public static function getMessages()
    {
        return [
            'contrasena:required' => 'El campo constraseña es requerido',
            'email' => 'Formato de correo inválido',
            'min' => 'El campo :attribute debe contener al menos :min caracteres'

        ];
    }
}
