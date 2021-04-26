<?php

namespace Tests\Controllers\Client;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;


class ConsultationRequestControllerTest extends ClientTestCase
{
    protected $consultationRequestUri;
    protected $consultationRequestOne_client;
    protected $consultationRequestTwo_team;
    protected $consultationRequestThree_inactiveTeamMember;
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $teamParticipantThree_inactiveTeamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestUri = $this->clientUri . "/consultation-requests";
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        
        $firm = $this->client->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $programThree = new RecordOfProgram($firm, '3');
        $this->connection->table('Program')->insert($programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programTwo->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programThree->toArrayForDbEntry());

        $consultationSetupOne = new RecordOfConsultationSetup($programOne, null, null, '1');
        $consultationSetupTwo = new RecordOfConsultationSetup($programTwo, null, null, '2');
        $consultationSetupThree = new RecordOfConsultationSetup($programThree, null, null, '3');
        $this->connection->table('ConsultationSetup')->insert($consultationSetupOne->toArrayForDbEntry());
        $this->connection->table('ConsultationSetup')->insert($consultationSetupTwo->toArrayForDbEntry());
        $this->connection->table('ConsultationSetup')->insert($consultationSetupThree->toArrayForDbEntry());
        
        $participantOne = new RecordOfParticipant($programOne, '1');
        $participantTwo = new RecordOfParticipant($programTwo, '2');
        $participantThree = new RecordOfParticipant($programThree, '3');
        $this->connection->table('Participant')->insert($participantOne->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantTwo->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantThree->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, '99');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        
        $consultantOne = new RecordOfConsultant($programOne, $personnel, '1');
        $consultantTwo = new RecordOfConsultant($programTwo, $personnel, '2');
        $consultantThree = new RecordOfConsultant($programThree, $personnel, '3');
        $this->connection->table('Consultant')->insert($consultantOne->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($consultantTwo->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($consultantThree->toArrayForDbEntry());
        
        $this->consultationRequestOne_client = new RecordOfConsultationRequest($consultationSetupOne, $participantOne, $consultantOne, '1');
        $this->consultationRequestOne_client->concluded = true;
        $this->consultationRequestOne_client->status = 'scheduled';
        $this->consultationRequestTwo_team = new RecordOfConsultationRequest($consultationSetupTwo, $participantTwo, $consultantTwo, '2');
        $this->consultationRequestTwo_team->startDateTime = (new DateTime('+1 months'))->format('Y-m-d H:i:s');
        $this->consultationRequestThree_inactiveTeamMember = new RecordOfConsultationRequest($consultationSetupThree, $participantThree, $consultantThree, '3');
        $this->consultationRequestThree_inactiveTeamMember->startDateTime = (new DateTime('+2 months'))->format('Y-m-d H:i:s');
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestOne_client->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestTwo_team->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestThree_inactiveTeamMember->toArrayForDbEntry());
        
        $this->clientParticipantOne = new RecordOfClientParticipant($this->client, $participantOne);
        $this->connection->table('ClientParticipant')->insert($this->clientParticipantOne->toArrayForDbEntry());
        
        $teamTwo = new RecordOfTeam($firm, $this->client, '2');
        $teamThree = new RecordOfTeam($firm, $this->client, '3');
        $this->connection->table('Team')->insert($teamTwo->toArrayForDbEntry());
        $this->connection->table('Team')->insert($teamThree->toArrayForDbEntry());
        
        $memberTwo = new RecordOfMember($teamTwo, $this->client, '2');
        $memberThree = new RecordOfMember($teamThree, $this->client, '3');
        $memberThree->active = false;
        $this->connection->table('T_Member')->insert($memberTwo->toArrayForDbEntry());
        $this->connection->table('T_Member')->insert($memberThree->toArrayForDbEntry());
        
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamTwo, $participantTwo);
        $this->teamParticipantThree_inactiveTeamMember = new RecordOfTeamProgramParticipation($teamThree, $participantThree);
        $this->connection->table('TeamParticipant')->insert($this->teamParticipantTwo->toArrayForDbEntry());
        $this->connection->table('TeamParticipant')->insert($this->teamParticipantThree_inactiveTeamMember->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
    }
    
    public function test_showAll_200()
    {
        $uri = $this->consultationRequestUri;
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    "id" => $this->consultationRequestOne_client->id,
                    "startTime" => $this->consultationRequestOne_client->startDateTime,
                    "endTime" => $this->consultationRequestOne_client->endDateTime,
                    "media" => $this->consultationRequestOne_client->media,
                    "address" => $this->consultationRequestOne_client->address,
                    "concluded" => $this->consultationRequestOne_client->concluded,
                    "status" => $this->consultationRequestOne_client->status,
                    "consultationSetup" => [
                        "id" => $this->consultationRequestOne_client->consultationSetup->id,
                        "name" => $this->consultationRequestOne_client->consultationSetup->name,
                    ],
                    "participant" => [
                        "id" => $this->consultationRequestOne_client->participant->id,
                        'program' => [
                            "id" => $this->consultationRequestOne_client->participant->program->id,
                            "name" => $this->consultationRequestOne_client->participant->program->name,
                        ],
                        'team' => null,
                    ],
                    'consultant' => [
                        "id" => $this->consultationRequestOne_client->consultant->id,
                        'personnel' => [
                            "id" => $this->consultationRequestOne_client->consultant->personnel->id,
                            "name" => $this->consultationRequestOne_client->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
                [
                    "id" => $this->consultationRequestTwo_team->id,
                    "startTime" => $this->consultationRequestTwo_team->startDateTime,
                    "endTime" => $this->consultationRequestTwo_team->endDateTime,
                    "media" => $this->consultationRequestTwo_team->media,
                    "address" => $this->consultationRequestTwo_team->address,
                    "concluded" => $this->consultationRequestTwo_team->concluded,
                    "status" => $this->consultationRequestTwo_team->status,
                    "consultationSetup" => [
                        "id" => $this->consultationRequestTwo_team->consultationSetup->id,
                        "name" => $this->consultationRequestTwo_team->consultationSetup->name,
                    ],
                    "participant" => [
                        "id" => $this->consultationRequestTwo_team->participant->id,
                        'program' => [
                            "id" => $this->consultationRequestTwo_team->participant->program->id,
                            "name" => $this->consultationRequestTwo_team->participant->program->name,
                        ],
                        'team' => [
                            'id' => $this->teamParticipantTwo->team->id,
                            'name' => $this->teamParticipantTwo->team->name,
                        ],
                    ],
                    'consultant' => [
                        "id" => $this->consultationRequestTwo_team->consultant->id,
                        'personnel' => [
                            "id" => $this->consultationRequestTwo_team->consultant->personnel->id,
                            "name" => $this->consultationRequestTwo_team->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_minMaxStartTimeFilter()
    {
        $minStarTime = (new DateTime('first day of this month'))->setTime(00, 00, 00)->format('Y-m-d H:i:s');
        $maxStarTime = (new DateTime('last day of this month'))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        
        $uri = $this->consultationRequestUri
                . "?minStartTime={$minStarTime}"
                . "&maxStartTime={$maxStarTime}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $response = [
            'id' => $this->consultationRequestOne_client->id,
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_concludedStatusFilter()
    {
        $uri = $this->consultationRequestUri
                . "?concludedStatus=true";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $response = [
            'id' => $this->consultationRequestOne_client->id,
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_statusFilter()
    {
        $uri = $this->consultationRequestUri
                . "?status[]=proposed";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $response = [
            'id' => $this->consultationRequestTwo_team->id,
        ];
        $this->seeJsonContains($response);
    }
}
