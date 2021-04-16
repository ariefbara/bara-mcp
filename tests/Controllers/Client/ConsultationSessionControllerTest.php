<?php

namespace Tests\Controllers\Client;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class ConsultationSessionControllerTest extends ClientTestCase
{
    protected $consultationSessionUri;
    protected $consultationSessionOne_client;
    protected $consultationSessionTwo_team;
    protected $consultationSessionThree_inactiveTeamMember;
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $teamParticipantThree_inactiveTeamMember;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionUri = $this->clientUri . "/consultation-sessions";
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
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
        
        $this->consultationSessionOne_client = new RecordOfConsultationSession($consultationSetupOne, $participantOne, $consultantOne, '1');
        $this->consultationSessionTwo_team = new RecordOfConsultationSession($consultationSetupTwo, $participantTwo, $consultantTwo, '2');
        $this->consultationSessionTwo_team->startDateTime = (new DateTime('+1 months'))->format('Y-m-d H:i:s');
        $this->consultationSessionThree_inactiveTeamMember = new RecordOfConsultationSession($consultationSetupThree, $participantThree, $consultantThree, '3');
        $this->consultationSessionThree_inactiveTeamMember->startDateTime = (new DateTime('+2 months'))->format('Y-m-d H:i:s');
        $this->connection->table('ConsultationSession')->insert($this->consultationSessionOne_client->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($this->consultationSessionTwo_team->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($this->consultationSessionThree_inactiveTeamMember->toArrayForDbEntry());
        
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
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
    }
    
    public function test_showAll_200()
    {
        $uri = $this->consultationSessionUri;
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    "id" => $this->consultationSessionOne_client->id,
                    "startTime" => $this->consultationSessionOne_client->startDateTime,
                    "endTime" => $this->consultationSessionOne_client->endDateTime,
                    "media" => $this->consultationSessionOne_client->media,
                    "address" => $this->consultationSessionOne_client->address,
                    "hasParticipantFeedback" => false,
                    "participant" => [
                        "id" => $this->consultationSessionOne_client->participant->id,
                        'program' => [
                            "id" => $this->consultationSessionOne_client->participant->program->id,
                            "name" => $this->consultationSessionOne_client->participant->program->name,
                        ],
                        'team' => null,
                    ],
                    'consultant' => [
                        "id" => $this->consultationSessionOne_client->consultant->id,
                        'personnel' => [
                            "id" => $this->consultationSessionOne_client->consultant->personnel->id,
                            "name" => $this->consultationSessionOne_client->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
                [
                    "id" => $this->consultationSessionTwo_team->id,
                    "startTime" => $this->consultationSessionTwo_team->startDateTime,
                    "endTime" => $this->consultationSessionTwo_team->endDateTime,
                    "media" => $this->consultationSessionTwo_team->media,
                    "address" => $this->consultationSessionTwo_team->address,
                    "hasParticipantFeedback" => false,
                    "participant" => [
                        "id" => $this->consultationSessionTwo_team->participant->id,
                        'program' => [
                            "id" => $this->consultationSessionTwo_team->participant->program->id,
                            "name" => $this->consultationSessionTwo_team->participant->program->name,
                        ],
                        'team' => [
                            'id' => $this->teamParticipantTwo->team->id,
                            'name' => $this->teamParticipantTwo->team->name,
                        ],
                    ],
                    'consultant' => [
                        "id" => $this->consultationSessionTwo_team->consultant->id,
                        'personnel' => [
                            "id" => $this->consultationSessionTwo_team->consultant->personnel->id,
                            "name" => $this->consultationSessionTwo_team->consultant->personnel->getFullName(),
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
        
        $uri = $this->consultationSessionUri
                . "?minStartTime={$minStarTime}"
                . "&maxStartTime={$maxStarTime}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $response = [
            'id' => $this->consultationSessionOne_client->id,
        ];
        $this->seeJsonContains($response);
    }
}
