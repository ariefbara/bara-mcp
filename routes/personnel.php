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
    
    $router->post('/mentors/{mentorId}/mentoring-requests', ['uses' => "MentoringRequestController@propose"]);
    $router->patch('/mentors/{mentorId}/mentoring-requests/{id}/approve', ['uses' => "MentoringRequestController@approve"]);
    $router->patch('/mentors/{mentorId}/mentoring-requests/{id}/reject', ['uses' => "MentoringRequestController@reject"]);
    $router->patch('/mentors/{mentorId}/mentoring-requests/{id}/offer', ['uses' => "MentoringRequestController@offer"]);
    $router->get('/mentoring-requests/all-unresponded', ['uses' => "MentoringRequestController@showAllUnresponded"]);
    $router->get('/mentoring-requests/{id}', ['uses' => "MentoringRequestController@show"]);
    
    $router->post('/mentors/{mentorId}/declared-mentorings', ['uses' => "DeclaredMentoringController@declare"]);
    $router->patch('/mentors/{mentorId}/declared-mentorings/{id}/update', ['uses' => "DeclaredMentoringController@update"]);
    $router->patch('/mentors/{mentorId}/declared-mentorings/{id}/cancel', ['uses' => "DeclaredMentoringController@cancel"]);
    $router->patch('/mentors/{mentorId}/declared-mentorings/{id}/approve', ['uses' => "DeclaredMentoringController@approve"]);
    $router->patch('/mentors/{mentorId}/declared-mentorings/{id}/deny', ['uses' => "DeclaredMentoringController@deny"]);
    $router->put('/mentors/{mentorId}/declared-mentorings/{id}/submit-report', ['uses' => "DeclaredMentoringController@submitReport"]);
    $router->get('declared-mentorings/{id}', ['uses' => "DeclaredMentoringController@show"]);
    
    $router->post('/mentors/{mentorId}/consultant-notes', ['uses' => "ConsultantNoteController@submit"]);
    $router->patch('/mentors/{mentorId}/consultant-notes/{id}/update', ['uses' => "ConsultantNoteController@update"]);
    $router->patch('/mentors/{mentorId}/consultant-notes/{id}/show-to-participant', ['uses' => "ConsultantNoteController@showToParticipant"]);
    $router->patch('/mentors/{mentorId}/consultant-notes/{id}/hide-from-participant', ['uses' => "ConsultantNoteController@hideFromParticipant"]);
    $router->delete('/mentors/{mentorId}/consultant-notes/{id}', ['uses' => "ConsultantNoteController@remove"]);
    $router->get('consultant-notes', ['uses' => "ConsultantNoteController@showAll"]);
    $router->get('consultant-notes/{id}', ['uses' => "ConsultantNoteController@show"]);
    
    $router->post('/coordinators/{coordinatorId}/coordinator-notes', ['uses' => "CoordinatorNoteController@submit"]);
    $router->patch('/coordinators/{coordinatorId}/coordinator-notes/{id}/update', ['uses' => "CoordinatorNoteController@update"]);
    $router->patch('/coordinators/{coordinatorId}/coordinator-notes/{id}/show-to-participant', ['uses' => "CoordinatorNoteController@showToParticipant"]);
    $router->patch('/coordinators/{coordinatorId}/coordinator-notes/{id}/hide-from-participant', ['uses' => "CoordinatorNoteController@hideFromParticipant"]);
    $router->delete('/coordinators/{coordinatorId}/coordinator-notes/{id}', ['uses' => "CoordinatorNoteController@remove"]);
    $router->get('coordinator-notes', ['uses' => "CoordinatorNoteController@showAll"]);
    $router->get('coordinator-notes/{id}', ['uses' => "CoordinatorNoteController@show"]);
    
    $router->post('/mentors/{mentorId}/submit-note', ['uses' => "CnosultantNoteController@submit"]);
    $router->patch('/mentors/{mentorId}/declared-mentorings/{id}/update', ['uses' => "DeclaredMentoringController@update"]);
    $router->patch('/mentors/{mentorId}/declared-mentorings/{id}/cancel', ['uses' => "DeclaredMentoringController@cancel"]);
    $router->patch('/mentors/{mentorId}/declared-mentorings/{id}/approve', ['uses' => "DeclaredMentoringController@approve"]);
    $router->patch('/mentors/{mentorId}/declared-mentorings/{id}/deny', ['uses' => "DeclaredMentoringController@deny"]);
    $router->put('/mentors/{mentorId}/declared-mentorings/{id}/submit-report', ['uses' => "DeclaredMentoringController@submitReport"]);
    $router->get('declared-mentorings/{id}', ['uses' => "DeclaredMentoringController@show"]);
    
    $router->put('/mentors/{mentorId}/negotiated-mentorings/{id}/submit-report', ['uses' => "NegotiatedMentoringController@submitReport"]);
    $router->get('/negotiated-mentorings/{id}', ['uses' => "NegotiatedMentoringController@show"]);
    
    $router->get('/dedicated-mentee-worksheets/all-uncommented', ['uses' => "DedicatedMenteeWorksheetController@showAllUncommented"]);
    
    $router->get('/dedicated-mentees/summary', ['uses' => "DedicatedMenteeController@allWithSummary"]);
    
    $router->get('/consultant-invitees/all-with-pending-report', ['uses' => "ConsultantInviteeController@allWithPendingReport"]);
    
    $router->get('/mentor-dashboard', ['uses' => "MentorDashboardController@view"]);
    
    $router->get('/missions/discussion-overview', ['uses' => "MissionController@viewDiscussionOverview"]);
    
    $router->get('/manageable-new-applicants', ['uses' => "ManageableNewApplicantController@viewAll"]);
    $router->get('/unconcluded-mentoring-request-in-manageable-program', ['uses' => "UnconcludedMentoringRequestInManageableProgramController@viewAll"]);
    $router->get('/uncommented-worksheet-list-in-coordinated-programs', ['uses' => "UncommentedWorksheetListInCoordinatedProgramsController@viewAll"]);
    $router->get('/unreviewed-metric-report-list-in-coordinated-programs', ['uses' => "UnreviewedMetricReportListInCoordinatedProgramController@viewAll"]);
    $router->get('/coordinator-dashboard-summary', ['uses' => "CoordinatorDashboardSummaryController@view"]);
    $router->get('/coordinated-programs-summary', ['uses' => "CoordinatedProgramsSummaryController@view"]);
    
    // task related route in personnel BC
    $router->get('/tasks-list-in-coordinated-programs', ['uses' => "TaskController@viewTaskListInAllCoordinatedProgram"]);
    $router->get('/task-list-in-consulted-programs', ['uses' => "TaskController@viewTaskListInAllConsultedProgram"]);
    
    // notes related route in personnel BC
    $router->get('/notes-list-in-coordinated-programs', ['uses' => "NoteController@viewTaskListInCoordinatedPrograms"]);
    $router->get('/notes-list-in-consulted-programs', ['uses' => "NoteController@viewTaskListInConsultedPrograms"]);
    
    // worksheet related route in personnel BC
    $router->get('/worksheet-list-in-coordinated-programs', ['uses' => "WorksheetController@viewListInCoordinatedProgram"]);
    $router->get('/worksheet-list-in-consulted-programs', ['uses' => "WorksheetController@viewListInConsultedProgram"]);
    
    // participant related route in personnel BC
    $router->get('/participant-summary-list-in-coordinated-program', ['uses' => "ParticipantController@viewSummaryListInCoordinatedProgram"]);
    $router->get('/participant-list-in-coordinated-program', ['uses' => "ParticipantController@listInCoordinatedProgram"]);
    $router->get('/dedicated-mentee-list', ['uses' => "ParticipantController@dedicatedMenteeList"]);
    
    // metric assignment report related route in personnel BC
    $router->get('/metric-assignment-report-list-in-coordinated-programs', ['uses' => "MetricAssignmentReportController@viewListInCoordinatedPrograms"]);
    $router->get('/metric-assignment-report-list-in-consulted-programs', ['uses' => "MetricAssignmentReportController@viewListInConsultedPrograms"]);
    
    //mentoring route in personnel BC
    $router->get('/mentoring-list-in-coordinated-programs', ['uses' => "MentoringController@mentoringListInCoordinatedPrograms"]);
    $router->get('/summary-of-owned-mentoring', ['uses' => "MentoringController@summaryOfOwnedMentoring"]);
    $router->get('/owned-mentoring-list', ['uses' => "MentoringController@ownedMentoringList"]);
    
    //program route in personnel BC
    $router->get('/list-of-coordinated-program', ['uses' => "ProgramController@listOfCoordinatedProgram"]);
    $router->get('/list-of-consulted-program', ['uses' => "ProgramController@listOfConsultedProgram"]);
    
    //mission related route in personnel BC
    $router->get('/mission-list-in-coordinated-program', ['uses' => "MissionController@missionListInCoordinatedProgram"]);
    $router->get('/mission-list-in-consulted-program', ['uses' => "MissionController@missionListInConsultedProgram"]);
    
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
        
