<?php

namespace Tests\Controllers\Client;

use Query\Domain\Model\Firm\ParticipantTypes;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfRegistrationPhase;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFirmFileInfo;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class ProgramControllerTest extends ClientTestCase
{
    protected $programUri;
    
    protected $programOne;
    protected $programTwo;
    protected $programThree;
    protected $programFour;
    
    protected $registrationPhaseOne_p1;
    protected $registrationPhaseTwo_p1;
    protected $registrationPhaseThree_p2;
    protected $registrationPhaseFour_p3;
    protected $registrationPhaseFive_p4;
    
    protected $clientParticipantOne_p1;
    protected $clientRegistrantOne_p2;

    protected $firmFileInfo;
    protected $firmFileInfoOne;
    protected $firmFileInfoThree;
    
    protected $otherFirm;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table("Program")->truncate();
        $this->connection->table("RegistrationPhase")->truncate();
        $this->connection->table("Registrant")->truncate();
        $this->connection->table("ClientRegistrant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("FileInfo")->truncate();
        $this->connection->table("FirmFileInfo")->truncate();
        
        $this->programUri = $this->clientUri . "/programs";
        
        $firm = $this->client->firm;
        
        $fileInfo = new RecordOfFileInfo("99");
        $fileInfoOne = new RecordOfFileInfo("1");
        $fileInfoThree = new RecordOfFileInfo("3");
        
        $this->firmFileInfo = new RecordOfFirmFileInfo($firm, $fileInfo);
        $this->firmFileInfoOne = new RecordOfFirmFileInfo($firm, $fileInfoOne);
        $this->firmFileInfoThree = new RecordOfFirmFileInfo($firm, $fileInfoThree);
        
        $this->programOne = new RecordOfProgram($firm, 1);
        $this->programOne->illustration = $this->firmFileInfo;
        
        $this->programTwo = new RecordOfProgram($firm, 2);
        $this->programTwo->illustration = $this->firmFileInfoOne;
        
        $this->programThree = new RecordOfProgram($firm, 3);
        
        $this->programFour = new RecordOfProgram($firm, 4);
        $this->programFour->illustration = $this->firmFileInfoThree;
        
        $this->registrationPhaseOne_p1 = new RecordOfRegistrationPhase($this->programOne, '1');
        $this->registrationPhaseOne_p1->startDate = (new \DateTimeImmutable('-30 days'))->format('Y-m-d');
        $this->registrationPhaseOne_p1->endDate = (new \DateTimeImmutable('+30 days'))->format('Y-m-d');
        
        $this->registrationPhaseTwo_p1 = new RecordOfRegistrationPhase($this->programOne, '2');
        $this->registrationPhaseTwo_p1->startDate = (new \DateTimeImmutable('+60 days'))->format('Y-m-d');
        $this->registrationPhaseTwo_p1->endDate = (new \DateTimeImmutable('+90 days'))->format('Y-m-d');
        
        $this->registrationPhaseThree_p2 = new RecordOfRegistrationPhase($this->programTwo, '3');
        $this->registrationPhaseThree_p2->startDate = (new \DateTimeImmutable())->format('Y-m-d');
        $this->registrationPhaseThree_p2->endDate = (new \DateTimeImmutable())->format('Y-m-d');
        
        $this->registrationPhaseFour_p3 = new RecordOfRegistrationPhase($this->programThree, '4');
        $this->registrationPhaseFour_p3->startDate = (new \DateTimeImmutable())->format('Y-m-d');
        $this->registrationPhaseFour_p3->endDate = (new \DateTimeImmutable('+60 days'))->format('Y-m-d');
        
        $this->registrationPhaseFive_p4 = new RecordOfRegistrationPhase($this->programFour, '5');
        $this->registrationPhaseFive_p4->startDate = (new \DateTimeImmutable('-60 days'))->format('Y-m-d');
        $this->registrationPhaseFive_p4->endDate = (new \DateTimeImmutable())->format('Y-m-d');
        
        $participantOne = new RecordOfParticipant($this->programOne, '1');
        $this->clientParticipantOne_p1 = new RecordOfClientParticipant($this->client, $participantOne);
        
        $registrantOne = new RecordOfRegistrant($this->programTwo, '1');
        $this->clientRegistrantOne_p2 = new RecordOfClientRegistrant($this->client, $registrantOne);
        
        $this->otherFirm = new RecordOfFirm('other');
    }
    
    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table("Program")->truncate();
//        $this->connection->table("RegistrationPhase")->truncate();
//        $this->connection->table("Registrant")->truncate();
//        $this->connection->table("ClientRegistrant")->truncate();
//        $this->connection->table("Participant")->truncate();
//        $this->connection->table("ClientParticipant")->truncate();
//        $this->connection->table("FileInfo")->truncate();
//        $this->connection->table("FirmFileInfo")->truncate();
    }
    
    protected function show()
    {
        $this->programOne->illustration->insert($this->connection);
        $this->programOne->insert($this->connection);
        
        $uri = $this->programUri . "/{$this->programOne->id}";
        $this->get($uri, $this->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->programOne->id,
            "name" => $this->programOne->name,
            "description" => $this->programOne->description,
            "published" => $this->programOne->published,
            "participantTypes" => explode(",", $this->programOne->participantTypes),
            "removed" => $this->programOne->removed,
            "illustration" => [
                "id" => $this->programOne->illustration->fileInfo->id,
                "url" => "/{$this->programOne->illustration->fileInfo->name}",
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->programOne->illustration->insert($this->connection);
        $this->programTwo->illustration->insert($this->connection);
        
        $this->programOne->insert($this->connection);
        $this->programTwo->insert($this->connection);
        
        $this->get($this->programUri, $this->client->token);
    }
    public function test_showAll_200_returnProgramContainClientPartipantType()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->programOne->id,
                    "name" => $this->programOne->name,
                    "description" => $this->programOne->description,
                    "published" => $this->programOne->published,
                    "participantTypes" => explode(",", $this->programOne->participantTypes),
                    "removed" => $this->programOne->removed,
                    "illustration" => [
                        "id" => $this->programOne->illustration->fileInfo->id,
                        "url" => "/{$this->programOne->illustration->fileInfo->name}",
                    ],
                ],
                [
                    "id" => $this->programTwo->id,
                    "name" => $this->programTwo->name,
                    "description" => $this->programTwo->description,
                    "published" => $this->programTwo->published,
                    "participantTypes" => explode(",", $this->programTwo->participantTypes),
                    "removed" => $this->programTwo->removed,
                    "illustration" => [
                        "id" => $this->programTwo->illustration->fileInfo->id,
                        "url" => "/{$this->programTwo->illustration->fileInfo->name}",
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_containProgramFromOtherFirm_excludeFromResult()
    {
        $this->otherFirm->insert($this->connection);
        $this->programTwo->firm = $this->otherFirm;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $programTwoResponse = [
            'id' => $this->programTwo->id,
        ];
        $this->seeJsonDoesntContains($programTwoResponse);
    }
    
    protected function showAllProgramForTeam()
    {
        $this->programOne->illustration->insert($this->connection);
        $this->programTwo->illustration->insert($this->connection);
        
        $this->programOne->insert($this->connection);
        $this->programTwo->insert($this->connection);
        
        $uri = $this->programUri . "/team-types";
        $this->get($uri, $this->client->token);
    }
    public function test_showAllProgramForTeam_200_returnAllProgramForTeamType()
    {
        $this->showAllProgramForTeam();
        
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->programOne->id,
                    "name" => $this->programOne->name,
                    "description" => $this->programOne->description,
                    "published" => $this->programOne->published,
                    "participantTypes" => explode(",", $this->programOne->participantTypes),
                    "removed" => $this->programOne->removed,
                    "illustration" => [
                        "id" => $this->programOne->illustration->fileInfo->id,
                        "url" => "/{$this->programOne->illustration->fileInfo->name}",
                    ],
                ],
                [
                    "id" => $this->programTwo->id,
                    "name" => $this->programTwo->name,
                    "description" => $this->programTwo->description,
                    "published" => $this->programTwo->published,
                    "participantTypes" => explode(",", $this->programTwo->participantTypes),
                    "removed" => $this->programTwo->removed,
                    "illustration" => [
                        "id" => $this->programTwo->illustration->fileInfo->id,
                        "url" => "/{$this->programTwo->illustration->fileInfo->name}",
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAllProgramForTeam_containProgramWithoutTeamType_excludeFromResult()
    {
        $this->programOne->participantTypes = ParticipantTypes::CLIENT_TYPE;
        $this->showAllProgramForTeam();
        $this->seeStatusCode(200);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonDoesntContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
    }
    public function test_showAllProgramForTeam_containProgramFromOtherFirm_expectedResult()
    {
        $this->otherFirm->insert($this->connection);
        $this->programOne->firm = $this->otherFirm;
        $this->showAllProgramForTeam();
        $this->seeStatusCode(200);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonDoesntContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
    }
    
    protected function showAllAvailableProgram()
    {
        $this->programOne->illustration->insert($this->connection);
        $this->programTwo->illustration->insert($this->connection);
        $this->programFour->illustration->insert($this->connection);
        
        $this->programOne->insert($this->connection);
        $this->programTwo->insert($this->connection);
        $this->programThree->insert($this->connection);
        $this->programFour->insert($this->connection);
        
        $this->registrationPhaseOne_p1->insert($this->connection);
        $this->registrationPhaseTwo_p1->insert($this->connection);
        $this->registrationPhaseThree_p2->insert($this->connection);
        $this->registrationPhaseFour_p3->insert($this->connection);
        $this->registrationPhaseFive_p4->insert($this->connection);
        
        $uri = $this->programUri . "/all-available";
        $this->get($uri, $this->client->token);
    }
    public function test_showAllAvailable_200()
    {
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 4,
            'list' => [
                [
                    'id' => $this->programOne->id,
                    'name' => $this->programOne->name,
                    'description' => $this->programOne->description,
                    'participantTypes' => $this->programOne->participantTypes,
                    'illustrationPaths' => $this->programOne->illustration->fileInfo->folders,
                    'illustrationName' => $this->programOne->illustration->fileInfo->name,
                ],
                [
                    'id' => $this->programTwo->id,
                    'name' => $this->programTwo->name,
                    'description' => $this->programTwo->description,
                    'participantTypes' => $this->programTwo->participantTypes,
                    'illustrationPaths' => $this->programTwo->illustration->fileInfo->folders,
                    'illustrationName' => $this->programTwo->illustration->fileInfo->name,
                ],
                [
                    'id' => $this->programThree->id,
                    'name' => $this->programThree->name,
                    'description' => $this->programThree->description,
                    'participantTypes' => $this->programThree->participantTypes,
                    'illustrationPaths' => null,
                    'illustrationName' => null,
                ],
                [
                    'id' => $this->programFour->id,
                    'name' => $this->programFour->name,
                    'description' => $this->programFour->description,
                    'participantTypes' => $this->programFour->participantTypes,
                    'illustrationPaths' => $this->programFour->illustration->fileInfo->folders,
                    'illustrationName' => $this->programFour->illustration->fileInfo->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAllAvailable_containProgramWithNoRegistrationTimeLimit()
    {
        $this->registrationPhaseThree_p2->startDate = null;
        $this->registrationPhaseThree_p2->endDate = null;
        
        $this->registrationPhaseFour_p3->startDate = null;
        $this->registrationPhaseFour_p3->endDate = (new \DateTimeImmutable('+60 days'));
        
        $this->registrationPhaseFive_p4->startDate = (new \DateTimeImmutable('-30 days'));
        $this->registrationPhaseFive_p4->endDate = null;
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 4];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
        
    }
    public function test_showAllAvailable_containProgramFromOtherFirm_exclude()
    {
        $this->otherFirm->insert($this->connection);
        $this->programThree->firm = $this->otherFirm;
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonDoesntContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
    }
    public function test_showAllAvailable_containProgramOnlyForUser_exclude()
    {
        $this->programTwo->participantTypes = ParticipantTypes::USER_TYPE;
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonDoesntContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
    }
    public function test_showAllAvailable_closedRegistration_containProgramWithoutRegistrationPhase_exclude()
    {
        $closedProgram = new RecordOfProgram($this->client->firm, 'closed');
        $closedProgram->insert($this->connection);
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 4];
        $this->seeJsonContains($totalResponse);
        
        $closedProgramResponse = ['id' => $closedProgram->id];
        $this->seeJsonDoesntContains($closedProgramResponse);
    }
    public function test_showAllAvailable_closedRegistration_containProgramWithExpiredRegistrationPhaseOnly_exclude()
    {
        $this->registrationPhaseFour_p3->startDate = (new \DateTimeImmutable('-60 days'))->format('Y-m-d');
        $this->registrationPhaseFour_p3->endDate = (new \DateTimeImmutable('-30 days'))->format('Y-m-d');
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonDoesntContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
    }
    public function test_showAllAvailable_closedRegistration_containProgramWithUpcomingRegistrationPhaseOnly_exclude()
    {
        $this->registrationPhaseFour_p3->startDate = (new \DateTimeImmutable('+30 days'))->format('Y-m-d');
        $this->registrationPhaseFour_p3->endDate = (new \DateTimeImmutable('+60 days'))->format('Y-m-d');
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonDoesntContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
    }
    public function test_showAllAvailable_exlcludeActivelyRegisteredProgram()
    {
        $this->clientRegistrantOne_p2->insert($this->connection);
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonDoesntContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
    }
    public function test_showAllAvailable_includeRegisteredProgramInConcludedState()
    {
        $this->clientRegistrantOne_p2->registrant->concluded = true;
        $this->clientRegistrantOne_p2->insert($this->connection);
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 4];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
    }
    public function test_showAllAvailable_excludeActivelyParticipatedProgram()
    {
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonDoesntContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
    }
    public function test_showAllAvailable_includeParticipatingProgramInInactiveState()
    {
        $this->clientParticipantOne_p1->participant->active = false;
        $this->clientParticipantOne_p1->insert($this->connection);
        
        $this->showAllAvailableProgram();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 4];
        $this->seeJsonContains($totalResponse);
        
        $programOneResponse = ['id' => $this->programOne->id];
        $this->seeJsonContains($programOneResponse);
        
        $programTwoResponse = ['id' => $this->programTwo->id];
        $this->seeJsonContains($programTwoResponse);
        
        $programThreeResponse = ['id' => $this->programThree->id];
        $this->seeJsonContains($programThreeResponse);
        
        $programFourResponse = ['id' => $this->programFour->id];
        $this->seeJsonContains($programFourResponse);
    }
    
}
