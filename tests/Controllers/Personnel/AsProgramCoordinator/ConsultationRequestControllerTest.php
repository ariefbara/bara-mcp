<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\Participant\RecordOfConsultationRequest,
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfPersonnel,
    Shared\RecordOfForm
};

class ConsultationRequestControllerTest extends AsProgramCoordinatorTestCase
{

    protected $consultationRequestUri;
    protected $consultationRequest;
    protected $consultationRequestOne;
    protected $clientParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestUri = $this->asProgramCoordinatorUri . "/consultation-requests";

        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;

        $personnel = new RecordOfPersonnel($firm, 0);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());

        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        
        $this->consultationRequest = new RecordOfConsultationRequest($consultationSetup, $participant, $consultant, 0);
        $this->consultationRequest->concluded = true;
        $this->consultationRequest->status = "rejected";
        $this->consultationRequest->startDateTime = (new DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequest->endDateTime = (new DateTime("-23 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequestOne = new RecordOfConsultationRequest($consultationSetup, $participant, $consultant, 1);
        $this->consultationRequestOne->concluded = false;
        $this->consultationRequestOne->startDateTime = (new DateTime("+24 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequestOne->endDateTime = (new DateTime("+25 hours"))->format("Y-m-d H:i:s");
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequestOne->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->consultationRequestOne->id,
            "startTime" => $this->consultationRequestOne->startDateTime,
            "endTime" => $this->consultationRequestOne->endDateTime,
            "concluded" => $this->consultationRequestOne->concluded,
            "status" => $this->consultationRequestOne->status,
            "consultationSetup" => [
                "id" => $this->consultationRequestOne->consultationSetup->id,
                "name" => $this->consultationRequestOne->consultationSetup->name,
                "duration" => $this->consultationRequestOne->consultationSetup->sessionDuration,
            ],
            "consultant" => [
                "id" => $this->consultationRequestOne->consultant->id,
                "personnel" => [
                    "id" => $this->consultationRequestOne->consultant->personnel->id,
                    "name" => $this->consultationRequestOne->consultant->personnel->getFullName(),
                ],
            ],
            "participant" => [
                "id" => $this->consultationRequestOne->participant->id,
                "enrolledTime" => $this->consultationRequestOne->participant->enrolledTime,
                "active" => $this->consultationRequestOne->participant->active,
                "note" => $this->consultationRequestOne->participant->note,
                "client" => [
                    "id" => $this->clientParticipant->client->id,
                    "name" => $this->clientParticipant->client->getFullName(),
                ],
                "team" => null,
                "user" => null,
            ],
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequestOne->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveCoordinator_403()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequestOne->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultationRequest->id,
                    "startTime" => $this->consultationRequest->startDateTime,
                    "endTime" => $this->consultationRequest->endDateTime,
                    "concluded" => $this->consultationRequest->concluded,
                    "status" => $this->consultationRequest->status,
                    "consultationSetup" => [
                        "id" => $this->consultationRequest->consultationSetup->id,
                        "name" => $this->consultationRequest->consultationSetup->name,
                        "duration" => $this->consultationRequest->consultationSetup->sessionDuration,
                    ],
                    "consultant" => [
                        "id" => $this->consultationRequest->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationRequest->consultant->personnel->id,
                            "name" => $this->consultationRequest->consultant->personnel->getFullName(),
                        ],
                    ],
                    "participant" => [
                        "id" => $this->consultationRequest->participant->id,
                        "enrolledTime" => $this->consultationRequest->participant->enrolledTime,
                        "active" => $this->consultationRequest->participant->active,
                        "note" => $this->consultationRequest->participant->note,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "team" => null,
                        "user" => null,
                    ],
                ],
                [
                    "id" => $this->consultationRequestOne->id,
                    "startTime" => $this->consultationRequestOne->startDateTime,
                    "endTime" => $this->consultationRequestOne->endDateTime,
                    "concluded" => $this->consultationRequestOne->concluded,
                    "status" => $this->consultationRequestOne->status,
                    "consultationSetup" => [
                        "id" => $this->consultationRequestOne->consultationSetup->id,
                        "name" => $this->consultationRequestOne->consultationSetup->name,
                        "duration" => $this->consultationRequestOne->consultationSetup->sessionDuration,
                    ],
                    "consultant" => [
                        "id" => $this->consultationRequestOne->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationRequestOne->consultant->personnel->id,
                            "name" => $this->consultationRequestOne->consultant->personnel->getFullName(),
                        ],
                    ],
                    "participant" => [
                        "id" => $this->consultationRequestOne->participant->id,
                        "enrolledTime" => $this->consultationRequestOne->participant->enrolledTime,
                        "active" => $this->consultationRequestOne->participant->active,
                        "note" => $this->consultationRequestOne->participant->note,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "team" => null,
                        "user" => null,
                    ],
                ],
            ],
        ];
        
        $this->get($this->consultationRequestUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->get($this->consultationRequestUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_showAll_minStarTimeFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $listResponse = [
            "id" => $this->consultationRequestOne->id,
        ];
        
        $minStartTime = (new \DateTime())->format("Y-m-d");
        $uri = $this->consultationRequestUri . "?minStartTime=$minStartTime";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_maxEndTimeFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $listResponse = [
            "id" => $this->consultationRequest->id,
        ];
        
        $maxEndTime = (new \DateTime())->format("Y-m-d");
        $uri = $this->consultationRequestUri . "?maxEndTime=$maxEndTime";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_concludedStatusFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $listResponse = [
            "id" => $this->consultationRequest->id,
        ];
        
        $uri = $this->consultationRequestUri . "?concludedStatus=true";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_statusFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $listResponse = [
            "id" => $this->consultationRequest->id,
        ];
        
        $uri = $this->consultationRequestUri . "?status[]=rejected";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
    }

}
