<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use Tests\Controllers\RecordPreparation\{
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\Participant\ConsultationSession\RecordOfConsultantFeedback,
    Firm\Program\Participant\ConsultationSession\RecordOfParticipantFeedback,
    Firm\Program\Participant\RecordOfConsultationSession,
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfPersonnel,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class ConsultationSessionControllerTest extends AsProgramCoordinatorTestCase
{

    protected $consultationSessionUri;
    protected $consultationSession;
    protected $consultationSessionOne;
    protected $clientParticipant;
    protected $participantFeedback_session;
    protected $consultantFeedback_sessionOne;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionUri = $this->asProgramCoordinatorUri . "/consultation-sessions";

        $this->connection->table("Form")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ParticipantFeedback")->truncate();
        $this->connection->table("ConsultantFeedback")->truncate();

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

        $this->consultationSession = new RecordOfConsultationSession($consultationSetup, $participant, $consultant, 0);
        $this->consultationSession->startDateTime = (new \DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->consultationSession->endDateTime = (new \DateTime("-23 hours"))->format("Y-m-d H:i:s");
        $this->consultationSessionOne = new RecordOfConsultationSession($consultationSetup, $participant, $consultant, 1);
        $this->consultationSessionOne->startDateTime = (new \DateTime("+24 hours"))->format("Y-m-d H:i:s");
        $this->consultationSessionOne->endDateTime = (new \DateTime("+25 hours"))->format("Y-m-d H:i:s");
        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSessionOne->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($form, 0);
        $this->connection->table("FormRecord")->insert($formRecord->toArrayForDbEntry());

        $this->participantFeedback_session = new RecordOfParticipantFeedback($this->consultationSession, $formRecord);
        $this->connection->table("ParticipantFeedback")->insert($this->participantFeedback_session->toArrayForDbEntry());

        $this->consultantFeedback_sessionOne = new RecordOfConsultantFeedback($this->consultationSessionOne, $formRecord);
        $this->connection->table("ConsultantFeedback")->insert($this->consultantFeedback_sessionOne->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->connection->table("Form")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ParticipantFeedback")->truncate();
        $this->connection->table("ConsultantFeedback")->truncate();
    }

    public function test_show_200()
    {
        $response = [
            "id" => $this->consultationSessionOne->id,
            "startTime" => $this->consultationSessionOne->startDateTime,
            "endTime" => $this->consultationSessionOne->endDateTime,
            "consultationSetup" => [
                "id" => $this->consultationSessionOne->consultationSetup->id,
                "name" => $this->consultationSessionOne->consultationSetup->name,
                "duration" => $this->consultationSessionOne->consultationSetup->sessionDuration,
            ],
            "consultant" => [
                "id" => $this->consultationSessionOne->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSessionOne->consultant->personnel->id,
                    "name" => $this->consultationSessionOne->consultant->personnel->getFullName(),
                ],
            ],
            "participant" => [
                "id" => $this->consultationSessionOne->participant->id,
                "enrolledTime" => $this->consultationSessionOne->participant->enrolledTime,
                "active" => $this->consultationSessionOne->participant->active,
                "note" => $this->consultationSessionOne->participant->note,
                "client" => [
                    "id" => $this->clientParticipant->client->id,
                    "name" => $this->clientParticipant->client->getFullName(),
                ],
                "team" => null,
                "user" => null,
            ],
            "consultantReport" => [
                "submitTime" => $this->participantFeedback_session->formRecord->submitTime,
                "attachmentFieldRecords" => [],
                "stringFieldRecords" => [],
                "integerFieldRecords" => [],
                "textAreaFieldRecords" => [],
                "singleSelectFieldRecords" => [],
                "multiSelectFieldRecords" => [],
            ],
            "participantReport" => null,
        ];
        $uri = $this->consultationSessionUri . "/{$this->consultationSessionOne->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_show_inactiveCoordinator_403()
    {
        $uri = $this->consultationSessionUri . "/{$this->consultationSessionOne->id}";
        $this->get($uri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultationSession->id,
                    "startTime" => $this->consultationSession->startDateTime,
                    "endTime" => $this->consultationSession->endDateTime,
                    "consultationSetup" => [
                        "id" => $this->consultationSession->consultationSetup->id,
                        "name" => $this->consultationSession->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationSession->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationSession->consultant->personnel->id,
                            "name" => $this->consultationSession->consultant->personnel->getFullName(),
                        ],
                    ],
                    "participant" => [
                        "id" => $this->consultationSession->participant->id,
                        "active" => $this->consultationSession->participant->active,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "team" => null,
                        "user" => null,
                    ],
                ],
                [
                    "id" => $this->consultationSessionOne->id,
                    "startTime" => $this->consultationSessionOne->startDateTime,
                    "endTime" => $this->consultationSessionOne->endDateTime,
                    "consultationSetup" => [
                        "id" => $this->consultationSessionOne->consultationSetup->id,
                        "name" => $this->consultationSessionOne->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationSessionOne->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationSessionOne->consultant->personnel->id,
                            "name" => $this->consultationSessionOne->consultant->personnel->getFullName(),
                        ],
                    ],
                    "participant" => [
                        "id" => $this->consultationSessionOne->participant->id,
                        "active" => $this->consultationSessionOne->participant->active,
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
        
        $this->get($this->consultationSessionUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->get($this->consultationSessionUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_showAll_minStarTimeFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $listResponse = [
            "id" => $this->consultationSessionOne->id,
        ];
        
        $minStartTime = (new \DateTime())->format("Y-m-d");
        $uri = $this->consultationSessionUri . "?minStartTime=$minStartTime";
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
            "id" => $this->consultationSession->id,
        ];
        
        $maxEndTime = (new \DateTime())->format("Y-m-d");
        $uri = $this->consultationSessionUri . "?maxEndTime=$maxEndTime";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_containParticipantFeedbackFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $listResponse = [
            "id" => $this->consultationSession->id,
        ];
        
        $uri = $this->consultationSessionUri . "?containParticipantFeedback=true";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_containConsultantFeedbackFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $listResponse = [
            "id" => $this->consultationSession->id,
        ];
        
        $uri = $this->consultationSessionUri . "?containConsultantFeedback=false";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
    }

}
