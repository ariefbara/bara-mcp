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
    
    $router->group(['prefix' => '/programs'], function () use($router) {
        $controller = "ProgramController";
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/team-types", ["uses" => "$controller@showAllProgramForTeam"]);
        $router->get("/{programId}", ["uses" => "$controller@show"]);
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
    
    $router->group(['prefix' => '/managers'], function () use($router) {
        $controller = "ManagerController";
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{managerId}", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/cv'], function () use($router) {
        $controller = "ClientCVController";
        $router->put("/{clientCVFormId}", ["uses" => "$controller@submit"]);
        $router->delete("/{clientCVFormId}", ["uses" => "$controller@remove"]);
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{clientCVFormId}", ["uses" => "$controller@show"]);
    });
    
    $programParticipationAggregate = [
        'prefix' => '/program-participations/{programParticipationId}',
        'namespace' => 'ProgramParticipation',
    ];
    $router->group($programParticipationAggregate, function () use ($router) {
        
        $router->get('/activity-logs', ['uses' => "ActivityLogController@showAll"]);
        
        $router->group(['prefix' => '/worksheets'], function () use($router) {
            $controller = "WorksheetController";
            $router->post("", ["uses" => "$controller@addRoot"]);
            $router->post("/{worksheetId}", ["uses" => "$controller@addBranch"]);
            $router->patch("/{worksheetId}", ["uses" => "$controller@update"]);
            $router->delete("/{worksheetId}", ["uses" => "$controller@remove"]);
            $router->get("", ["uses" => "$controller@showAll"]);
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
                $router->get("/{commentId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
        });
        
        $router->group(['prefix' => '/consultation-requests'], function () use($router) {
            $controller = "ConsultationRequestController";
            $router->post("", ["uses" => "$controller@submit"]);
            $router->patch("/{consultationRequestId}/cancel", ["uses" => "$controller@cancel"]);
            $router->patch("/{consultationRequestId}/change-time", ["uses" => "$controller@changeTime"]);
            $router->patch("/{consultationRequestId}/accept", ["uses" => "$controller@accept"]);
            $router->get("/{consultationRequestId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/consultation-sessions'], function () use($router) {
            $controller = "ConsultationSessionController";
            $router->put("/{consultationSessionId}/submit-report", ["uses" => "$controller@submitReport"]);
            $router->get("/{consultationSessionId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/metric-assignment-reports'], function () use($router) {
            $controller = "MetricAssignmentReportController";
            $router->post("", ["uses" => "$controller@submit"]);
            $router->patch("/{metricAssignmentReportId}", ["uses" => "$controller@update"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{metricAssignmentReportId}", ["uses" => "$controller@show"]);
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
        
        $router->group(['prefix' => '/profiles'], function () use($router) {
            $controller = "ProfileController";
            $router->put("/{programsProfileFormId}", ["uses" => "$controller@submit"]);
            $router->delete("/{programsProfileFormId}", ["uses" => "$controller@remove"]);
            $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
    });
    
    $programRegistrationAggregate = [
        'prefix' => '/program-registrations/{programRegistrationId}',
        'namespace' => 'ProgramRegistration',
    ];
    $router->group($programRegistrationAggregate, function () use ($router) {
        
        $router->group(['prefix' => '/profiles'], function () use($router) {
            $controller = "ProfileController";
            $router->put("/{programsProfileFormId}", ["uses" => "$controller@submit"]);
            $router->delete("/{programsProfileFormId}", ["uses" => "$controller@remove"]);
            $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
    });
    
    $asProgramRegistrantAggregate = [
        'prefix' => '/as-program-registrant/{programId}',
        'namespace' => 'AsProgramRegistrant',
    ];
    $router->group($asProgramRegistrantAggregate, function () use ($router) {
        
        $router->group(['prefix' => '/programs-profile-forms'], function () use($router) {
            $controller = "ProgramsProfileFormController";
            $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
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
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/by-position/{position}", ["uses" => "$controller@showByPosition"]);
            $router->get("/by-id/{missionId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/activity-types'], function () use($router) {
            $controller = "ActivityTypeController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{activityTypeId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/coordinators'], function () use($router) {
            $controller = "CoordinatorController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{coordinatorId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/participants'], function () use($router) {
            $controller = "ParticipantController";
            $router->get("/{participantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/consultation-setups'], function () use($router) {
            $controller = "ConsultationSetupController";
            $router->get("/{consultationSetupId}", ["uses" => "$controller@show"]);
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
        
        $router->group(['prefix' => '/meetings'], function () use($router) {
            $controller = "MeetingController";
            $router->post("", ["uses" => "$controller@initiate"]);
        });
        
        $router->group(['prefix' => '/programs-profile-forms'], function () use($router) {
            $controller = "ProgramsProfileFormController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
        });
        
    });
    
    $asTeamMemberAggregate = [
        'prefix' => '/as-team-member/{teamId}',
        'namespace' => 'AsTeamMember',
    ];
    $router->group($asTeamMemberAggregate, function () use ($router) {
        
        $router->post('/file-uploads', ['uses' => "FileUploadController@upload"]);
        
        $router->group(['prefix' => '/find-client-by-email'], function () use($router) {
            $controller = "FindClientByEmailController";
            $router->get("", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/members'], function () use($router) {
            $controller = "MemberController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->delete("/{memberId}", ["uses" => "$controller@remove"]);
            $router->get("/{memberId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/programs'], function () use($router) {
            $controller = "ProgramController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{programId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/program-registrations'], function () use($router) {
            $controller = "ProgramRegistrationController";
            $router->post("", ["uses" => "$controller@register"]);
            $router->patch("/{teamProgramRegistrationId}/cancel", ["uses" => "$controller@cancel"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{teamProgramRegistrationId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/program-participations'], function () use($router) {
            $controller = "ProgramParticipationController";
            $router->patch("/{teamProgramParticipationId}/quit", ["uses" => "$controller@quit"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{teamProgramParticipationId}", ["uses" => "$controller@show"]);
        });
        
        $asProgramParticipantAggregate = [
            'prefix' => '/as-program-participant/{programId}',
            'namespace' => 'AsProgramParticipant',
        ];
        $router->group($asProgramParticipantAggregate, function () use ($router) {
            
            $router->group(['prefix' => '/missions'], function () use($router) {
                $controller = "MissionController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/by-id/{missionId}", ["uses" => "$controller@show"]);
                $router->get("/by-position/{position}", ["uses" => "$controller@showByPosition"]);
            });
            
            $router->group(['prefix' => '/activity-types'], function () use($router) {
                $controller = "ActivityTypeController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{activityTypeId}", ["uses" => "$controller@show"]);
            });

            $router->group(['prefix' => '/coordinators'], function () use($router) {
                $controller = "CoordinatorController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{coordinatorId}", ["uses" => "$controller@show"]);
            });
            
            $router->group(['prefix' => '/consultants'], function () use($router) {
                $controller = "ConsultantController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{consultantId}", ["uses" => "$controller@show"]);
            });

            $router->group(['prefix' => '/participants'], function () use($router) {
                $controller = "ParticipantController";
                $router->get("/{participantId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            
            $router->group(['prefix' => '/consultation-setups'], function () use($router) {
                $controller = "ConsultationSetupController";
                $router->get("/{consultationSetupId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            
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
            
            $router->group(['prefix' => '/meetings'], function () use($router) {
                $controller = "MeetingController";
                $router->post("", ["uses" => "$controller@initiate"]);
            });
            
            $router->group(['prefix' => '/programs-profile-forms'], function () use($router) {
                $controller = "ProgramsProfileFormController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
            });
            
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
            
            $router->group(['prefix' => '/metric-assignment-reports'], function () use($router) {
                $controller = "MetricAssignmentReportController";
                $router->post("", ["uses" => "$controller@submit"]);
                $router->patch("/{metricAssignmentReportId}", ["uses" => "$controller@update"]);
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{metricAssignmentReportId}", ["uses" => "$controller@show"]);
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
            
            $router->group(['prefix' => '/profiles'], function () use($router) {
                $controller = "ProfileController";
                $router->put("/{programsProfileFormId}", ["uses" => "$controller@submit"]);
                $router->delete("/{programsProfileFormId}", ["uses" => "$controller@remove"]);
                $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
        });
        
        $programRegistrationAggregate = [
            'prefix' => '/program-registrations/{programRegistrationId}',
            'namespace' => 'ProgramRegistration',
        ];
        $router->group($programRegistrationAggregate, function () use ($router) {

            $router->group(['prefix' => '/profiles'], function () use($router) {
                $controller = "ProfileController";
                $router->put("/{programsProfileFormId}", ["uses" => "$controller@submit"]);
                $router->delete("/{programsProfileFormId}", ["uses" => "$controller@remove"]);
                $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });

        });
        
        $asProgramRegistrantAggregate = [
            'prefix' => '/as-program-registrant/{programId}',
            'namespace' => 'AsProgramRegistrant',
        ];
        $router->group($asProgramRegistrantAggregate, function () use ($router) {

            $router->group(['prefix' => '/programs-profile-forms'], function () use($router) {
                $controller = "ProgramsProfileFormController";
                $router->get("/{programsProfileFormId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });

        });
        
    });
});
