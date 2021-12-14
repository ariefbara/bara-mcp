<?php

$managerAggregate = [
    'prefix' => '/api/manager',
    'namespace' => 'Manager',
    'middleware' => 'managerJwtAuth',
];
$router->group($managerAggregate, function () use ($router) {
    
    $router->post('/upload-firm-file', ['uses' => "UploadFileController@upload"]);
    $router->post('/upload-personal-file', ['uses' => "FileUploadController@upload"]);
    
    $router->get("/evaluation-report-summary", ["uses" => "EvaluationReportSummaryController@summary"]);
    $router->get("/download-evaluation-report-summary-xls", ["uses" => "EvaluationReportSummaryController@downloadSummaryXls"]);
    $router->get("/download-evaluation-report-summary-xls-group-by-feedback-form", ["uses" => "EvaluationReportSummaryController@downloadSummaryXlsGroupByFeedbackForm"]);
    $router->get("/evaluation-report-transcript", ["uses" => "EvaluationReportSummaryController@transcript"]);
    $router->get("/download-evaluation-report-transcript-xls", ["uses" => "EvaluationReportSummaryController@downloadTranscriptXls"]);
    $router->get("/download-evaluation-report-transcript-xls-group-by-feedback-form", ["uses" => "EvaluationReportSummaryController@downloadTranscriptXlsGroupByFeedbackForm"]);
    
    $router->post("/clients", ["uses" => "ClientController@add"]);
    $router->patch("/clients/{id}/activate", ["uses" => "ClientController@activate"]);
    $router->get("/clients", ["uses" => "ClientController@showAll"]);
    $router->get("/clients/{id}", ["uses" => "ClientController@show"]);
    
    $router->post("/teams", ["uses" => "TeamController@add"]);
    $router->post("/teams/{teamId}/members", ["uses" => "TeamController@addMember"]);
    $router->delete("/teams/{teamId}/members/{id}", ["uses" => "TeamController@disableMember"]);
    $router->get("/teams", ["uses" => "TeamController@showAll"]);
    $router->get("/teams/{id}", ["uses" => "TeamController@show"]);
    
    $router->put("/programs/{programId}/client-participants", ["uses" => "ProgramParticipantController@addClientParticipant"]);
    $router->put("/programs/{programId}/team-participants", ["uses" => "ProgramParticipantController@addTeamParticipant"]);
    $router->get("/participants", ["uses" => "ProgramParticipantController@showAll"]);
    $router->get("/participants/{id}", ["uses" => "ProgramParticipantController@show"]);
    
    $router->group(['prefix' => '/account'], function () use($router) {
        $controller = "AccountController";
        $router->patch("/change-password", ["uses" => "$controller@changePassword"]);
        $router->get("", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/firm-profile'], function () use($router) {
        $controller = "FirmController";
        $router->patch("/update", ["uses" => "$controller@update"]);
        $router->put("/bio-search-filter", ["uses" => "$controller@setBioSearchFilter"]);
        $router->get("", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/feedback-forms'], function () use($router) {
        $controller = "FeedbackFormController";
        $router->post("", ["uses" => "$controller@add"]);
        $router->patch("/{feedbackFormId}", ["uses" => "$controller@update"]);
        $router->delete("/{feedbackFormId}", ["uses" => "$controller@remove"]);
        $router->get("/{feedbackFormId}", ["uses" => "$controller@show"]);
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
    
    $router->group(['prefix' => '/profile-forms'], function () use($router) {
        $controller = "ProfileFormController";
        $router->post("", ["uses" => "$controller@create"]);
        $router->patch("/{profileFormId}", ["uses" => "$controller@update"]);
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{profileFormId}", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/personnels'], function () use($router) {
        $controller = "PersonnelController";
        $router->post("", ["uses" => "$controller@add"]);
        $router->patch("/{personnelId}", ["uses" => "$controller@update"]);
        $router->patch("/{personnelId}/enable", ["uses" => "$controller@enable"]);
        $router->delete("/{personnelId}", ["uses" => "$controller@disable"]);
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
    
    $router->group(['prefix' => '/managers'], function () use($router) {
        $controller = "ManagerController";
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{managerId}", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/bio-forms'], function () use($router) {
        $controller = "BioFormController";
        $router->post("", ["uses" => "$controller@create"]);
        $router->patch("/{bioFormId}/update", ["uses" => "$controller@update"]);
        $router->patch("/{bioFormId}/disable", ["uses" => "$controller@disable"]);
        $router->patch("/{bioFormId}/enable", ["uses" => "$controller@enable"]);
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{bioFormId}", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/client-bios'], function () use($router) {
        $controller = "ClientBioController";
        $router->get("/{clientId}", ["uses" => "$controller@showAll"]);
        $router->get("/{clientId}/{bioFormId}", ["uses" => "$controller@show"]);
    });
    
    $programAggregate = [
        'prefix' => '/programs/{programId}',
        'namespace' => 'Program',
    ];
    $router->group($programAggregate, function () use ($router) {
        
        $router->post('/sponsors', ['uses' => "SponsorController@add"]);
        $router->put('/sponsors/{sponsorId}/update', ['uses' => "SponsorController@update"]);
        $router->put('/sponsors/{sponsorId}/enable', ['uses' => "SponsorController@enable"]);
        $router->delete('/sponsors/{sponsorId}', ['uses' => "SponsorController@disable"]);
        $router->get('/sponsors/{sponsorId}', ['uses' => "SponsorController@show"]);
        $router->get('/sponsors', ['uses' => "SponsorController@showAll"]);
        
        $router->group(['prefix' => '/coordinators'], function () use($router) {
            $controller = "CoordinatorController";
            $router->put("", ["uses" => "$controller@assign"]);
            $router->delete("/{coordinatorId}", ["uses" => "$controller@disable"]);
            $router->get("/{coordinatorId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/consultants'], function () use($router) {
            $controller = "ConsultantController";
            $router->put("", ["uses" => "$controller@assign"]);
            $router->delete("/{consultantId}", ["uses" => "$controller@disable"]);
            $router->get("/{consultantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/participants'], function () use($router) {
            $controller = "ParticipantController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{participantId}", ["uses" => "$controller@show"]);
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
        
        $router->group(['prefix' => '/metrics'], function () use($router) {
            $controller = "MetricController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{metricId}", ["uses" => "$controller@update"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{metricId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/missions'], function () use($router) {
            $controller = "MissionController";
            $router->post("", ["uses" => "$controller@addRoot"]);
            $router->post("/{missionId}", ["uses" => "$controller@addBranch"]);
            $router->patch("/{missionId}/update", ["uses" => "$controller@update"]);
            $router->patch("/{missionId}/change-worksheet-form", ["uses" => "$controller@changeWorksheetForm"]);
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
        
        $router->group(['prefix' => '/activity-types'], function () use($router) {
            $controller = "ActivityTypeController";
            $router->post("", ["uses" => "$controller@create"]);
            $router->patch("/{activityTypeId}/update", ["uses" => "$controller@update"]);
            $router->patch("/{activityTypeId}/disable", ["uses" => "$controller@disable"]);
            $router->patch("/{activityTypeId}/enable", ["uses" => "$controller@enable"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/all-initiable", ["uses" => "$controller@showAllInitableActivityType"]);
            $router->get("/{activityTypeId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/evaluation-plans'], function () use($router) {
            $controller = "EvaluationPlanController";
            $router->post("", ["uses" => "$controller@create"]);
            $router->patch("/{evaluationPlanId}/update", ["uses" => "$controller@update"]);
            $router->patch("/{evaluationPlanId}/disable", ["uses" => "$controller@disable"]);
            $router->patch("/{evaluationPlanId}/enable", ["uses" => "$controller@enable"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{evaluationPlanId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/programs-profile-forms'], function () use($router) {
            $controller = "ProgramsProfileFormController";
            $router->post("", ["uses" => "$controller@assign"]);
            $router->delete("/{programsProfileFormId}", ["uses" => "$controller@disable"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
        });
    });
    
    $router->group(['prefix' => '/activities'], function () use($router) {
        $controller = "ActivityController";
        $router->post("", ["uses" => "$controller@initiate"]);
        $router->patch("/{activityId}", ["uses" => "$controller@update"]);
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{activityId}", ["uses" => "$controller@show"]);
    });
    
    $activityAggregate = [
        'prefix' => '/activities/{activityId}',
        'namespace' => 'Activity',
    ];
    $router->group($activityAggregate, function () use ($router) {
        $router->group(['prefix' => '/invitees'], function () use($router) {
            $controller = "InviteeController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{inviteeId}", ["uses" => "$controller@show"]);
        });
    });
    
    $router->group(['prefix' => '/invitations'], function () use($router) {
        $controller = "InvitationController";
        $router->put("/{invitationId}", ["uses" => "$controller@submitReport"]);
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{invitationId}", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/meetings'], function () use($router) {
        $controller = "MeetingController";
        $router->post("", ["uses" => "$controller@initiate"]);
    });
    
    $asMeetingInitiatorAggregate = [
        'prefix' => '/as-meeting-initiator/{meetingId}',
        'namespace' => 'AsMeetingInitiator',
    ];
    $router->group($asMeetingInitiatorAggregate, function () use ($router){
        
        $router->patch("/update-meeting", ['uses' => "MeetingController@update"]);
        
        $router->group(['prefix' => '/attendees'], function () use($router) {
            $controller = "AttendeeController";
            $router->put("/invite-manager", ["uses" => "$controller@inviteManager"]);
            $router->put("/invite-coordinator", ["uses" => "$controller@inviteCoordinator"]);
            $router->put("/invite-consultant", ["uses" => "$controller@inviteConsultant"]);
            $router->put("/invite-participant", ["uses" => "$controller@inviteParticipant"]);
            $router->patch("/cancel-invitation/{attendeeId}", ["uses" => "$controller@cancel"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{attendeeId}", ["uses" => "$controller@show"]);
        });
    });
    
});

