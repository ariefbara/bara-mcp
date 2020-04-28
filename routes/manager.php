<?php

$managerAggregate = [
    'prefix' => '/api/manager',
    'namespace' => 'Manager',
    'middleware' => 'managerJwtAuth',
];
$router->group($managerAggregate, function () use ($router) {
    $router->group(['prefix' => '/consultation-feedback-forms'], function () use($router) {
        $controller = "ConsultationFeedbackFormController";
        $router->post("", ["uses" => "$controller@add"]);
        $router->patch("/{consultationFeedbackFormId}", ["uses" => "$controller@update"]);
        $router->delete("/{consultationFeedbackFormId}", ["uses" => "$controller@remove"]);
        $router->get("/{consultationFeedbackFormId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/worksheet-forms'], function () use($router) {
        $controller = "WorksheetFormController";
        $router->post("", ["uses" => "$controller@add"]);
        $router->patch("/{worksheetFormId}", ["uses" => "$controller@update"]);
        $router->delete("/{worksheetFormId}", ["uses" => "$controller@remove"]);
        $router->get("/{worksheetFormId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/personnels'], function () use($router) {
        $controller = "PersonnelController";
        $router->post("", ["uses" => "$controller@add"]);
        $router->patch("/{personnelId}", ["uses" => "$controller@update"]);
        $router->delete("/{personnelId}", ["uses" => "$controller@remove"]);
        $router->get("/{personnelId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/programs'], function () use($router) {
        $controller = "ProgramController";
        $router->post("", ["uses" => "$controller@add"]);
        $router->patch("/{programId}/update", ["uses" => "$controller@update"]);
        $router->patch("/{programId}/publish", ["uses" => "$controller@publish"]);
        $router->delete("/{programId}", ["uses" => "$controller@remove"]);
        $router->get("/{programId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $programAggregate = [
        'prefix' => '/programs/{programId}',
        'namespace' => 'Program',
    ];
    $router->group($programAggregate, function () use ($router) {
        $router->group(['prefix' => '/coordinators'], function () use($router) {
            $controller = "CoordinatorController";
            $router->put("", ["uses" => "$controller@assign"]);
            $router->delete("/{coordinatorId}", ["uses" => "$controller@remove"]);
            $router->get("/{coordinatorId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/consultants'], function () use($router) {
            $controller = "ConsultantController";
            $router->put("", ["uses" => "$controller@assign"]);
            $router->delete("/{consultantId}", ["uses" => "$controller@remove"]);
            $router->get("/{consultantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/registration-phases'], function () use($router) {
            $controller = "RegistrationPhaseController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{registrationPhaseId}", ["uses" => "$controller@update"]);
            $router->delete("/{registrationPhaseId}", ["uses" => "$controller@remove"]);
            $router->get("/{registrationPhaseId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/consultation-setups'], function () use($router) {
            $controller = "ConsultationSetupController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{consultationSetupId}", ["uses" => "$controller@update"]);
            $router->delete("/{consultationSetupId}", ["uses" => "$controller@remove"]);
            $router->get("/{consultationSetupId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/missions'], function () use($router) {
            $controller = "MissionController";
            $router->post("", ["uses" => "$controller@addRoot"]);
            $router->post("/{missionId}", ["uses" => "$controller@addBranch"]);
            $router->patch("/{missionId}/update", ["uses" => "$controller@update"]);
            $router->patch("/{missionId}/publish", ["uses" => "$controller@publish"]);
            $router->get("/{missionId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $missionAggregate = [
            'prefix' => '/missions/{missionId}',
            'namespace' => 'Mission',
        ];
        $router->group($missionAggregate, function () use ($router) {
            $router->group(['prefix' => '/learning-materials'], function () use($router) {
                $controller = "LearningMaterialController";
                $router->post("", ["uses" => "$controller@add"]);
                $router->patch("/{learningMaterialId}", ["uses" => "$controller@update"]);
                $router->delete("/{learningMaterialId}", ["uses" => "$controller@remove"]);
                $router->get("/{learningMaterialId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
        });
    });
});

