<?php

$clientAggregate = [
    'prefix' => '/api/client',
    'namespace' => 'Client',
    'middleware' => 'clientJwtAuth',
];
$router->group($clientAggregate, function () use ($router) {
    $router->patch("/update-profile", ["uses" => "AccountController@updateProfile"]);
    $router->patch("/change-password", ["uses" => "AccountController@changePassword"]);
    $router->post('/file-uploads', ['uses' => "FileUploadController@upload"]);
    $router->get('/notifications', ['uses' => "NotificationController@showAll"]);
    
    $router->group(['prefix' => '/create-team'], function () use($router) {
        $controller = "CreateTeamController";
        $router->post("", ["uses" => "$controller@create"]);
    });
    $router->group(['prefix' => '/team-memberships'], function () use($router) {
        $controller = "TeamMembershipController";
        $router->delete("/{teamMembershipId}", ["uses" => "$controller@quit"]);
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{teamMembershipId}", ["uses" => "$controller@show"]);
    });
    $teamMembershipAggregate = [
        'prefix' => '/team-memberships/{teamMembershipId}',
        'namespace' => 'TeamMembership',
    ];
    $router->group($teamMembershipAggregate, function () use ($router) {
        
        $router->post('/file-uploads', ['uses' => "FileUploadController@upload"]);
        
        $router->group(['prefix' => '/find-client-by-email'], function () use($router) {
            $controller = "FindClientByEmailController";
            $router->get("", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/programs'], function () use($router) {
            $controller = "ProgramController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{teamprogramRegistrationId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/program-registrations'], function () use($router) {
            $controller = "ProgramRegistrationController";
            $router->post("", ["uses" => "$controller@register"]);
            $router->patch("/{teamProgramRegistrationId}/cancel", ["uses" => "$controller@cancel"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{teamprogramRegistrationId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/program-participations'], function () use($router) {
            $controller = "ProgramParticipationController";
            $router->patch("/{teamProgramParticipationId}/quit", ["uses" => "$controller@quit"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{teamProgramParticipationId}", ["uses" => "$controller@show"]);
        });
        $teamProgramParticipationAggregate = [
            'prefix' => '/program-participations/{teamProgramParticipationId}',
            'namespace' => 'ProgramParticipation',
        ];
        $router->group($teamProgramParticipationAggregate, function () use ($router) {
            
            $router->group(['prefix' => '/activity-logs'], function () use($router) {
                $controller = "ActivityLogController";
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            
            $router->group(['prefix' => '/missions'], function () use($router) {
                $controller = "MissionController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/by-id/{missionId}", ["uses" => "$controller@show"]);
                $router->get("/by-position/{position}", ["uses" => "$controller@showByPosition"]);
            });
            
            $router->group(['prefix' => '/worksheets'], function () use($router) {
                $controller = "WorksheetController";
                $router->post("", ["uses" => "$controller@submitRoot"]);
                $router->post("/{worksheetId}", ["uses" => "$controller@submitBranch"]);
                $router->patch("/{worksheetId}", ["uses" => "$controller@update"]);
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/roots", ["uses" => "$controller@showAllRoots"]);
                $router->get("/{worksheetId}/branches", ["uses" => "$controller@showAllBranches"]);
                $router->get("/{worksheetId}", ["uses" => "$controller@show"]);
            });
            $worksheetAggregate = [
                'prefix' => '/worksheets/{worksheetId}',
                'namespace' => 'Worksheet',
            ];
            $router->group($worksheetAggregate, function () use ($router) {
                $router->group(['prefix' => '/comments'], function () use($router) {
                    $controller = "CommentController";
                    $router->post("", ["uses" => "$controller@submitNew"]);
                    $router->post("/{commentId}", ["uses" => "$controller@reply"]);
                    $router->get("", ["uses" => "$controller@showAll"]);
                    $router->get("/{commentId}", ["uses" => "$controller@show"]);
                });
                
            });
            
            $router->group(['prefix' => '/consultation-requests'], function () use($router) {
                $controller = "ConsultationRequestController";
                $router->post("", ["uses" => "$controller@submit"]);
                $router->patch("/{consultationRequestId}/change-time", ["uses" => "$controller@changeTime"]);
                $router->patch("/{consultationRequestId}/cancel", ["uses" => "$controller@cancel"]);
                $router->patch("/{consultationRequestId}/accept", ["uses" => "$controller@accept"]);
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{consultationRequestId}", ["uses" => "$controller@show"]);
            });
            
            $router->group(['prefix' => '/consultation-sessions'], function () use($router) {
                $controller = "ConsultationSessionController";
                $router->put("/{consultationSessionId}/submit-report", ["uses" => "$controller@submitReport"]);
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{consultationSessionId}", ["uses" => "$controller@show"]);
            });
            
        });
        
    });
    
    $router->group(['prefix' => '/programs'], function () use($router) {
        $controller = "ProgramController";
        $router->get("/{programId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    
    $router->group(['prefix' => '/program-registrations'], function () use($router) {
        $controller = "ProgramRegistrationController";
        $router->post("", ["uses" => "$controller@register"]);
        $router->patch("/{programRegistrationId}/cancel", ["uses" => "$controller@cancel"]);
        $router->get("/{programRegistrationId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    
    $router->group(['prefix' => '/program-participations'], function () use($router) {
        $controller = "ProgramParticipationController";
        $router->patch("/{programParticipationId}/quit", ["uses" => "$controller@quit"]);
        $router->get("/{programParticipationId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    
    $programParticipationAggregate = [
        'prefix' => '/program-participations/{programParticipationId}',
        'namespace' => 'ProgramParticipation',
    ];
    $router->group($programParticipationAggregate, function () use ($router) {
        
        $router->group(['prefix' => '/worksheets'], function () use($router) {
            $controller = "WorksheetController";
            $router->post("", ["uses" => "$controller@addRoot"]);
            $router->post("/{worksheetId}", ["uses" => "$controller@addBranch"]);
            $router->patch("/{worksheetId}", ["uses" => "$controller@update"]);
            $router->delete("/{worksheetId}", ["uses" => "$controller@remove"]);
            $router->get("/{worksheetId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $worksheetAggregate = [
            'prefix' => '/worksheets/{worksheetId}',
            'namespace' => 'Worksheet',
        ];
        $router->group($worksheetAggregate, function () use ($router) {
            $router->group(['prefix' => '/comments'], function () use($router) {
                $controller = "CommentController";
                $router->post("", ["uses" => "$controller@submitNew"]);
                $router->post("/{commentId}", ["uses" => "$controller@reply"]);
                $router->get("/{commentId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
        });
        
        $router->group(['prefix' => '/consultation-requests'], function () use($router) {
            $controller = "ConsultationRequestController";
            $router->post("", ["uses" => "$controller@propose"]);
            $router->patch("/{consultationRequestId}/cancel", ["uses" => "$controller@cancel"]);
            $router->patch("/{consultationRequestId}/repropose", ["uses" => "$controller@rePropose"]);
            $router->patch("/{consultationRequestId}/accept", ["uses" => "$controller@accept"]);
            $router->get("/{consultationRequestId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/consultation-sessions'], function () use($router) {
            $controller = "ConsultationSessionController";
            $router->put("/{consultationSessionId}/participant-feedback", ["uses" => "$controller@setParticipantFeedback"]);
            $router->get("/{consultationSessionId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->delete('/participant-comments/{participantCommentId}', ['uses' => "ParticipantCommentController@remove"]);
        
        $router->group(['prefix' => '/missions'], function () use($router) {
            $controller = "MissionController";
            $router->get("/by-position/{position}", ["uses" => "$controller@showByPosition"]);
            $router->get("/by-id/{missionId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
    });
    
    $asProgramParticipantAggregate = [
        'prefix' => '/as-program-participant/{programId}',
        'namespace' => 'AsProgramParticipant',
    ];
    $router->group($asProgramParticipantAggregate, function () use ($router) {
        $router->group(['prefix' => '/consultants'], function () use($router) {
            $controller = "ConsultantController";
            $router->get("/{consultantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/missions'], function () use($router) {
            $controller = "MissionController";
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
                $router->get("/{learningMaterialId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
        });
        
        $router->group(['prefix' => '/consultation-setups'], function () use($router) {
            $controller = "ConsultationSetupController";
            $router->get("/{consultationSetupId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
    });
    
    $asTeamAdminAggregate = [
        'prefix' => '/as-team-admin/{teamId}',
        'namespace' => 'AsTeamAdmin',
    ];
    $router->group($asTeamAdminAggregate, function () use ($router) {
        $router->group(['prefix' => '/members'], function () use($router) {
            $controller = "MemberController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->delete("/{memberId}", ["uses" => "$controller@remove"]);
            $router->get("/{memberId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
    });
    
    $asTeamMemberAggregate = [
        'prefix' => '/as-team-member/{teamId}',
        'namespace' => 'AsTeamMember',
    ];
    $router->group($asTeamMemberAggregate, function () use ($router) {
        
        $asProgramParticipantAggregate = [
            'prefix' => '/as-program-participant/{programId}',
            'namespace' => 'AsProgramParticipant',
        ];
        
        $router->group($asProgramParticipantAggregate, function () use ($router) {
            $missionAggregate = [
                'prefix' => '/missions/{missionId}',
                'namespace' => 'Mission',
            ];
            $router->group($missionAggregate, function () use ($router) {
                $router->group(['prefix' => '/learning-materials'], function () use($router) {
                    $controller = "LearningMaterialController";
                    $router->get("", ["uses" => "$controller@showAll"]);
                    $router->get("/{learningMaterialId}", ["uses" => "$controller@show"]);
                });
            });
            
        });
        
    });
});
