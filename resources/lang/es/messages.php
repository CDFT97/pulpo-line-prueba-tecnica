<?php

return [
  'weather' => [
    'retrieved' => 'Datos del clima obtenidos exitosamente',
    'not_found' => 'Ciudad no encontrada'
  ],
  'auth' => [
    'registered' => 'Registro exitoso 🎉',
    'logged_out' => 'Sesión cerrada correctamente'
  ],
  'errors' => [
    'invalid_credentials' => 'Credenciales inválidas ❌',
    'unauthorized' => 'No autorizado ❌',
    'not_found' => '404 No encontrado :(',
    'internal_server_error' => '¡Ups! Algo salió mal 😭',
    'invalid_request' => 'Solicitud inválida',
  ],
  'subscription' => [
    'upgraded' => '¡Ahora eres usuario premium! 🎉',
    'already_premium' => 'Ya eres usuario premium 🎉'
  ],
  'favorites' => [
    'added' => 'Ciudad agregada a favoritos',
    'removed' => 'Ciudad removida de favoritos' 
  ],
  'validation_errors' => [
    'city_required' => 'El parámetro ciudad es requerido',
    'invalid_city_max_length' => 'El parámetro ciudad debe tener menos de 150 caracteres',
    'invalid_lang_length' => 'El parámetro lang debe tener 2 caracteres de longitud',
    'lang_must_be_string' => 'El parámetro lang debe ser una cadena de texto',
    'name_required' => 'El parámetro nombre es requerido',
    'invalid_name_max_length' => 'El parámetro nombre debe tener menos de 100 caracteres',
    'name_must_be_string' => 'El parámetro nombre debe ser una cadena de texto',
    'email_required' => 'El parámetro email es requerido',
    'invalid_email_max_length' => 'El parámetro email debe tener menos de 255 caracteres',
    'email_must_be_valid' => 'El parámetro email debe ser un correo válido',
    'email_must_be_unique' => 'El parámetro email debe ser único',
    'password_required' => 'El parámetro contraseña es requerido',
    'passwords_must_match' => 'Las contraseñas deben coincidir',
    'city_id_required' => 'El parámetro city_id es requerido',
    'city_id_must_exist' => 'El parámetro city_id debe existir'
  ]
];