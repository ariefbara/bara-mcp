<?php

namespace Tests\Controllers\User\ProgramParticipation;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\ {
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest,
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfConsultationSetup,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Shared\RecordOfForm,
    User\ProgramParticipationTestCase
};

class ConsultationRequestControllerTest extends ProgramParticipationTestCase
{

    protected $consultationRequestUri;
    protected $consultationRequest;
    protected $consultationRequestOne;
    protected $consultationRequestTwo_rejected;
    protected $consultationSession;
    protected $consultationSetup, $consultant;
    protected $proposeInput;
    protected $changeTimeInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestUri = $this->programParticipationUri . "/{$this->programParticipation->id}/consultation-requests";
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('ConsultationRequestActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
        $this->connection->table('TeamMemberActivityLog')->truncate();
        
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
        $this->connection->table('ConsultationRequestMail')->truncate();
        $this->connection->table('ConsultationSessionMail')->truncate();
        
        $this->connection->table('Notification')->truncate();
        $this->connection->table('ConsultationRequestNotification')->truncate();
        $this->connection->table('ConsultationSessionNotification')->truncate();
        $this->connection->table('PersonnelNotificationRecipient')->truncate();
        $this->connection->table('UserNotificationRecipient')->truncate();
        
        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;

        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $this->connection->table('Form')->insert($formOne->toArrayForDbEntry());
        $this->connection->table('Form')->insert($formTwo->toArrayForDbEntry());
        
        $participantFeedbackForm = new RecordOfFeedbackForm($firm, $formOne);
        $consultantFeedbackForm = new RecordOfFeedbackForm($firm, $formTwo);
        $this->connection->table('FeedbackForm')->insert($participantFeedbackForm->toArrayForDbEntry());
        $this->connection->table('FeedbackForm')->insert($consultantFeedbackForm->toArrayForDbEntry());

        $this->consultationSetup = new RecordOfConsultationSetup($program, $participantFeedbackForm, $consultantFeedbackForm, 0);
        $this->connection->table('ConsultationSetup')->insert($this->consultationSetup->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, 0);
        $personnel->email = "adi@barapraja.com";
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table('Consultant')->insert($this->consultant->toArrayForDbEntry());
        
        $this->consultationRequest = new RecordOfConsultationRequest($this->consultationSetup, $this->programParticipation->participant, $this->consultant, 0);
        $this->consultationRequest->status = "offered";
        $this->consultationRequest->startDateTime = (new DateTime("+24 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequest->endDateTime = (new DateTime("+25 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequestOne = new RecordOfConsultationRequest($this->consultationSetup, $this->programParticipation->participant, $this->consultant, 1);
        $this->consultationRequestOne->startDateTime = (new DateTime("+72 hours"))->format('Y-m-d H:i:s');
        $this->consultationRequestOne->endDateTime = (new DateTime("+73 hours"))->format('Y-m-d H:i:s');
        $this->consultationRequestTwo_rejected = new RecordOfConsultationRequest($this->consultationSetup, $this->programParticipation->participant, $this->consultant, 2);
        $this->consultationRequestTwo_rejected->concluded = true;
        $this->consultationRequestTwo_rejected->status = "rejected";
        $this->consultationRequestTwo_rejected->startDateTime = (new DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequestTwo_rejected->endDateTime = (new DateTime("-23 hours"))->format("Y-m-d H:i:s");
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestOne->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestTwo_rejected->toArrayForDbEntry());
        
        $this->consultationSession = new RecordOfConsultationSession($this->consultationSetup, $this->programParticipation->participant, $this->consultant, 0);
        $this->connection->table('ConsultationSession')->insert($this->consultationSession->toArrayForDbEntry());
        
        $this->proposeInput = [
            "consultationSetupId" => $this->consultationSetup->id,
            "consultantId" => $this->consultant->id,
            "startTime" => (new DateTime('+36 hours'))->format('Y-m-d H:i:s'),
            "media" => "new media",
            "address" => "new address",
        ];
        $this->changeTimeInput = [
            "startTime" => (new DateTime('+36 hours'))->format('Y-m-d H:i:s'),
            "media" => "new media",
            "address" => "new address",
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('ConsultationRequestActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
        $this->connection->table('TeamMemberActivityLog')->truncate();
        
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
        $this->connection->table('ConsultationRequestMail')->truncate();
        $this->connection->table('ConsultationSessionMail')->truncate();
        
        $this->connection->table('Notification')->truncate();
        $this->connection->table('ConsultationRequestNotification')->truncate();
        $this->connection->table('ConsultationSessionNotification')->truncate();
        $this->connection->table('PersonnelNotificationRecipient')->truncate();
        $this->connection->table('UserNotificationRecipient')->truncate();
    }
    
    public function test_submit_201()
    {
        $this->connection->table('ConsultationRequest')->truncate();
        
        $response = [
            "consultationSetup" => [
                "id" => $this->consultationSetup->id,
                "name" => $this->consultationSetup->name,
            ],
            "consultant" => [
                "id" => $this->consultant->id,
                "personnel" => [
                    "id" => $this->consultant->personnel->id,
                    "name" => $this->consultant->personnel->getFullName(),
                ],
            ],
            "startTime" => $this->proposeInput['startTime'],
            "media" => $this->proposeInput['media'],
            "address" => $this->proposeInput['address'],
            "concluded" => false,
            "status" => 'proposed',
        ];
        
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->user->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "ConsultationSetup_id" => $this->consultationSetup->id,
            "Participant_id" => $this->programParticipation->id,
            "Consultant_id" => $this->consultant->id,
            "startDateTime" => $this->proposeInput['startTime'],
            "media" => $this->proposeInput['media'],
            "address" => $this->proposeInput['address'],
            "concluded" => false,
            "status" => 'proposed',
        ];
        $this->seeInDatabase('ConsultationRequest', $consultationRequestEntry);
    }
    public function test_submit_aggregateMailNotificaitonForConsultant()
    {
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->user->token)
            ->seeStatusCode(201);
        
        $mailEntry = [
            "subject" => "Permintaan Konsultasi",
            "SenderMailAddress" => $this->programParticipation->participant->program->firm->mailSenderAddress,
            "SenderName" => $this->programParticipation->participant->program->firm->mailSenderName,
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $consultantMailRecipientEntry = [
            "recipientMailAddress" => $this->consultant->personnel->email,
            "recipientName" => $this->consultant->personnel->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $consultantMailRecipientEntry);
    }
    public function test_submit_aggregateNotificationForConsultant()
    {
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->user->token)
            ->seeStatusCode(201);
        
        $personnelNotificationRecipientEntry = [
            "readStatus" => false,
            "Personnel_id" => $this->consultant->personnel->id,
        ];
        $this->seeInDatabase("PersonnelNotificationRecipient", $personnelNotificationRecipientEntry);
    }
    public function test_submit_conflictWithExistingConsultationRequest_409()
    {
        $this->proposeInput["startTime"] = $this->consultationRequestOne->startDateTime;
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->user->token)
            ->seeStatusCode(409);
    }
    public function test_submit_conflictWithNonProposedRequest_201()
    {
        $this->proposeInput["startTime"] = $this->consultationRequest->startDateTime;
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->user->token)
            ->seeStatusCode(201);
    }
    public function test_submit_conflictWithExistingConsultationSession_403()
    {
        $this->proposeInput["startTime"] = $this->consultationSession->startDateTime;
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->user->token)
            ->seeStatusCode(403);
    }
    public function test_submit_logActivity()
    {
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->user->token)
            ->seeStatusCode(201);
        $activityLogEntry = [
            "message" => "participant submitted consultation request",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
    }
    
    public function test_changeTime_200()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "startTime" => $this->changeTimeInput['startTime'],
            "concluded" => false,
            "status" => 'proposed',
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "startDateTime" => $this->changeTimeInput['startTime'],
            "concluded" => false,
            "status" => 'proposed',
        ];
        $this->seeInDatabase('ConsultationRequest', $consultationRequestEntry);
    }
    public function test_changeTime_aggregateMailNotificaitonForConsultant()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
        
        $mailEntry = [
            "subject" => "Permintaan Konsultasi",
            "SenderMailAddress" => $this->programParticipation->participant->program->firm->mailSenderAddress,
            "SenderName" => $this->programParticipation->participant->program->firm->mailSenderName,
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $consultantMailRecipientEntry = [
            "recipientMailAddress" => $this->consultant->personnel->email,
            "recipientName" => $this->consultant->personnel->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $consultantMailRecipientEntry);
    }
    public function test_changeTime_aggregateNotificationForConsultant()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
        
        $personnelNotificationRecipientEntry = [
            "readStatus" => false,
            "Personnel_id" => $this->consultant->personnel->id,
        ];
        $this->seeInDatabase("PersonnelNotificationRecipient", $personnelNotificationRecipientEntry);
    }
    public function test_changeTime_conflictWithExistingRequest_409()
    {
        $this->changeTimeInput["startTime"] = $this->consultationRequestOne->startDateTime;
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(409);
    }
    public function test_changeTime_conflictWithSelf_200()
    {
        $this->changeTimeInput["startTime"] = $this->consultationRequestOne->startDateTime;
        $uri = $this->consultationRequestUri . "/{$this->consultationRequestOne->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
    }
    public function test_changeTime_conflictedExistingRequestNotProposed_200()
    {
        $this->changeTimeInput["startTime"] = $this->consultationRequest->startDateTime;
        $uri = $this->consultationRequestUri . "/{$this->consultationRequestOne->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
        
    }
    public function test_changeTime_conflictWithExistingSession_403()
    {
        $this->changeTimeInput["startTime"] = $this->consultationSession->startDateTime;
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(403);
    }
    public function test_changeTime_logActivity()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "participant changed consultation request time",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultationRequestActivityLogEntry = [
            "ConsultationRequest_id" => $this->consultationRequest->id,
        ];
        $this->seeInDatabase("ConsultationRequestActivityLog", $consultationRequestActivityLogEntry);
    }
   
    public function test_cancel_200()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/cancel";
        $this->patch($uri, [], $this->user->token)
            ->seeStatusCode(200);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "concluded" => true,
            "status" => 'cancelled',
        ];
        $this->seeInDatabase('ConsultationRequest', $consultationRequestEntry);
    }
    public function test_cancel_aggregateMailNotificaitonForConsultant()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/cancel";
        $this->patch($uri, [], $this->user->token)
            ->seeStatusCode(200);
        
        $mailEntry = [
            "subject" => "Permintaan Konsultasi",
            "SenderMailAddress" => $this->programParticipation->participant->program->firm->mailSenderAddress,
            "SenderName" => $this->programParticipation->participant->program->firm->mailSenderName,
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $consultantMailRecipientEntry = [
            "recipientMailAddress" => $this->consultant->personnel->email,
            "recipientName" => $this->consultant->personnel->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $consultantMailRecipientEntry);
    }
    public function test_cancel_aggregateNotificationForOtherTeamMemberAndConsultant()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/cancel";
        $this->patch($uri, [], $this->user->token)
            ->seeStatusCode(200);
        
        $personnelNotificationRecipientEntry = [
            "readStatus" => false,
            "Personnel_id" => $this->consultant->personnel->id,
        ];
        $this->seeInDatabase("PersonnelNotificationRecipient", $personnelNotificationRecipientEntry);
    }
    public function test_cancel_logActivity()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/cancel";
        $this->patch($uri, [], $this->user->token)
            ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "participant cancelled consultation request",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultationRequestActivityLogEntry = [
            "ConsultationRequest_id" => $this->consultationRequest->id,
        ];
        $this->seeInDatabase("ConsultationRequestActivityLog", $consultationRequestActivityLogEntry);
    }

    public function test_accept()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "concluded" => true,
            "status" => 'scheduled',
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "concluded" => true,
            "status" => 'scheduled',
        ];
        $this->seeInDatabase('ConsultationRequest', $consultationRequestEntry);
        
        $consultationSession = [
            "ConsultationSetup_id" => $this->consultationRequest->consultationSetup->id,
            "Participant_id" => $this->consultationRequest->participant->id,
            "Consultant_id" => $this->consultationRequest->consultant->id,
            "startDateTime" => $this->consultationRequest->startDateTime,
            "endDateTime" => $this->consultationRequest->endDateTime,
        ];
        $this->seeInDatabase('ConsultationSession', $consultationSession);
    }
    public function test_accept_aggregateMailNotificaitonForSelfAndConsultant()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
        
        $mailEntry = [
            "subject" => "Jadwal Konsultasi",
            "SenderMailAddress" => $this->programParticipation->participant->program->firm->mailSenderAddress,
            "SenderName" => $this->programParticipation->participant->program->firm->mailSenderName,
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $selfMailRecipientEntry = [
            "recipientMailAddress" => $this->user->email,
            "recipientName" => $this->user->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $selfMailRecipientEntry);
        
        $consultantMailRecipientEntry = [
            "recipientMailAddress" => $this->consultant->personnel->email,
            "recipientName" => $this->consultant->personnel->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $consultantMailRecipientEntry);
        
    }
    public function test_accept_aggregateNotificationForConsultant()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
        
        $personnelNotificationRecipientEntry = [
            "readStatus" => false,
            "Personnel_id" => $this->consultant->personnel->id,
        ];
        $this->seeInDatabase("PersonnelNotificationRecipient", $personnelNotificationRecipientEntry);
    }
    public function test_accept_conflictWithOtherProposedRequest_409()
    {
        $this->connection->table("ConsultationRequest")->truncate();
        $this->consultationRequest->startDateTime = $this->consultationRequestOne->startDateTime;
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequestOne->toArrayForDbEntry());
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(409);
    }
    public function test_accept_conflictWithNonProposedRequest_200()
    {
        $this->connection->table("ConsultationRequest")->truncate();
        $this->consultationRequest->startDateTime = $this->consultationRequestOne->startDateTime;
        $this->consultationRequest->endDateTime = $this->consultationRequestOne->endDateTime;
        $this->consultationRequestOne->status = "cancelled";
        $this->consultationRequestOne->concluded = true;
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequestOne->toArrayForDbEntry());
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
    }
    public function test_accept_conflictWithExistingSession_409()
    {
        $this->connection->table("ConsultationRequest")->truncate();
        $this->consultationRequest->startDateTime = $this->consultationSession->startDateTime;
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(409);
    }
    public function test_accept_logConsultationSessionActivity()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->user->token)
            ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "participant scheduled consultation session",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
    }

    public function test_show()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "consultationSetup" => [
                "id" => $this->consultationRequest->consultationSetup->id,
                "name" => $this->consultationRequest->consultationSetup->name,
            ],
            "consultant" => [
                "id" => $this->consultationRequest->consultant->id,
                "personnel" => [
                    "id" => $this->consultationRequest->consultant->personnel->id,
                    "name" => $this->consultationRequest->consultant->personnel->getFullName(),
                ],
            ],
            "startTime" => $this->consultationRequest->startDateTime,
            "endTime" => $this->consultationRequest->endDateTime,
            "media" => $this->consultationRequest->media,
            "address" => $this->consultationRequest->address,
            "concluded" => false,
            "status" => $this->consultationRequest->status,
        ];
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}";
        $this->get($uri, $this->user->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 3, 
            "list" => [
                [
                    "id" => $this->consultationRequest->id,
                    "status" => $this->consultationRequest->status,
                    "startTime" => $this->consultationRequest->startDateTime,
                    "endTime" => $this->consultationRequest->endDateTime,
                    "media" => $this->consultationRequest->media,
                    "address" => $this->consultationRequest->address,
                    "concluded" => $this->consultationRequest->concluded,
                    "consultationSetup" => [
                        "id" => $this->consultationRequest->consultationSetup->id,
                        "name" => $this->consultationRequest->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationRequest->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationRequest->consultant->personnel->id,
                            "name" => $this->consultationRequest->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
                [
                    "id" => $this->consultationRequestOne->id,
                    "status" => $this->consultationRequestOne->status,
                    "startTime" => $this->consultationRequestOne->startDateTime,
                    "endTime" => $this->consultationRequestOne->endDateTime,
                    "media" => $this->consultationRequestOne->media,
                    "address" => $this->consultationRequestOne->address,
                    "concluded" => $this->consultationRequestOne->concluded,
                    "consultationSetup" => [
                        "id" => $this->consultationRequestOne->consultationSetup->id,
                        "name" => $this->consultationRequestOne->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationRequestOne->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationRequestOne->consultant->personnel->id,
                            "name" => $this->consultationRequestOne->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
                [
                    "id" => $this->consultationRequestTwo_rejected->id,
                    "status" => $this->consultationRequestTwo_rejected->status,
                    "startTime" => $this->consultationRequestTwo_rejected->startDateTime,
                    "endTime" => $this->consultationRequestTwo_rejected->endDateTime,
                    "media" => $this->consultationRequestTwo_rejected->media,
                    "address" => $this->consultationRequestTwo_rejected->address,
                    "concluded" => $this->consultationRequestTwo_rejected->concluded,
                    "consultationSetup" => [
                        "id" => $this->consultationRequestTwo_rejected->consultationSetup->id,
                        "name" => $this->consultationRequestTwo_rejected->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationRequestTwo_rejected->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationRequestTwo_rejected->consultant->personnel->id,
                            "name" => $this->consultationRequestTwo_rejected->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
            ],
        ];
        $this->get($this->consultationRequestUri, $this->user->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }

    public function test_showAll_minStartTimeFilterSet_200()
    {
        $totalResponse = [
            "total" => 2,
        ];
        $objectReponse = [
            "id" => $this->consultationRequest->id,
        ];
        $objectOneReponse = [
            "id" => $this->consultationRequestOne->id,
        ];
        $uri = $this->consultationRequestUri . "?minStartTime=" . (new DateTimeImmutable())->format("Y-m-d H:i:s");
        $this->get($uri, $this->user->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeJsonContains($objectOneReponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_maxEndTimeFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->consultationRequestTwo_rejected->id,
        ];
        
        $uri = $this->consultationRequestUri . "?maxEndTime=" . (new DateTimeImmutable())->format("Y-m-d H:i:s");
        $this->get($uri, $this->user->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_concludedStatusFilterSet_200()
    {
        $totalResponse = [
            "total" => 2,
        ];
        $objectReponse = [
            "id" => $this->consultationRequest->id,
        ];
        $objectOneReponse = [
            "id" => $this->consultationRequestOne->id,
        ];
        $uri = $this->consultationRequestUri . "?concludedStatus=false";
        $this->get($uri, $this->user->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeJsonContains($objectOneReponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_statusFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->consultationRequestTwo_rejected->id,
        ];
        
        $uri = $this->consultationRequestUri . "?status[]=rejected";
        $this->get($uri, $this->user->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }

}
