<?php

$router->post('/api/generate-doctrine-proxy', ['uses' => "GenerateDoctrineProxiesController@generate"]);
$router->post('/api/admin-login', ['uses' => "LoginController@adminLogin"]);
$router->post('/api/manager-login', ['uses' => "LoginController@managerLogin"]);
$router->post('/api/personnel-login', ['uses' => "LoginController@personnelLogin"]);
$router->post('/api/client-signup', ['uses' => "SignupController@clientSignup"]);
$router->post('/api/client-login', ['uses' => "LoginController@clientLogin"]);
$router->patch('/api/client-generate-activation-code', ['uses' => "NotLoggedClientAccountController@generateActivationCode"]);
$router->patch('/api/client-generate-reset-password-code', ['uses' => "NotLoggedClientAccountController@generateResetPasswordCode"]);
$router->patch('/api/client-activate', ['uses' => "NotLoggedClientAccountController@activate"]);
$router->patch('/api/client-reset-password', ['uses' => "NotLoggedClientAccountController@resetPassword"]);
