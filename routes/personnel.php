<?php

$personnelAggregate = [
    'prefix' => '/api/personnel',
    'namespace' => 'Personnel',
    'middleware' => 'personnelJwtAuth',
];
$router->group($personnelAggregate, function () use ($router) {
    $router->get('/notifications', ['uses' => "NotificationController@showAll"]);
    $router->post('/file-uploads', ['uses' => "FileUploadController@upload"]);
    
    $router->group(['prefix' => '/profile'], function () use($router) {
        $controller = "AccountController";
        $router->patch("/update", ["uses" => "AccountController@updateProfile"]);
        $router->patch("/change-password", ["uses" => "AccountController@changePassword"]);
        $router->get("", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/managers'], function () use($router) {
        $controller = "ManagerController";
        $router->get("", ["uses" => "$controller@showAll"]);
        $router->get("/{managerId}", ["uses" => "$controller@show"]);
    });
    
    $asProgramCoordinatorAggregate = [
        'prefix' => '/as-program-coordinator/{programId}',
        'namespace' => 'AsProgramCoordinator',
    ];
    $router->group($asProgramCoordinatorAggregate, function () use ($router) {
        
        $router->get('/participant-summary', ['uses' => "ParticipantSummaryController@showAll"]);
        $router->get('/participant-achievement-summary', ['uses' => "ParticipantSummaryController@showAllMetricAchievement"]);
        $router->get('/consultant-summary', ['uses' => "ConsultantSummaryController@showAll"]);
        
        $router->group(['prefix' => '/consultation-requests'], function () use($router) {
            $controller = "ConsultationRequestController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{consultationRequestId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/consultation-sessions'], function () use($router) {
            $controller = "ConsultationSessionController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{consultationSessionId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/metrics'], function () use($router) {
            $controller = "MetricController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{metricId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/participants'], function () use($router) {
            $controller = "ParticipantController";
            $router->put("/{participantId}/assign-metric", ["uses" => "$controller@assignMetric"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{participantId}", ["uses" => "$controller@show"]);
        });
        
        $participantAggregate = [
            'prefix' => '/participants/{participantId}',
            'namespace' => 'Participant',
        ];
        $router->group($participantAggregate, function () use ($router) {
            
            $router->group(['prefix' => '/worksheets'], function () use($router) {
                $controller = "WorksheetController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{worksheetId}", ["uses" => "$controller@show"]);
            });
            
            $router->group(['prefix' => '/metric-assignment-reports'], function () use($router) {
                $controller = "MetricAssignmentReportController";
                $router->patch("/{metricAssignmentReportId}/approve", ["uses" => "$controller@approve"]);
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{metricAssignmentReportId}", ["uses" => "$controller@show"]);
            });
            
        });
        
        $router->group(['prefix' => '/registrants'], function () use($router) {
            $controller = "RegistrantController";
            $router->patch("/{registrantId}/accept", ["uses" => "$controller@accept"]);
            $router->patch("/{registrantId}/reject", ["uses" => "$controller@reject"]);
            $router->get("/{registrantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/activity-types'], function () use($router) {
            $controller = "ActivityTypeController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{activityTypeId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/consultants'], function () use($router) {
            $controller = "ConsultantController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{consultantId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/coordinators'], function () use($router) {
            $controller = "CoordinatorController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{coordinatorId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/meetings'], function () use($router) {
            $controller = "MeetingController";
            $router->post("", ["uses" => "$controller@initiate"]);
        });
        
    });
    
    $asProgramConsultantAggregate = [
        'prefix' => '/as-program-consultant/{programId}',
        'namespace' => 'AsProgramConsultant',
    ];
    $router->group($asProgramConsultantAggregate, function () use ($router) {
        $router->group(['prefix' => '/participants'], function () use($router) {
            $controller = "ParticipantController";
            $router->get("/{participantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $participantAggregate = [
            'prefix' => '/participants/{participantId}',
            'namespace' => 'Participant',
        ];
        $router->group($participantAggregate, function () use ($router) {
            $router->group(['prefix' => '/worksheets'], function () use($router) {
                $controller = "WorksheetController";
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
                    $router->get("/{commentId}", ["uses" => "$controller@show"]);
                    $router->get("", ["uses" => "$controller@showAll"]);
                });
            });
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
        
        $router->group(['prefix' => '/meetings'], function () use($router) {
            $controller = "MeetingController";
            $router->post("", ["uses" => "$controller@initiate"]);
        });
        
    });
    
    $programConsultationAggregate = [
        'prefix' => '/program-consultations/{programConsultationId}',
        'namespace' => 'ProgramConsultation',
    ];
    $router->group($programConsultationAggregate, function () use ($router) {
        $router->get("/activity-logs", ["uses" => "ActivityLogController@showAll"]);
        
        $router->group(['prefix' => '/participants'], function () use($router) {
            $controller = "ParticipantController";
            $router->get("/{participantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $participantAggregate = [
            'prefix' => '/participants/{participantId}',
            'namespace' => 'Participant',
        ];
        $router->group($participantAggregate, function () use ($router) {
            $router->group(['prefix' => '/worksheets'], function () use($router) {
                $controller = "WorksheetController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/roots", ["uses" => "$controller@showAllRoots"]);
                $router->get("/{worksheetId}", ["uses" => "$controller@show"]);
                $router->get("/{worksheetId}/branches", ["uses" => "$controller@showBranches"]);
            });
        });
        
        $router->group(['prefix' => '/consultation-requests'], function () use($router) {
            $controller = "ConsultationRequestController";
            $router->patch("/{consultationRequestId}/accept", ["uses" => "$controller@accept"]);
            $router->patch("/{consultationRequestId}/offer", ["uses" => "$controller@offer"]);
            $router->patch("/{consultationRequestId}/reject", ["uses" => "$controller@reject"]);
            $router->get("/{consultationRequestId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/consultation-sessions'], function () use($router) {
            $controller = "ConsultationSessionController";
            $router->put("/{consultationSessionId}/submit-report", ["uses" => "$controller@setConsultantFeedback"]);
            $router->get("/{consultationSessionId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->group(['prefix' => '/consultant-comments'], function () use($router) {
            $controller = "ConsultantCommentController";
            $router->post("/new", ["uses" => "$controller@submitNew"]);
            $router->post("/reply", ["uses" => "$controller@submitReply"]);
            $router->delete("/{consultantCommentId}", ["uses" => "$controller@remove"]);
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
        
    });
    
    $coordinatorAggregate = [
        'prefix' => '/coordinators/{coordinatorId}',
        'namespace' => 'Coordinator',
    ];
    $router->group($coordinatorAggregate, function () use ($router) {
        
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
