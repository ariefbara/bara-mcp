<?php

namespace Tests\Controllers\Client\TeamMembership\ProgramParticipation;

use DateTime;
use Tests\Controllers\ {
    Client\TeamMembership\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest,
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfConsultationSetup,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Shared\RecordOfForm
};

class ConsultationRequestControllerTest extends ProgramParticipationTestCase
{

    protected $consultationRequestUri;
    protected $consultationRequest, $consultationRequestOne;
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
        $this->connection->table('Notification')->truncate();
        $this->connection->table('PersonnelNotification')->truncate();
        
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
        
        $personnel = new RecordOfPersonnel($firm, 0, "purnama.adi@gmail.com", 'password123');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        
        $this->consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table('Consultant')->insert($this->consultant->toArrayForDbEntry());

        $this->consultationRequest = new RecordOfConsultationRequest($this->consultationSetup, $this->programParticipation->participant, $this->consultant, 0);
        $this->consultationRequest->status = "offered";
        $this->consultationRequestOne = new RecordOfConsultationRequest($this->consultationSetup, $this->programParticipation->participant, $this->consultant, 1);
        $this->consultationRequestOne->startDateTime = (new DateTime("+72 hours"))->format('Y-m-d H:i:s');
        $this->consultationRequestOne->endDateTime = (new DateTime("+73 hours"))->format('Y-m-d H:i:s');
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestOne->toArrayForDbEntry());
        
        $this->consultationSession = new RecordOfConsultationSession($this->consultationSetup, $this->programParticipation->participant, $this->consultant, 0);
        $this->connection->table('ConsultationSession')->insert($this->consultationSession->toArrayForDbEntry());
        
        $this->proposeInput = [
            "consultationSetupId" => $this->consultationSetup->id,
            "consultantId" => $this->consultant->id,
            "startTime" => (new DateTime('+36 hours'))->format('Y-m-d H:i:s'),
        ];
        $this->changeTimeInput = [
            "startTime" => (new DateTime('+36 hours'))->format('Y-m-d H:i:s'),
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
        $this->connection->table('Notification')->truncate();
        $this->connection->table('PersonnelNotification')->truncate();
    }
    
    public function test_propose()
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
            "concluded" => false,
            "status" => 'proposed',
        ];
        
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->teamMembership->client->token)
            ->seeStatusCode(201)
            ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "ConsultationSetup_id" => $this->consultationSetup->id,
            "Participant_id" => $this->programParticipation->id,
            "Consultant_id" => $this->consultant->id,
            "startDateTime" => $this->proposeInput['startTime'],
            "concluded" => false,
            "status" => 'proposed',
        ];
        $this->seeInDatabase('ConsultationRequest', $consultationRequestEntry);
    }
    public function test_submit_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
    public function test_submit_conflictWithExistingConsultationRequest_409()
    {
        $this->proposeInput["startTime"] = $this->consultationRequestOne->startDateTime;
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->teamMembership->client->token)
            ->seeStatusCode(409);
    }
    public function test_submit_conflictWithNonProposedRequest_201()
    {
        $this->proposeInput["startTime"] = $this->consultationRequest->startDateTime;
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->teamMembership->client->token)
            ->seeStatusCode(201);
    }
    public function test_submit_conflictWithExistingConsultationSession_409()
    {
        $this->proposeInput["startTime"] = $this->consultationSession->startDateTime;
        $this->post($this->consultationRequestUri, $this->proposeInput, $this->teamMembership->client->token)
            ->seeStatusCode(409);
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
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
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
    public function test_changeTime_conflictWithExistingRequest_409()
    {
        $this->changeTimeInput["startTime"] = $this->consultationRequestOne->startDateTime;
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(409);
    }
    public function test_changeTime_conflictWithSelf_200()
    {
        $this->changeTimeInput["startTime"] = $this->consultationRequestOne->startDateTime;
        $uri = $this->consultationRequestUri . "/{$this->consultationRequestOne->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(200);
    }
    public function test_changeTime_conflictedExistingRequestNotProposed_200()
    {
        $this->changeTimeInput["startTime"] = $this->consultationRequest->startDateTime;
        $uri = $this->consultationRequestUri . "/{$this->consultationRequestOne->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(200);
        
    }
    public function test_changeTime_conflictWithExistingSession_409()
    {
        $this->changeTimeInput["startTime"] = $this->consultationSession->startDateTime;
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(409);
    }
    public function test_changeTime_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/change-time";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_accept()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "concluded" => true,
            "status" => 'scheduled',
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
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
    public function test_accept_conflictWithOtherProposedRequest_409()
    {
        $this->connection->table("ConsultationRequest")->truncate();
        $this->consultationRequest->startDateTime = $this->consultationRequestOne->startDateTime;
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequestOne->toArrayForDbEntry());
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(409);
    }
    public function test_accept_conflictWithNonProposedRequest_200()
    {
        $this->connection->table("ConsultationRequest")->truncate();
        $this->consultationRequest->startDateTime = $this->consultationRequestOne->startDateTime;
        $this->consultationRequestOne->status = "cancelled";
        $this->consultationRequestOne->concluded = true;
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequestOne->toArrayForDbEntry());
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(200);
        
    }
    public function test_accept_conflictWithExistingSession_409()
    {
        $this->connection->table("ConsultationRequest")->truncate();
        $this->consultationRequest->startDateTime = $this->consultationSession->startDateTime;
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(409);
    }
    public function test_accept_inactiveMember_200()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, $this->changeTimeInput, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_cancel_200()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/cancel";
        $this->patch($uri, [], $this->teamMembership->client->token)
            ->seeStatusCode(200);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "concluded" => true,
            "status" => 'cancelled',
        ];
        $this->seeInDatabase('ConsultationRequest', $consultationRequestEntry);
    }
    public function test_cancel_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/cancel";
        $this->patch($uri, [], $this->teamMembership->client->token)
            ->seeStatusCode(403);
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
            "concluded" => false,
            "status" => $this->consultationRequest->status,
        ];
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}";
        $this->get($uri, $this->teamMembership->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}";
        $this->get($uri, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->consultationRequest->id,
                    "status" => $this->consultationRequest->status,
                    "startTime" => $this->consultationRequest->startDateTime,
                    "endTime" => $this->consultationRequest->endDateTime,
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
            ],
        ];
        $this->get($this->consultationRequestUri, $this->teamMembership->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->get($this->consultationRequestUri, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }

}