//        $router->group(['prefix' => '/activities'], function () use($router) {
//            $controller = "ActivityController";
//            $router->post("", ["uses" => "$controller@initiate"]);
//            $router->patch("/{activityId}", ["uses" => "$controller@update"]);
//            $router->get("", ["uses" => "$controller@showAll"]);
//            $router->get("/{activityId}", ["uses" => "$controller@show"]);
//        });

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
        
        $router->get("/activities/{activityId}", ["uses" => "ActivityController@viewActivityDetail"]);
        
        $router->get("/mentor-evaluation-reports/summary", ["uses" => "MentorEvaluationReportController@summary"]);
        $router->get("/mentor-evaluation-reports/download-xls-summary", ["uses" => "MentorEvaluationReportController@downloadXlsSummary"]);
        
        $router->get("/evaluation-report-transcript", ["uses" => "ParticipantEvaluationReportTranscriptController@transcript"]);
        $router->get("/download-evaluation-report-transcript-xls", ["uses" => "ParticipantEvaluationReportTranscriptController@downloadXlsTranscript"]);
        
        $router->get("/worksheets", ["uses" => "WorksheetController@viewAll"]);
        $router->get("/worksheets/{worksheetId}", ["uses" => "WorksheetController@viewDetail"]);
        
        $router->get("/mentorings", ["uses" => "MentoringController@viewAll"]);
        $router->get("/mentoring-requests/{id}", ["uses" => "MentoringController@viewMentoringRequestDetail"]);
        $router->get("/negotiated-mentorings/{id}", ["uses" => "MentoringController@viewNegotiatedMentoringDetail"]);
        $router->get("/booked-mentoring-slots/{id}", ["uses" => "MentoringController@viewBookedMentoringSlotDetail"]);
        $router->get("/mentoring-slots/{id}", ["uses" => "MentoringController@viewMentoringSlotDetail"]);
        $router->get("/declared-mentorings/{id}", ["uses" => "MentoringController@viewDeclaredMentoringDetail"]);
        
        $router->get("/program-participants/{participantId}/valid-activity-invitations", ["uses" => "ActivityController@viewAllValidInvitationsToParticipant"]);
        $router->get("/participant-activity-invitations/{id}", ["uses" => "ActivityController@viewParticipantInvitationDetail"]);
        
        $router->get("/team-participants/{id}", ["uses" => "ParticipantController@viewTeamParticipantDetail"]);
        $router->get("/client-participants/{id}", ["uses" => "ParticipantController@viewClientParticipantDetail"]);
        $router->get("/user-participants/{id}", ["uses" => "ParticipantController@viewUserParticipantDetail"]);
        
        $router->get("/participant-profiles/{id}", ["uses" => "ParticipantProfileController@view"]);
        $router->get("/participants/{participantId}/profiles", ["uses" => "ParticipantProfileController@viewAllProfileOfParticularParticipant"]);
        
        // task route in coordinatorBC
        $router->post("/tasks", ["uses" => "TaskController@submitTask"]);
        $router->patch("/tasks/{id}", ["uses" => "TaskController@updateTask"]);
        $router->patch("/tasks/{id}/approve-report", ["uses" => "TaskController@approveTaskReport"]);
        $router->patch("/tasks/{id}/ask-for-report-revision", ["uses" => "TaskController@askForTaskReportRevision"]);
        $router->delete("/tasks/{id}", ["uses" => "TaskController@cancelTask"]);
        $router->get("/consultant-tasks/{id}", ["uses" => "TaskController@viewConsultantTaskDetail"]);
        $router->get("/coordinator-tasks/{id}", ["uses" => "TaskController@viewCoordinatorTaskDetail"]);
        
        // notes routes for coordinator
        $router->post("/notes", ["uses" => "NoteController@submit"]);
        $router->patch("/notes/{id}/update", ["uses" => "NoteController@update"]);
        $router->patch("/notes/{id}/hide-from-participant", ["uses" => "NoteController@hideFromParticipant"]);
        $router->patch("/notes/{id}/show-to-participant", ["uses" => "NoteController@showToParticipant"]);
        $router->delete("/notes/{id}", ["uses" => "NoteController@remove"]);
        $router->get("/consultant-notes/{id}", ["uses" => "NoteController@viewConsultantNoteDetail"]);
        $router->get("/coordinator-notes/{id}", ["uses" => "NoteController@viewCoordinatorNoteDetail"]);
        $router->get("/participant-notes/{id}", ["uses" => "NoteController@viewParticipantNoteDetail"]);
        
        // schedule for program dashboard
        $router->get("/schedules", ["uses" => "ScheduleInProgramController@viewAll"]);
        
        // schedule for program dashboard
        $router->get("/program-dashboard", ["uses" => "ProgramDashboardController@view"]);
        
    });
    
    $consultantAggregate = [
        'prefix' => '/consultants/{consultantId}',
        'namespace' => 'ProgramConsultation',
    ];
    $router->group($consultantAggregate, function () use ($router) {
        $router->get("/worksheets", ["uses" => "WorksheetController@viewAll"]);
        $router->get("/worksheets/{worksheetId}", ["uses" => "WorksheetController@viewDetail"]);
        
        $router->get("/mentorings", ["uses" => "MentoringController@viewAll"]);
        $router->get("/mentoring-requests/{id}", ["uses" => "MentoringController@viewMentoringRequestDetail"]);
        $router->get("/negotiated-mentorings/{id}", ["uses" => "MentoringController@viewNegotiatedMentoringDetail"]);
        $router->get("/booked-mentoring-slots/{id}", ["uses" => "MentoringController@viewBookedMentoringSlotDetail"]);
        $router->get("/declared-mentorings/{id}", ["uses" => "MentoringController@viewDeclaredMentoringDetail"]);
        
        $router->get("/program-participants/{participantId}/valid-activity-invitations", ["uses" => "ActivityController@viewAllValidInvitationsToParticipant"]);
        $router->get("/participant-activity-invitations/{id}", ["uses" => "ActivityController@viewParticipantInvitationDetail"]);
        
        $router->get("/team-participants/{id}", ["uses" => "ProgramParticipantController@viewTeamParticipantDetail"]);
        $router->get("/client-participants/{id}", ["uses" => "ProgramParticipantController@viewClientParticipantDetail"]);
        $router->get("/user-participants/{id}", ["uses" => "ProgramParticipantController@viewUserParticipantDetail"]);
        
        $router->get("/participant-profiles/{id}", ["uses" => "ParticipantProfileController@view"]);
        $router->get("/participants/{participantId}/profiles", ["uses" => "ParticipantProfileController@viewAllProfileOfParticularParticipant"]);
        $router->post("/tasks", ["uses" => "TaskController@submitTask"]);
        $router->patch("/tasks/{id}", ["uses" => "TaskController@updateTask"]);
        $router->patch("/tasks/{id}/approve-report", ["uses" => "TaskController@approveTaskReport"]);
        $router->patch("/tasks/{id}/ask-for-report-revision", ["uses" => "TaskController@askForTaskReportRevision"]);
        $router->delete("/tasks/{id}", ["uses" => "TaskController@cancelTask"]);
        $router->get("/consultant-tasks/{id}", ["uses" => "TaskController@viewConsultantTaskDetail"]);
        $router->get("/coordinator-tasks/{id}", ["uses" => "TaskController@viewCoordinatorTaskDetail"]);
        
        // notes routes for consultant
        $router->post("/notes", ["uses" => "NoteController@submit"]);
        $router->patch("/notes/{id}/update", ["uses" => "NoteController@update"]);
        $router->patch("/notes/{id}/hide-from-participant", ["uses" => "NoteController@hideFromParticipant"]);
        $router->patch("/notes/{id}/show-to-participant", ["uses" => "NoteController@showToParticipant"]);
        $router->delete("/notes/{id}", ["uses" => "NoteController@remove"]);
        $router->get("/consultant-notes/{id}", ["uses" => "NoteController@viewConsultantNoteDetail"]);
        $router->get("/coordinator-notes/{id}", ["uses" => "NoteController@viewCoordinatorNoteDetail"]);
        $router->get("/participant-notes/{id}", ["uses" => "NoteController@viewParticipantNoteDetail"]);
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
