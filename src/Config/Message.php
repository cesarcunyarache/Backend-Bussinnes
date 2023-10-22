<?php

namespace App\Config;

class Message
{
    public static function getMessages()
    {
        return [
            'required' => 'El campo :attribute es requerido',
            'contrasena:required' => 'El campo constraseña es requerido',
            'confirmContrasena:required' => 'El campo Confirmar Constraseña es requerido',
            'email' => 'Formato de correo inválido',
            'min' => 'El campo :attribute debe contener al menos :min caracteres',
            'contrasena:min' => 'El campo Contraseña debe contener al menos :min caracteres',
            'alpha_spaces' => 'El campo :attribute solo se permite caracteres alfabéticos'

        ];
    }
}
