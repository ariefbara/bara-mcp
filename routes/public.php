<?php

$router->post('/api/admin-login', ['uses' => "LoginController@adminLogin"]);
$router->post('/api/client-login', ['uses' => "LoginController@clientLogin"]);
$router->post('/api/manager-login', ['uses' => "LoginController@managerLogin"]);
$router->post('/api/personnel-login', ['uses' => "LoginController@personnelLogin"]);
$router->post('/api/client-signup', ['uses' => "SignupController@clientSignup"]);
$router->post('/api/generate-doctrine-proxy', ['uses' => "GenerateDoctrineProxiesController@generate"]);