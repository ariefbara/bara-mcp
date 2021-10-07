<?php

namespace Tests\Controllers\Client;

use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramRegistration;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class ActiveTeamProgramRegistrationControllerTest extends ClientTestCase
{
    protected $activeTeamProgramRegistrationUri;
    protected $teamMemberOne;
    protected $teamMemberTwo_inactive;
    protected $teamMemberThree;
    protected $teamOneProgramRegistrationOne;
    protected $teamOneProgramRegistrationTwo_concluded;
    protected $teamOneProgramRegistrationThree;
    protected $teamTwoProgramRegistrationOne;
    protected $teamThreeProgramRegistrationOne;
    
    protected $firmFileInfoThree;

    protected function setUp(): void
    {
        parent::setUp();
        $this->activeTeamProgramRegistrationUri = $this->clientUri . '/active-team-program-registrations';
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
        
        $firm = $this->client->firm;
        
        $teamOne = new RecordOfTeam($firm, $this->client, '1');
        $teamTwo = new RecordOfTeam($firm, $this->client, '2');
        $teamThree = new RecordOfTeam($firm, $this->client, '3');
        $this->connection->table('Team')->insert($teamOne->toArrayForDbEntry());
        $this->connection->table('Team')->insert($teamTwo->toArrayForDbEntry());
        $this->connection->table('Team')->insert($teamThree->toArrayForDbEntry());
        
        $this->teamMemberOne = new RecordOfMember($teamOne, $this->client, '1');
        $this->teamMemberTwo_inactive = new RecordOfMember($teamTwo, $this->client, '2');
        $this->teamMemberTwo_inactive->active = false;
        $this->teamMemberThree = new RecordOfMember($teamThree, $this->client, '3');
        $this->connection->table('T_Member')->insert($this->teamMemberOne->toArrayForDbEntry());
        $this->connection->table('T_Member')->insert($this->teamMemberTwo_inactive->toArrayForDbEntry());
        $this->connection->table('T_Member')->insert($this->teamMemberThree->toArrayForDbEntry());
        
        $fileInfoThree = new RecordOfFileInfo("2");
        
        $this->firmFileInfoThree = new RecordOfFirmFileInfo($firm, $fileInfoThree);
        $this->firmFileInfoThree->insert($this->connection);
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        $programThree = new RecordOfProgram($firm, '3');
        $programThree->illustration = $this->firmFileInfoThree;
        $this->connection->table('Program')->insert($programOne->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programTwo->toArrayForDbEntry());
        $this->connection->table('Program')->insert($programThree->toArrayForDbEntry());
        
        $programOneRegistrantOne = new RecordOfRegistrant($programOne, '11');
        $programOneRegistrantTwo = new RecordOfRegistrant($programOne, '12');
        $programOneRegistrantThree = new RecordOfRegistrant($programOne, '13');
        $programTwoRegistrantOne = new RecordOfRegistrant($programTwo, '21');
        $programTwoRegistrantOne->concluded = true;
        $programThreeRegistrantOne = new RecordOfRegistrant($programThree, '31');
        $this->connection->table('Registrant')->insert($programOneRegistrantOne->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($programOneRegistrantTwo->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($programOneRegistrantThree->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($programTwoRegistrantOne->toArrayForDbEntry());
        $this->connection->table('Registrant')->insert($programThreeRegistrantOne->toArrayForDbEntry());
        
        $this->teamOneProgramRegistrationOne = new RecordOfTeamProgramRegistration($teamOne, $programOneRegistrantOne);
        $this->teamOneProgramRegistrationTwo_concluded = new RecordOfTeamProgramRegistration($teamOne, $programTwoRegistrantOne);
        $this->teamOneProgramRegistrationThree = new RecordOfTeamProgramRegistration($teamOne, $programThreeRegistrantOne);
        $this->teamTwoProgramRegistrationOne = new RecordOfTeamProgramRegistration($teamTwo, $programOneRegistrantTwo);
        $this->teamThreeProgramRegistrationOne = new RecordOfTeamProgramRegistration($teamThree, $programOneRegistrantThree);
        $this->connection->table('TeamRegistrant')->insert($this->teamOneProgramRegistrationOne->toArrayForDbEntry());
        $this->connection->table('TeamRegistrant')->insert($this->teamOneProgramRegistrationTwo_concluded->toArrayForDbEntry());
        $this->connection->table('TeamRegistrant')->insert($this->teamOneProgramRegistrationThree->toArrayForDbEntry());
        $this->connection->table('TeamRegistrant')->insert($this->teamTwoProgramRegistrationOne->toArrayForDbEntry());
        $this->connection->table('TeamRegistrant')->insert($this->teamThreeProgramRegistrationOne->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Registrant')->truncate();
        $this->connection->table('TeamRegistrant')->truncate();
        $this->connection->table('FirmFileInfo')->truncate();
        $this->connection->table('FileInfo')->truncate();
    }
    
    public function test_showAll_200()
    {
        $this->get($this->activeTeamProgramRegistrationUri, $this->client->token)
                ->seeStatusCode(200);
        
        $response = [
            'list' => [
                [
                    'id' => $this->teamMemberOne->id,
                    'team' => [
                        'id' => $this->teamMemberOne->team->id,
                        'name' => $this->teamMemberOne->team->name,
                        'programRegistrations' => [
                            [
                                'id' => $this->teamOneProgramRegistrationOne->id,
                                'registeredTime' => $this->teamOneProgramRegistrationOne->registrant->registeredTime,
                                'program' => [
                                    'id' => $this->teamOneProgramRegistrationOne->registrant->program->id,
                                    'name' => $this->teamOneProgramRegistrationOne->registrant->program->name,
                                    "illustration" => null,
                                ],
                            ],
                            [
                                'id' => $this->teamOneProgramRegistrationThree->id,
                                'registeredTime' => $this->teamOneProgramRegistrationThree->registrant->registeredTime,
                                'program' => [
                                    'id' => $this->teamOneProgramRegistrationThree->registrant->program->id,
                                    'name' => $this->teamOneProgramRegistrationThree->registrant->program->name,
                                    "illustration" => [
                                        "id" => $this->firmFileInfoThree->id,
                                        "url" => "/{$this->firmFileInfoThree->fileInfo->name}",
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => $this->teamMemberThree->id,
                    'team' => [
                        'id' => $this->teamMemberThree->team->id,
                        'name' => $this->teamMemberThree->team->name,
                        'programRegistrations' => [
                            [
                                'id' => $this->teamThreeProgramRegistrationOne->id,
                                'registeredTime' => $this->teamThreeProgramRegistrationOne->registrant->registeredTime,
                                'program' => [
                                    'id' => $this->teamThreeProgramRegistrationOne->registrant->program->id,
                                    'name' => $this->teamThreeProgramRegistrationOne->registrant->program->name,
                                    "illustration" => null,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
