<?php

return [
  'weather' => [
    'retrieved' => 'Weather data retrieved successfully',
    'not_found' => 'City not found'
  ],
  'auth' => [
    'registered' => 'Register successfully 🎉',
    'logged_out' => 'Logged out successfully'
  ],
  'errors' => [
    'invalid_credentials' => 'Invalid credentials ❌',
    'unauthorized' => 'Unauthorized ❌',
    'not_found' => '404 Not found :(',
    'internal_server_error' => 'Wops something went wrong 😭',
    'invalid_request' => 'Invalid request',
  ],
  'subscription' => [
    'upgraded' => 'Now you are a premium user 🎉',
    'already_premium' => 'You are already a premium user 🎉'
  ],
  'favorites' => [
    'added' => 'Ciudad agregada a favoritos',
    'removed' => 'Ciudad removida de favoritos'
  ],
  'validation_errors' => [
    'city_required' => 'The city parameter is required',
    'invalid_city_max_length' => 'The city parameter must be less than 150 characters',
    'invalid_lang_length' => 'The lang parameter must be 2 characters length',
    'lang_must_be_string' => 'The lang parameter must be a string',
    'name_required' => 'The name parameter is required',
    'invalid_name_max_length' => 'The name parameter must be less than 100 characters',
    'name_must_be_string' => 'The name parameter must be a string',
    'email_required' => 'The email parameter is required',
    'invalid_email_max_length' => 'The email parameter must be less than 255 characters',
    'email_must_be_valid' => 'The email parameter must be a valid email',
    'email_must_be_unique' => 'The email parameter must be unique',
    'password_required' => 'The password parameter is required',
    'passwords_must_match' => 'The passwords parameters must match',
    'city_id_required' => 'The city_id parameter is required',
    'city_id_must_exist' => 'The city_id parameter must exist'
  ],
  'errors' => [
    'weather_api_errors' => [
      'location_not_found' => 'City not found, please try with another one',
    ],
  ]
];
