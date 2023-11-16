<?php

namespace App\Config;

class Message
{
    public static function getMessages()
    {
        return [
            'required' => 'El campo :attribute es requerido',
            'email' => 'Formato de correo inválido',
            'min' => 'El campo :attribute debe contener al menos :min caracteres',
            'max' => 'El campo :attribute debe contener un máximo de :max caracteres',
            'alpha_spaces' => 'El campo :attribute solo se permite caracteres alfabéticos',
            'numeric' => 'El campo :attribute solo se permiten caracteres numéricos',
            'date' => 'La Fecha no tiene es un formato válido',

            'contrasena:required' => 'El campo constraseña es requerido',
            'contrasena:min' => 'El campo Contraseña debe contener al menos :min caracteres',
            'confirmContrasena:required' => 'El campo Confirmar Constraseña es requerido',
            'idTipoDoc:required' => 'El campo Tipo de Documento es requerido',
            'nuevaContrasena:min' => 'El campo Nueva Contraseña debe contener al menos :min caracteres'

        ];
    }
}
