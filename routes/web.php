<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});



$router->group(['prefix' => '/api/founder-account'], function () use($router) {
    $controller = 'FounderAccountController';
    $router->patch("/generate-activation-code", ["uses" => "$controller@generateActivationCode"]);
    $router->patch("/activate", ["uses" => "$controller@activate"]);
    $router->patch("/generate-reset-password-code", ["uses" => "$controller@generateResetPasswordCode"]);
    $router->patch("/reset-password", ["uses" => "$controller@resetPassword"]);
});

$personnelAggregate = [
    "prefix" => 'api/personnel',
    "namespace" => 'Personnel',
    "middleware" => 'personnelJwtAuth',
];
$router->group($personnelAggregate, function () use ($router) {
    $router->post("/file-upload", ["uses" => "FileUploadController@upload"]);
    
    $router->group(['prefix' => '/personnel-profile'], function () use ($router) {
        $controller = "PersonnelProfileController";
        $router->patch("/update", ["uses" => "$controller@update"]);
        $router->patch("/change-password", ["uses" => "$controller@changePassword"]);
        $router->get("", ["uses" => "$controller@show"]);
    });
    
    $router->group(['prefix' => '/personnel-notifications'], function () use ($router) {
        $controller = "PersonnelNotificationController";
        $router->patch("/{personnelNotificationId}/read", ["uses" => "$controller@read"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });

    $router->group(['prefix' => '/mentorships'], function () use ($router) {
        $controller = "MentorshipController";
        $router->get("/{mentorId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/coordinatorships'], function () use ($router) {
        $controller = "CoordinatorshipController";
        $router->get("/{coordinatorId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $mentorshipAggregate = [
        "prefix" => '/mentorships/{mentorId}',
        "namespace" => 'Mentorship',
    ];
    $router->group($mentorshipAggregate, function () use ($router) {
        $router->group(['prefix' => '/negotiate-schedules'], function () use ($router) {
            $controller = "NegotiateScheduleController";
            $router->patch("/{negotiateScheduleId}/accept", ["uses" => "$controller@accept"]);
            $router->patch("/{negotiateScheduleId}/reject", ["uses" => "$controller@reject"]);
            $router->patch("/{negotiateScheduleId}/offer", ["uses" => "$controller@offer"]);
            $router->get("/{negotiateScheduleId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/schedules'], function () use ($router) {
            $controller = "ScheduleController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->put("/{scheduleId}/mentor-mentoring-report", ["uses" => "$controller@setMentorMentoringReport"]);
            $router->get("/{scheduleId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/comments'], function () use ($router) {
            $controller = "MentorCommentController";
            $router->post("", ["uses" => "$controller@submitNew"]);
            $router->post("/{commentId}", ["uses" => "$controller@reply"]);
            $router->delete("/{commentId}", ["uses" => "$controller@remove"]);
        });
        
    });
    
    $asMentorAggregate = [
        "prefix" => '/as-mentor/{programId}',
        "namespace" => 'AsMentor',
    ];
    $router->group($asMentorAggregate, function () use ($router) {
        $router->group(['prefix' => '/mentorings'], function () use ($router) {
            $controller = "MentoringController";
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/missions'], function () use ($router) {
            $controller = "MissionController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{missionId}", ["uses" => "$controller@show"]);
        });
        $router->group(['prefix' => '/participants'], function () use ($router) {
            $controller = "ParticipantController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/include-journals", ["uses" => "$controller@showAllIncludeJournals"]);
            $router->get("/{participantId}", ["uses" => "$controller@show"]);
        });
        $participantAggregate = [
            "prefix" => '/participants/{participantId}',
            "namespace" => 'Participant',
        ];
        $router->group($participantAggregate, function () use ($router) {
            $router->group(['prefix' => '/journals'], function () use ($router) {
                $controller = "JournalController";
                $router->get("/{journalId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $journalAggregate = [
                "prefix" => '/journals/{journalId}',
                "namespace" => 'Journal',
            ];
            $router->group($journalAggregate, function () use ($router) {
                $router->group(['prefix' => '/comments'], function () use ($router) {
                    $controller = "CommentController";
                    $router->get("/{commentId}", ["uses" => "$controller@show"]);
                    $router->get("", ["uses" => "$controller@showAll"]);
                });
            });
        });
        
//        $missionAggregate = [
//            "prefix" => '/missions/{missionId}',
//            "namespace" => 'Mission',
//        ];
//        $router->group($missionAggregate, function () use ($router) {
//            $journalAggregate = [
//                "prefix" => '/journals/{journalId}',
//                "namespace" => 'Journal',
//            ];
//            $router->group($journalAggregate, function () use ($router) {
//                $router->group(['prefix' => '/comments'], function () use ($router) {
//                    $controller = "CommentController";
//                    $router->get("/{commentId}", ["uses" => "$controller@show"]);
//                    $router->get("", ["uses" => "$controller@showAll"]);
//                });
//            });
//        });
    });
    
    $asAdminAggregate = [
        "prefix" => '/as-admin',
        "namespace" => 'AsAdmin',
    ];
    $router->group($asAdminAggregate, function () use ($router) {
        $router->group(['prefix' => '/profile-forms'], function () use ($router) {
            $controller = "ProfileFormController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{profileFormId}", ["uses" => "$controller@update"]);
            $router->delete("/{profileFormId}", ["uses" => "$controller@remove"]);
            $router->get("/{profileFormId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/worksheet-forms'], function () use ($router) {
            $controller = "WorksheetFormController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{worksheetFormId}", ["uses" => "$controller@update"]);
            $router->delete("/{worksheetFormId}", ["uses" => "$controller@remove"]);
            $router->get("/{worksheetFormId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/mentoring-feedback-forms'], function () use ($router) {
            $controller = "MentoringFeedbackFormController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{mentoringFeedbackFormId}", ["uses" => "$controller@update"]);
            $router->delete("/{mentoringFeedbackFormId}", ["uses" => "$controller@remove"]);
            $router->get("/{mentoringFeedbackFormId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/team-profile-forms'], function () use ($router) {
            $controller = "TeamProfileFormController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{teamProfileFormId}", ["uses" => "$controller@update"]);
            $router->delete("/{teamProfileFormId}", ["uses" => "$controller@remove"]);
            $router->get("/{teamProfileFormId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/founders'], function () use ($router) {
            $controller = "FounderController";
            $router->get("/{founderId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/programs'], function () use ($router) {
            $controller = "ProgramController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{programId}/update", ["uses" => "$controller@update"]);
            $router->patch("/{programId}/publish", ["uses" => "$controller@publish"]);
            $router->delete("/{programId}", ["uses" => "$controller@remove"]);
            $router->get("/{programId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $programAggregate = [
            "prefix" => '/programs/{programId}',
            "namespace" => 'Program',
        ];
        $router->group($programAggregate, function () use ($router) {
            $router->group(['prefix' => '/coordinators'], function () use ($router) {
                $controller = "CoordinatorController";
                $router->post("", ["uses" => "$controller@assign"]);
                $router->delete("/{coordinatorId}", ["uses" => "$controller@remove"]);
                $router->get("/{coordinatorId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $router->group(['prefix' => '/mentors'], function () use ($router) {
                $controller = "MentorController";
                $router->post("", ["uses" => "$controller@assign"]);
                $router->delete("/{mentorId}", ["uses" => "$controller@remove"]);
                $router->get("/{mentorId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $router->group(['prefix' => '/missions'], function () use ($router) {
                $controller = "MissionController";
                $router->post("", ["uses" => "$controller@addRoot"]);
                $router->post("/{missionId}", ["uses" => "$controller@addBranch"]);
                $router->patch("/{missionId}", ["uses" => "$controller@update"]);
                $router->patch("/{missionId}/publish", ["uses" => "$controller@publish"]);
                $router->get("/{missionId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $missionAggregate = [
                "prefix" => '/missions/{missionId}',
                "namespace" => 'Mission',
            ];
            $router->group($missionAggregate, function () use ($router) {
                $router->group(['prefix' => '/learning-materials'], function () use ($router) {
                    $controller = "LearningMaterialController";
                    $router->post("", ["uses" => "$controller@add"]);
                    $router->patch("/{learningMaterialId}", ["uses" => "$controller@update"]);
                    $router->delete("/{learningMaterialId}", ["uses" => "$controller@remove"]);
                    $router->get("/{learningMaterialId}", ["uses" => "$controller@show"]);
                    $router->get("", ["uses" => "$controller@showAll"]);
                });
            });
            
            $router->group(['prefix' => '/mentorings'], function () use ($router) {
                $controller = "MentoringController";
                $router->post("", ["uses" => "$controller@add"]);
                $router->delete("/{mentoringId}", ["uses" => "$controller@remove"]);
                $router->get("/{mentoringId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
        });
        
        $router->group(['prefix' => '/personnels'], function () use ($router) {
            $controller = "PersonnelController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->get("/{personnelId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
    });
    
    $asCoordinatorAggregate = [
        "prefix" => '/as-coordinator/{programId}',
        "namespace" => 'AsCoordinator',
    ];
    $router->group($asCoordinatorAggregate, function () use ($router) {
        $router->group(['prefix' => '/registrants'], function () use ($router) {
            $controller = "RegistrantController";
            $router->patch("/{registrantId}/accept", ["uses" => "$controller@accept"]);
            $router->patch("/{registrantId}/reject", ["uses" => "$controller@reject"]);
            $router->get("/{registrantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/participants'], function () use ($router) {
            $controller = "ParticipantController";
            $router->delete("/{participantId}", ["uses" => "$controller@remove"]);
            $router->get("/{participantId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/mentorings'], function () use ($router) {
            $controller = "MentoringController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{mentoringId}", ["uses" => "$controller@show"]);
        });
        $router->group(['prefix' => '/mentors'], function () use ($router) {
            $controller = "MentorController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{mentorId}", ["uses" => "$controller@show"]);
        });
        $router->group(['prefix' => '/mentoring-schedules'], function () use ($router) {
            $controller = "MentoringScheduleController";
            $router->get("", ["uses" => "$controller@showAll"]);
            $router->get("/{scheduleId}", ["uses" => "$controller@show"]);
        });
        $router->group(['prefix' => '/registration-phases'], function () use ($router) {
            $controller = "RegistrationPhaseController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{registrationPhaseId}", ["uses" => "$controller@update"]);
            $router->delete("/{registrationPhaseId}", ["uses" => "$controller@remove"]);
            $router->get("/{registrationPhaseId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        
    });

});

$founderAggregate = [
    "prefix" => 'api/founder',
    "namespace" => 'Founder',
    "middleware" => 'founderJwtAuth',
];
$router->group($founderAggregate, function () use ($router) {
    $router->patch("/update-account", ["uses" => "AccountController@update"]);
    $router->patch("/change-password", ["uses" => "AccountController@changePassword"]);
    $router->post("/file-upload", ["uses" => "FileUploadController@upload"]);
    $router->get("/programs", ["uses" => "ProgramController@showAll"]);
    $router->get("/founders", ["uses" => "FounderController@show"]);
    $router->put("/last-team-membership", ["uses" => "LastTeamMembershipController@set"]);
    $lastTeamMembershipAggregate = [
        "prefix" => 'last-team-membership',
        "namespace" => 'LastTeamMembership',
    ];
    $router->group($lastTeamMembershipAggregate, function () use ($router) {
        $router->put("/last-program-participationship", ["uses" => "LastProgramParticipationshipController@set"]);
    });
    
    $router->group(['prefix' => '/founder-notifications'], function () use ($router) {
        $controller = "FounderNotificationController";
        $router->patch("/{founderNotificationId}/read", ["uses" => "$controller@read"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/profile-forms'], function () use ($router) {
        $controller = "ProfileFormController";
        $router->get("/{profileFormId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/profiles'], function () use ($router) {
        $controller = "ProfileController";
        $router->put("/{profileFormId}", ["uses" => "$controller@set"]);
        $router->delete("/{profileFormId}", ["uses" => "$controller@remove"]);
        $router->get("/{profileFormId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/teams'], function () use ($router) {
        $controller = "TeamController";
        $router->post("", ["uses" => "$controller@create"]);
    });
    $router->group(['prefix' => '/team-member-candidateships'], function () use ($router) {
        $controller = "TeamMemberCandidateshipController";
        $router->patch("/{teamMemberCandidateshipId}/accept", ["uses" => "$controller@accept"]);
        $router->patch("/{teamMemberCandidateshipId}/reject", ["uses" => "$controller@reject"]);
        $router->get("/{teamMemberCandidateshipId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $router->group(['prefix' => '/team-memberships'], function () use ($router) {
        $controller = "TeamMembershipController";
        $router->delete("/{teamMembershipId}", ["uses" => "$controller@quit"]);
        $router->get("/{teamMembershipId}", ["uses" => "$controller@show"]);
        $router->get("", ["uses" => "$controller@showAll"]);
    });
    $teamMembershipAggregate = [
        "prefix" => '/team-memberships/{teamMembershipId}',
        "namespace" => 'TeamMembership',
    ];
    $router->group($teamMembershipAggregate, function () use ($router) {
        $router->group(['prefix' => '/comments'], function () use ($router) {
            $controller = "CommentController";
            $router->post("", ["uses" => "$controller@submitNew"]);
            $router->post("/{commentId}", ["uses" => "$controller@reply"]);
            $router->delete("/{commentId}", ["uses" => "$controller@remove"]);
        });
    });
    
    $asTeamMemberAggregate = [
        "prefix" => '/as-team-member/{teamId}',
        "namespace" => 'AsTeamMember',
    ];
    $router->group($asTeamMemberAggregate, function () use ($router) {
        $router->patch("", ["uses" => "TeamProfileController@update"]);
        $router->post("/file-upload", ["uses" => "FileUploadController@upload"]);
        $router->get("/programs", ["uses" => "ProgramController@showAll"]);

        $router->group(['prefix' => '/member-candidates'], function () use ($router) {
            $controller = "MemberCandidateController";
            $router->post("", ["uses" => "$controller@invite"]);
            $router->delete("/{memberCandidateId}", ["uses" => "$controller@cancelInvitation"]);
            $router->get("/{memberCandidateId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/members'], function () use ($router) {
            $controller = "MemberController";
            $router->delete("/{memberId}", ["uses" => "$controller@remove"]);
            $router->get("/{memberId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/worksheets'], function () use ($router) {
            $controller = "WorksheetController";
            $router->post("", ["uses" => "$controller@add"]);
            $router->patch("/{worksheetId}", ["uses" => "$controller@update"]);
            $router->delete("/{worksheetId}", ["uses" => "$controller@remove"]);
            $router->get("/{worksheetId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/team-profile-forms'], function () use ($router) {
            $controller = "TeamProfileFormController";
            $router->get("/{teamProfileFormId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/profiles'], function () use ($router) {
            $controller = "ProfileController";
            $router->put("/{teamProfileFormId}", ["uses" => "$controller@set"]);
            $router->delete("/{teamProfileFormId}", ["uses" => "$controller@remove"]);
            $router->get("/{teamProfileFormId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/program-registrations'], function () use ($router) {
            $controller = "ProgramRegistrationController";
            $router->post("", ["uses" => "$controller@apply"]);
            $router->delete("/{programRegistrationId}", ["uses" => "$controller@cancel"]);
            $router->get("/{programRegistrationId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $router->group(['prefix' => '/program-participations'], function () use ($router) {
            $controller = "ProgramParticipationController";
            $router->delete("/{programParticipationId}", ["uses" => "$controller@quit"]);
            $router->get("/{programParticipationId}", ["uses" => "$controller@show"]);
            $router->get("", ["uses" => "$controller@showAll"]);
        });
        $programParticipationAggregate = [
            "prefix" => '/program-participations/{programParticipationId}',
            "namespace" => 'ProgramParticipation',
        ];
        $router->group($programParticipationAggregate, function () use ($router) {
            $router->group(['prefix' => '/missions'], function () use ($router) {
                $controller = "MissionController";
                $router->get("/{missionId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $missionAggregate = [
                "prefix" => '/missions/{missionId}',
                "namespace" => 'Mission',
            ];
            $router->group($missionAggregate, function () use ($router) {
                $router->group(['prefix' => '/learning-materials'], function () use ($router) {
                    $controller = "LearningMaterialController";
                    $router->get("/{learningMaterialId}", ["uses" => "$controller@show"]);
                    $router->get("", ["uses" => "$controller@showAll"]);
                });
            });
            
            $router->group(['prefix' => '/mentors'], function () use ($router) {
                $controller = "MentorController";
                $router->get("/{mentorId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $router->group(['prefix' => '/mentorings'], function () use ($router) {
                $controller = "MentoringController";
                $router->get("/{mentoringId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $router->group(['prefix' => '/negotiate-mentoring-schedules'], function () use ($router) {
                $controller = "NegotiateMentoringScheduleController";
                $router->post("", ["uses" => "$controller@propose"]);
                $router->patch("/{negotiateMentoringScheduleId}/re-propose", ["uses" => "$controller@rePropose"]);
                $router->patch("/{negotiateMentoringScheduleId}/accept", ["uses" => "$controller@accept"]);
                $router->delete("/{negotiateMentoringScheduleId}", ["uses" => "$controller@cancel"]);
                $router->get("/{negotiateMentoringScheduleId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $router->group(['prefix' => '/mentoring-schedules'], function () use ($router) {
                $controller = "MentoringScheduleController";
                $router->post("", ["uses" => "$controller@add"]);
                $router->put("/{mentoringScheduleId}/participant-mentoring-report", ["uses" => "$controller@setParticipantMentoringReport"]);
                $router->get("/{mentoringScheduleId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $router->group(['prefix' => '/journals'], function () use ($router) {
                $controller = "JournalController";
                $router->post("", ["uses" => "$controller@addRoot"]);
                $router->post("/{journalId}", ["uses" => "$controller@addBranch"]);
                $router->patch("/{journalId}", ["uses" => "$controller@update"]);
//                $router->delete("/{journalId}", ["uses" => "$controller@remove"]);
                $router->get("/{journalId}", ["uses" => "$controller@show"]);
                $router->get("", ["uses" => "$controller@showAll"]);
            });
            $router->group(['prefix' => '/journals_atomic-worksheet'], function () use ($router) {
                $controller = "AtomicJournalAndWorksheetController";
                $router->post("", ["uses" => "$controller@addRoot"]);
                $router->post("/{journalId}", ["uses" => "$controller@addBranch"]);
                $router->patch("/{journalId}", ["uses" => "$controller@update"]);
            });
            $journalAggregate = [
                "prefix" => '/journals/{journalId}',
                "namespace" => 'Journal',
            ];
            $router->group($journalAggregate, function () use ($router) {
                $router->group(['prefix' => '/comments'], function () use ($router) {
                    $controller = "CommentController";
                    $router->get("/{commentId}", ["uses" => "$controller@show"]);
                    $router->get("", ["uses" => "$controller@showAll"]);
                });
            });
        });
    });
});
