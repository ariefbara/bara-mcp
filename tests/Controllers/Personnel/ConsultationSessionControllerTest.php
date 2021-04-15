<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class ConsultationSessionControllerTest extends PersonnelTestCase
{
    protected $consultationSessionUri;
    protected $consultationSessionOne;
    protected $consultationSessionTwo;
    protected $participantOne_client;
    protected $participantTwo_team;
    protected $clientParticipant;
    protected $teamParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        
        $this->consultationSessionUri = $this->personnelUri . "/consultation-sessions";

        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $this->connection->table('Program')->insert($programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programTwo->toArrayForDbEntry());
        
        $consultationSetupOne = new RecordOfConsultationSetup($programOne, null, null, '1');
        $consultationSetupTwo = new RecordOfConsultationSetup($programTwo, null, null, '2');
        $this->connection->table('ConsultationSetup')->insert($consultationSetupOne->toArrayForDbEntry());
        $this->connection->table('ConsultationSetup')->insert($consultationSetupTwo->toArrayForDbEntry());
        
        $this->participantOne_client = new RecordOfParticipant($programOne, '1');
        $this->participantTwo_team = new RecordOfParticipant($programTwo, '2');
        $this->connection->table('Participant')->insert($this->participantOne_client->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($this->participantTwo_team->toArrayForDbEntry());
        
        $consultantOne = new RecordOfConsultant($programOne, $this->personnel, '1');
        $consultantTwo = new RecordOfConsultant($programTwo, $this->personnel, '2');
        $this->connection->table('Consultant')->insert($consultantOne->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($consultantTwo->toArrayForDbEntry());
        
        $this->consultationSessionOne = new RecordOfConsultationSession($consultationSetupOne, $this->participantOne_client, $consultantOne, '1');
        $this->consultationSessionOne->startDateTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionOne->endDateTime = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionTwo = new RecordOfConsultationSession($consultationSetupTwo, $this->participantTwo_team, $consultantTwo, '2');
        $this->consultationSessionTwo->startDateTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionTwo->endDateTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        $this->connection->table('ConsultationSession')->insert($this->consultationSessionOne->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($this->consultationSessionTwo->toArrayForDbEntry());
        
        $clientOne = new RecordOfClient($firm, '1');
        $this->connection->table('Client')->insert($clientOne->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($clientOne, $this->participantOne_client);
        $this->connection->table('ClientParticipant')->insert($this->clientParticipant->toArrayForDbEntry());
        
        $teamOne = new RecordOfTeam($firm, $clientOne, '1');
        $this->connection->table('Team')->insert($teamOne->toArrayForDbEntry());
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($teamOne, $this->participantTwo_team);
        $this->connection->table('TeamParticipant')->insert($this->teamParticipant->toArrayForDbEntry());
        
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
    }
    
    public function test_showAll_200()
    {
        $uri = $this->consultationSessionUri;
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200);
        
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultationSessionOne->id,
                    "startTime" => $this->consultationSessionOne->startDateTime,
                    "endTime" => $this->consultationSessionOne->endDateTime,
                    "media" => $this->consultationSessionOne->media,
                    "address" => $this->consultationSessionOne->address,
                    "hasConsultantFeedback" => false,
                    "participant" => [
                        "id" => $this->consultationSessionOne->participant->id,
                        "user" => null,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "team" => null,
                    ],
                    'consultant' => [
                        "id" => $this->consultationSessionOne->consultant->id,
                        'program' => [
                            "id" => $this->consultationSessionOne->consultant->program->id,
                            "name" => $this->consultationSessionOne->consultant->program->name,
                        ],
                        
                    ],
                ],
                [
                    "id" => $this->consultationSessionTwo->id,
                    "startTime" => $this->consultationSessionTwo->startDateTime,
                    "endTime" => $this->consultationSessionTwo->endDateTime,
                    "media" => $this->consultationSessionTwo->media,
                    "address" => $this->consultationSessionTwo->address,
                    "hasConsultantFeedback" => false,
                    "participant" => [
                        "id" => $this->consultationSessionTwo->participant->id,
                        "user" => null,
                        "client" => null,
                        "team" => [
                            "id" => $this->teamParticipant->team->id,
                            "name" => $this->teamParticipant->team->name,
                        ],
                    ],
                    'consultant' => [
                        "id" => $this->consultationSessionTwo->consultant->id,
                        'program' => [
                            "id" => $this->consultationSessionTwo->consultant->program->id,
                            "name" => $this->consultationSessionTwo->consultant->program->name,
                        ],
                        
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_hasMinStartTimeFilter()
    {
        $response = [
            "total" => 1,
        ];
        $consultationSessionResponse = [
            "id" => $this->consultationSessionOne->id
        ];
        $minStartTimeString = (new DateTime())->format('Y-m-d H:i:s');
        $uri = $this->consultationSessionUri
                . "?minStartTime=$minStartTimeString";

        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeJsonContains($consultationSessionResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_maxStartTimeAndConsultantFeedbackSetFilter()
    {
        $response = [
            "total" => 1,
        ];
        $consultationSessionResponse = [
            "id" => $this->consultationSessionTwo->id
        ];
        $maxEndTimeString = (new DateTime())->format('Y-m-d H:i:s');
        $uri = $this->consultationSessionUri
                . "?maxEndTime=$maxEndTimeString"
                . "&containConsultantFeedback=false";

        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeJsonContains($consultationSessionResponse)
                ->seeStatusCode(200);
    }
}
