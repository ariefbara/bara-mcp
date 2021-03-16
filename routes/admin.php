<?php

$managerAggregate = [
    'prefix' => '/api/admin',
    'namespace' => 'Admin',
    'middleware' => 'adminJwtAuth',
];
$router->group($managerAggregate, function () use ($router) {
    $router->patch("/update-profile", ["uses" => "ProfileController@update"]);
    $router->patch("/change-password", ["uses" => "ProfileController@changePassword"]);
    
    $router->group(['prefix' => '/admins'], function () use($router) {
        $controller = "AdminController";
        $router->post("", ["uses" => "$controller@add"]);
        $router->delete("/{adminId}", ["uses" => "$controller@remove"]);
        $router->get("/{adminId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/firms'], function () use($router) {
        $controller = "FirmController";
        $router->post("", ["uses" => "$controller@add"]);
        $router->patch("/{firmId}/suspend", ["uses" => "$controller@suspend"]);
        $router->get("/{firmId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/worksheet-forms'], function () use($router) {
        $controller = "WorksheetFormController";
        $router->post("", ["uses" => "$controller@create"]);
        $router->patch("/{worksheetFormId}", ["uses" => "$controller@update"]);
        $router->delete("/{worksheetFormId}", ["uses" => "$controller@remove"]);
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{worksheetFormId}", ["uses" => "$controller@show"]);
    });
});