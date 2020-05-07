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

$guestAggregate = [
    'prefix' => '/api/guest',
    'namespace' => 'Guest',
];
$router->group($guestAggregate, function () use ($router) {
    $router->group(['prefix' => '/firms'], function () use($router) {
        $controller = "FirmController";
        $router->get("/{firmId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $firmAggregate = [
        'prefix' => '/firms/{firmId}',
        'namespace' => 'Firm',
    ];
    $router->group($firmAggregate, function () use ($router) {
        $router->group(['prefix' => '/programs'], function () use($router) {
            $controller = "ProgramController";
            $router->get("/{programId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
    });
});
