<?php

$personnelAggregate = [
    'prefix' => '/api/personnel',
    'namespace' => 'Personnel',
    'middleware' => 'personnelJwtAuth',
];
$router->group($personnelAggregate, function () use ($router) {
    $router->get('/notifications', ['uses' => "NotificationController@showAll"]);
    $router->post('/file-uploads', ['uses' => "FileUploadController@upload"]);
    
    $router->get('/mentorings', ['uses' => "MentoringController@showAll"]);
    
    $router->get('/mentoring-slots/{id}', ['uses' => "MentoringSlotController@show"]);
    $router->get('/mentoring-slots', ['uses' => "MentoringSlotController@showAll"]);
    
    $router->post('/program-consultation/{consultantId}/mentoring-slots/create-multiple-slot', ['uses' => "MentoringSlotController@createMultipleSlot"]);
    $router->patch('/program-consultation/{consultantId}/mentoring-slots/{id}', ['uses' => "MentoringSlotController@update"]);
    $router->delete('/program-consultation/{consultantId}/mentoring-slots/{id}', ['uses' => "MentoringSlotController@cancel"]);
    
    $router->put('/program-consultation/{consultantId}/booked-mentorings/{id}/submit-report', ['uses' => "BookedMentoringController@submitReport"]);
    $router->delete('/program-consultation/{consultantId}/booked-mentorings/{id}', ['uses' => "BookedMentoringController@cancel"]);
    $router->get('/program-consultation/{consultantId}/booked-mentorings/{id}', ['uses' => "BookedMentoringController@show"]);
    
    $router->get('/consultation-requests', ['uses' => "ConsultationRequestController@showAll"]);
    $router->get('/consultation-sessions', ['uses' => "ConsultationSessionController@showAll"]);
    $router->get('/metric-assignment-reports', ['uses' => "MetricAssignmentReportController@showAll"]);
    $router->get('/registrants', ['uses' => "RegistrantController@showAll"]);
    $router->get('/activity-invitations', ['uses' => "ActivityInvitationController@showAll"]);
    
    $router->put('/dedicated-mentors/{dedicatedMentorId}/evaluation-reports/{evaluationPlanId}', ['uses' => "MentorEvaluationReportController@submit"]);
    $router->delete('/dedicated-mentors/{dedicatedMentorId}/evaluation-reports/{id}', ['uses' => "MentorEvaluationReportController@cancel"]);
    $router->get('/programs/{programId}/evaluation-reports', ['uses' => "MentorEvaluationReportController@showAll"]);
    $router->get('/evaluation-reports/{id}', ['uses' => "MentorEvaluationReportController@show"]);
    
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
    
    $router->group(['prefix' => '/client-bios'], function () use($router) {
        $controller = "ClientBioController";
        $router->get("/{clientId}", ["uses" => "$controller@showAll"]);
        $router->get("/{clientId}/{bioFormId}", ["uses" => "$controller@show"]);
    });
    
    $router->get("/team/{teamId}/members", ["uses" => "TeamMemberController@showAll"]);
    $router->get("team-members/{teamMemberId}", ["uses" => "TeamMemberController@show"]);
    
    $asProgramCoordinatorAggregate = [
        'prefix' => '/as-program-coordinator/{programId}',
        'namespace' => 'AsProgramCoordinator',
    ];
    $router->group($asProgramCoordinatorAggregate, function () use ($router) {
        
        $router->get('/participant-summary', ['uses' => "ParticipantSummaryController@showAll"]);
        $router->get('/participant-achievement-summary', ['uses' => "ParticipantSummaryController@showAllMetricAchievement"]);
        $router->get('/participant-evaluation-summary', ['uses' => "ParticipantSummaryController@showAllEvaluationSummary"]);
        $router->get('/consultant-summary', ['uses' => "ConsultantSummaryController@showAll"]);
        
        $router->group(['prefix' => '/consultation-requests'], function () use($router) {
            $controller = "ConsultationRequestController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{consultationRequestId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/consultation-sessions'], function () use($router) {
            $controller = "ConsultationSessionController";
            $router->patch("/{consultationSessionId}/change-channel", ["uses" => "$controller@changeChannel"]);
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
            $router->patch("/{participantId}/evaluate", ["uses" => "$controller@evaluate"]);
            $router->patch("/{participantId}/qualify", ["uses" => "$controller@qualify"]);
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
                $router->patch("/{metricAssignmentReportId}/reject", ["uses" => "$controller@reject"]);
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{metricAssignmentReportId}", ["uses" => "$controller@show"]);
            });
            
            $router->group(['prefix' => '/evaluation-reports'], function () use($router) {
                $controller = "EvaluationReportController";
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{evaluationReportId}", ["uses" => "$controller@show"]);
            });
            
            $router->group(['prefix' => '/evaluations'], function () use($router) {
                $controller = "EvaluationController";
                $router->post("", ["uses" => "$controller@evaluate"]);
                $router->get("", ["uses" => "$controller@showAll"]);
                $router->get("/{evaluationId}", ["uses" => "$controller@show"]);
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
        
        $router->group(['prefix' => '/evaluation-plans'], function () use($router) {
            $controller = "EvaluationPlanController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{evaluationPlanId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/activities'], function () use($router) {
            $controller = "ActivityController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{activityId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/missions'], function () use($router) {
            $controller = "MissionController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{missionId}", ["uses" => "$controller@show"]);
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
        
        $router->get("/activities/{activityId}/activity-reports", ["uses" => "ActivityReportController@showAllReportsInActivity"]);
        $router->get("/activity-reports/{activityReportId}", ["uses" => "ActivityReportController@show"]);
        
        $router->get("/registrants/{registrantId}/registrant-profiles", ["uses" => "RegistrantProfileController@showAll"]);
        $router->get("/registrant-profiles/{registrantProfileId}", ["uses" => "RegistrantProfileController@show"]);
        
        $router->get("/participants/{participantId}/participant-profiles", ["uses" => "ParticipantProfileController@showAll"]);
        $router->get("/participant-profiles/{participantProfileId}", ["uses" => "ParticipantProfileController@show"]);
        
        $router->patch("/okr-periods/{okrPeriodId}/approve", ["uses" => "OKRPeriodController@approve"]);
        $router->patch("/okr-periods/{okrPeriodId}/reject", ["uses" => "OKRPeriodController@reject"]);
        $router->get("/okr-periods/{okrPeriodId}", ["uses" => "OKRPeriodController@show"]);
        $router->get("/participants/{participantId}/okr-periods", ["uses" => "OKRPeriodController@showAllBelongsToParticipant"]);
        
        $router->patch("/objective-progress-reports/{objectiveProgressReportId}/approve", ["uses" => "ObjectiveProgressReportController@approve"]);
        $router->patch("/objective-progress-reports/{objectiveProgressReportId}/reject", ["uses" => "ObjectiveProgressReportController@reject"]);
        $router->get("/objective-progress-reports/{objectiveProgressReportId}", ["uses" => "ObjectiveProgressReportController@show"]);
        $router->get("/objectives/{objectiveId}/objective-progress-reports", ["uses" => "ObjectiveProgressReportController@showAllInObjective"]);
        
        $router->post("/participants/{participantId}/dedicated-mentors", ["uses" => "DedicatedMentorController@assign"]);
        $router->get("/participants/{participantId}/dedicated-mentors", ["uses" => "DedicatedMentorController@showAllBelongsToParticipant"]);
        $router->get("/consultants/{consultantId}/dedicated-mentors", ["uses" => "DedicatedMentorController@showAllBelongsToConsultant"]);
        $router->delete("/dedicated-mentors/{dedicatedMentorId}", ["uses" => "DedicatedMentorController@cancel"]);
        $router->get("/dedicated-mentors/{dedicatedMentorId}", ["uses" => "DedicatedMentorController@show"]);
        
        $router->get("/activities/{activityId}/attendees", ["uses" => "ActivityAttendeeController@showAll"]);
        $router->get("/attendees/{attendeeId}", ["uses" => "ActivityAttendeeController@show"]);
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
        
        $router->group(['prefix' => '/missions'], function () use($router) {
            $controller = "MissionController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{missionId}", ["uses" => "$controller@show"]);
        });
        
        $router->get("/participant/{participantId}/metric-assignment-reports", ["uses" => "MetricAssignmentReportController@showAll"]);
        $router->get("/metric-assignment-reports/{metricAssignmentReportId}", ["uses" => "MetricAssignmentReportController@show"]);
        
        $router->post('/missions/{missionId}/mission-comments', ['uses' => "MissionCommentController@submit"]);
        $router->post('/mission-comments/{missionCommentId}', ['uses' => "MissionCommentController@reply"]);
        $router->get('/missions/{missionId}/mission-comments', ['uses' => "MissionCommentController@showAll"]);
        $router->get('/mission-comments/{missionCommentId}', ['uses' => "MissionCommentController@show"]);
        
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
            $router->post("", ["uses" => "$controller@declare"]);
            $router->patch("/{consultationSessionId}/cancel", ["uses" => "$controller@cancel"]);
            $router->patch("/{consultationSessionId}/deny", ["uses" => "$controller@deny"]);
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
        
        $router->get("/dedicated-mentors", ["uses" => "DedicatedMentorController@showAll"]);
        $router->get("/dedicated-mentors/{dedicatedMentorId}", ["uses" => "DedicatedMentorController@show"]);
        
        $router->get("/evaluation-plans", ["uses" => "EvaluationPlanController@showAll"]);
        $router->get("/evaluation-plans/{evaluationPlanId}", ["uses" => "EvaluationPlanController@show"]);
        
        $router->get("/consultation-setups", ["uses" => "ConsultationSetupController@showAll"]);
        $router->get("/consultation-setups/{id}", ["uses" => "ConsultationSetupController@show"]);
        
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
        
        $router->group(['prefix' => '/evaluation-reports'], function () use($router) {
            $controller = "EvaluationReportController";
            $router->put("/{participantId}/{evaluationPlanId}", ["uses" => "$controller@submit"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{participantId}/{evaluationPlanId}", ["uses" => "$controller@show"]);
        });
        
        $router->group(['prefix' => '/notifications'], function () use($router) {
            $controller = "NotificationController";
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
        $router->get("/mentor-evaluation-reports/summary", ["uses" => "MentorEvaluationReportController@summary"]);
        $router->get("/mentor-evaluation-reports/download-xls-summary", ["uses" => "MentorEvaluationReportController@downloadXlsSummary"]);
        
        $router->get("/evaluation-report-transcript", ["uses" => "ParticipantEvaluationReportTranscriptController@transcript"]);
        $router->get("/download-evaluation-report-transcript-xls", ["uses" => "ParticipantEvaluationReportTranscriptController@downloadXlsTranscript"]);
    });
    
    $asConsultantMeetingInitiatorAggregate = [
        'prefix' => '/as-consultant-meeting-initiator/{meetingId}',
        'namespace' => 'AsConsultantMeetingInitiator',
    ];
    $router->group($asConsultantMeetingInitiatorAggregate, function () use ($router){
        
        $router->patch("/update-meeting", ['uses' => "MeetingController@update"]);
        
        $router->group(['prefix' => '/attendees'], function () use($router) {
            $controller = "AttendeeController";
            $router->put("/invite-manager", ["uses" => "$controller@inviteManager"]);
            $router->put("/invite-coordinator", ["uses" => "$controller@inviteCoordinator"]);
            $router->put("/invite-consultant", ["uses" => "$controller@inviteConsultant"]);
            $router->put("/invite-participant", ["uses" => "$controller@inviteParticipant"]);
            $router->put("/invite-all-active-dedicated-mentees", ["uses" => "$controller@inviteAllActiveDedicatedMentees"]);
            $router->patch("/cancel-invitation/{attendeeId}", ["uses" => "$controller@cancel"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{attendeeId}", ["uses" => "$controller@show"]);
        });
    });
    
    $asCoordinatorMeetingInitiatorAggregate = [
        'prefix' => '/as-coordinator-meeting-initiator/{meetingId}',
        'namespace' => 'AsCoordinatorMeetingInitiator',
    ];
    $router->group($asCoordinatorMeetingInitiatorAggregate, function () use ($router){
        
        $router->patch("/update-meeting", ['uses' => "MeetingController@update"]);
        
        $router->group(['prefix' => '/attendees'], function () use($router) {
            $controller = "AttendeeController";
            $router->put("/invite-manager", ["uses" => "$controller@inviteManager"]);
            $router->put("/invite-coordinator", ["uses" => "$controller@inviteCoordinator"]);
            $router->put("/invite-consultant", ["uses" => "$controller@inviteConsultant"]);
            $router->put("/invite-participant", ["uses" => "$controller@inviteParticipant"]);
            $router->put("/invite-all-active-program-participants", ["uses" => "$controller@inviteAllActiveProgramParticipants"]);
            $router->patch("/cancel-invitation/{attendeeId}", ["uses" => "$controller@cancel"]);
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{attendeeId}", ["uses" => "$controller@show"]);
        });
    });
    
});
