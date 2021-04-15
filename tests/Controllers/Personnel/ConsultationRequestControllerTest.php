<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class ConsultationRequestControllerTest extends PersonnelTestCase
{
    protected $consultationRequestUri;
    protected $consultationRequestOne;
    protected $consultationRequestTwo;
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
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        
        $this->consultationRequestUri = $this->personnelUri . "/consultation-requests";

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
        
        $this->consultationRequestOne = new RecordOfConsultationRequest($consultationSetupOne, $this->participantOne_client, $consultantOne, '1');
        $this->consultationRequestOne->startDateTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->consultationRequestOne->endDateTime = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->consultationRequestTwo = new RecordOfConsultationRequest($consultationSetupTwo, $this->participantTwo_team, $consultantTwo, '2');
        $this->consultationRequestTwo->startDateTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->consultationRequestTwo->endDateTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestOne->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestTwo->toArrayForDbEntry());
        
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
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
    }
    
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $uri = $this->consultationRequestUri;
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200);
        
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultationRequestOne->id,
                    "startTime" => $this->consultationRequestOne->startDateTime,
                    "endTime" => $this->consultationRequestOne->endDateTime,
                    "media" => $this->consultationRequestOne->media,
                    "address" => $this->consultationRequestOne->address,
                    "concluded" => $this->consultationRequestOne->concluded,
                    "status" => $this->consultationRequestOne->status,
                    'consultationSetup' => [
                        "id" => $this->consultationRequestOne->consultationSetup->id,
                        "name" => $this->consultationRequestOne->consultationSetup->name,
                    ],
                    "participant" => [
                        "id" => $this->consultationRequestOne->participant->id,
                        "user" => null,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "team" => null,
                    ],
                    'consultant' => [
                        "id" => $this->consultationRequestOne->consultant->id,
                        'program' => [
                            "id" => $this->consultationRequestOne->consultant->program->id,
                            "name" => $this->consultationRequestOne->consultant->program->name,
                        ],
                        
                    ],
                    
                ],
                [
                    "id" => $this->consultationRequestTwo->id,
                    "startTime" => $this->consultationRequestTwo->startDateTime,
                    "endTime" => $this->consultationRequestTwo->endDateTime,
                    "media" => $this->consultationRequestTwo->media,
                    "address" => $this->consultationRequestTwo->address,
                    "concluded" => $this->consultationRequestTwo->concluded,
                    "status" => $this->consultationRequestTwo->status,
                    'consultationSetup' => [
                        "id" => $this->consultationRequestTwo->consultationSetup->id,
                        "name" => $this->consultationRequestTwo->consultationSetup->name,
                    ],
                    "participant" => [
                        "id" => $this->consultationRequestTwo->participant->id,
                        "user" => null,
                        "client" => null,
                        "team" => [
                            "id" => $this->teamParticipant->team->id,
                            "name" => $this->teamParticipant->team->name,
                        ],
                    ],
                    'consultant' => [
                        "id" => $this->consultationRequestTwo->consultant->id,
                        'program' => [
                            "id" => $this->consultationRequestTwo->consultant->program->id,
                            "name" => $this->consultationRequestTwo->consultant->program->name,
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
        $consultationRequestResponse = [
            "id" => $this->consultationRequestOne->id
        ];
        $minStartTimeString = (new DateTime())->format('Y-m-d H:i:s');
        $uri = $this->consultationRequestUri
                . "?minStartTime=$minStartTimeString";

        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeJsonContains($consultationRequestResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_maxStartTimeFilter()
    {
        $response = [
            "total" => 1,
        ];
        $consultationRequestResponse = [
            "id" => $this->consultationRequestTwo->id
        ];
        $maxEndTimeString = (new DateTime())->format('Y-m-d H:i:s');
        $uri = $this->consultationRequestUri
                . "?maxEndTime=$maxEndTimeString";

        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeJsonContains($consultationRequestResponse)
                ->seeStatusCode(200);
    }
}
