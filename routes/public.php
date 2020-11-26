<?php

$router->post('/api/generate-doctrine-proxy', ['uses' => "GenerateDoctrineProxiesController@generate"]);
$router->post('/api/admin-login', ['uses' => "LoginController@adminLogin"]);
$router->post('/api/manager-login', ['uses' => "LoginController@managerLogin"]);
$router->post('/api/personnel-login', ['uses' => "LoginController@personnelLogin"]);

$router->post('/api/client-signup', ['uses' => "SignupController@clientSignup"]);
$router->post('/api/client-login', ['uses' => "LoginController@clientLogin"]);

$router->post('/api/user-signup', ['uses' => "SignupController@userSignup"]);
$router->post('/api/user-login', ['uses' => "LoginController@userLogin"]);

$guestAggregate = [
    'prefix' => '/api/guest',
    'namespace' => 'Guest',
];
$router->group($guestAggregate, function () use ($router) {
    
    $router->get('/firm-setting/{firmIdentifier}', ['uses' => "FirmSettingController@show"]);
    
    $router->group(['prefix' => '/client-account'], function () use($router) {
        $controller = "ClientAccountController";
        $router->patch("/activate", ["uses" => "$controller@activate"]);
        $router->patch("/reset-password", ["uses" => "$controller@resetPassword"]);
        $router->patch("/generate-activation-code", ["uses" => "$controller@generateActivationCode"]);
        $router->patch("/generate-reset-password-code", ["uses" => "$controller@generateResetPasswordCode"]);
    });
    
    $router->group(['prefix' => '/user-account'], function () use($router) {
        $controller = "UserAccountController";
        $router->patch("/activate", ["uses" => "$controller@activate"]);
        $router->patch("/reset-password", ["uses" => "$controller@resetPassword"]);
        $router->patch("/generate-activation-code", ["uses" => "$controller@generateActivationCode"]);
        $router->patch("/generate-reset-password-code", ["uses" => "$controller@generateResetPasswordCode"]);
    });
    
    $router->group(['prefix' => '/manager-account'], function () use($router) {
        $controller = "ManagerAccountController";
        $router->patch("/reset-password", ["uses" => "$controller@resetPassword"]);
        $router->patch("/generate-reset-password-code", ["uses" => "$controller@generateResetPasswordCode"]);
    });
    
    $router->group(['prefix' => '/personnel-account'], function () use($router) {
        $controller = "PersonnelAccountController";
        $router->patch("/reset-password", ["uses" => "$controller@resetPassword"]);
        $router->patch("/generate-reset-password-code", ["uses" => "$controller@generateResetPasswordCode"]);
    });
    
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
