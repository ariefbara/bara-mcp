<?php

namespace Tests\Controllers\User;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ConsultationRequestControllerTest extends UserTestCase
{
    protected $consultationRequestUri;
    protected $consultationRequestOne;
    protected $consultationRequestTwo;
    protected $consultationRequestThree;
    protected $userParticipantOne;
    protected $userParticipantTwo;
    protected $userParticipantThree_inactive;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestUri = $this->userUri . "/consultation-requests";
        
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        
        $firmOne = new RecordOfFirm('1');
        $firmTwo = new RecordOfFirm('2');
        $firmThree = new RecordOfFirm('3');
        $this->connection->table('Firm')->insert($firmOne->toArrayForDbEntry());
        $this->connection->table('Firm')->insert($firmTwo->toArrayForDbEntry());
        $this->connection->table('Firm')->insert($firmThree->toArrayForDbEntry());
        
        $programOne = new RecordOfProgram($firmOne, '1');
        $programTwo = new RecordOfProgram($firmTwo, '2');
        $programThree = new RecordOfProgram($firmThree, '3');
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
        $participantThree->active = false;
        $this->connection->table('Participant')->insert($participantOne->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantTwo->toArrayForDbEntry());
        $this->connection->table('Participant')->insert($participantThree->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firmOne, '99');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        
        $consultantOne = new RecordOfConsultant($programOne, $personnel, '1');
        $consultantTwo = new RecordOfConsultant($programTwo, $personnel, '2');
        $consultantThree = new RecordOfConsultant($programThree, $personnel, '3');
        $this->connection->table('Consultant')->insert($consultantOne->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($consultantTwo->toArrayForDbEntry());
        $this->connection->table('Consultant')->insert($consultantThree->toArrayForDbEntry());
        
        $this->consultationRequestOne = new RecordOfConsultationRequest($consultationSetupOne, $participantOne, $consultantOne, '1');
        $this->consultationRequestOne->concluded = true;
        $this->consultationRequestOne->status = 'scheduled';
        $this->consultationRequestTwo = new RecordOfConsultationRequest($consultationSetupTwo, $participantTwo, $consultantTwo, '2');
        $this->consultationRequestTwo->startDateTime = (new DateTime('+1 months'))->format('Y-m-d H:i:s');
        $this->consultationRequestThree = new RecordOfConsultationRequest($consultationSetupThree, $participantThree, $consultantThree, '3');
        $this->consultationRequestThree->startDateTime = (new DateTime('+2 months'))->format('Y-m-d H:i:s');
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestOne->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestTwo->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequestThree->toArrayForDbEntry());
        
        $this->userParticipantOne = new RecordOfUserParticipant($this->user, $participantOne);
        $this->userParticipantTwo = new RecordOfUserParticipant($this->user, $participantTwo);
        $this->userParticipantThree_inactive = new RecordOfUserParticipant($this->user, $participantThree);
        $this->connection->table('UserParticipant')->insert($this->userParticipantOne->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($this->userParticipantTwo->toArrayForDbEntry());
        $this->connection->table('UserParticipant')->insert($this->userParticipantThree_inactive->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('UserParticipant')->truncate();
    }
    
    public function test_showAll_200()
    {
        $uri = $this->consultationRequestUri;
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        $consultationRequestOneResponse = [
            "id" => $this->consultationRequestOne->id,
            "startTime" => $this->consultationRequestOne->startDateTime,
            "endTime" => $this->consultationRequestOne->endDateTime,
            "media" => $this->consultationRequestOne->media,
            "address" => $this->consultationRequestOne->address,
            "concluded" => $this->consultationRequestOne->concluded,
            "status" => $this->consultationRequestOne->status,
            "consultationSetup" => [
                "id" => $this->consultationRequestOne->consultationSetup->id,
                "name" => $this->consultationRequestOne->consultationSetup->name,
            ],
            "participant" => [
                "id" => $this->consultationRequestOne->participant->id,
                'program' => [
                    "id" => $this->consultationRequestOne->participant->program->id,
                    "name" => $this->consultationRequestOne->participant->program->name,
                ],
            ],
            'consultant' => [
                "id" => $this->consultationRequestOne->consultant->id,
                'personnel' => [
                    "id" => $this->consultationRequestOne->consultant->personnel->id,
                    "name" => $this->consultationRequestOne->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($consultationRequestOneResponse);
        $consultationRequestTwoResponse = [
            "id" => $this->consultationRequestTwo->id,
            "startTime" => $this->consultationRequestTwo->startDateTime,
            "endTime" => $this->consultationRequestTwo->endDateTime,
            "media" => $this->consultationRequestTwo->media,
            "address" => $this->consultationRequestTwo->address,
            "concluded" => $this->consultationRequestTwo->concluded,
            "status" => $this->consultationRequestTwo->status,
            "consultationSetup" => [
                "id" => $this->consultationRequestTwo->consultationSetup->id,
                "name" => $this->consultationRequestTwo->consultationSetup->name,
            ],
            "participant" => [
                "id" => $this->consultationRequestTwo->participant->id,
                'program' => [
                    "id" => $this->consultationRequestTwo->participant->program->id,
                    "name" => $this->consultationRequestTwo->participant->program->name,
                ],
            ],
            'consultant' => [
                "id" => $this->consultationRequestTwo->consultant->id,
                'personnel' => [
                    "id" => $this->consultationRequestTwo->consultant->personnel->id,
                    "name" => $this->consultationRequestTwo->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($consultationRequestTwoResponse);
    }
    public function test_showAll_minMaxStartTimeFilter()
    {
        $minStarTime = (new DateTime('first day of this month'))->setTime(00, 00, 00)->format('Y-m-d H:i:s');
        $maxStarTime = (new DateTime('last day of this month'))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        
        $uri = $this->consultationRequestUri
                . "?minStartTime={$minStarTime}"
                . "&maxStartTime={$maxStarTime}";
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $response = [
            'id' => $this->consultationRequestOne->id,
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_concludedFilter()
    {
        $uri = $this->consultationRequestUri
                . "?concludedStatus=true";
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $response = [
            'id' => $this->consultationRequestOne->id,
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_statusFilter()
    {
        $uri = $this->consultationRequestUri
                . "?status[]=proposed";
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $response = [
            'id' => $this->consultationRequestTwo->id,
        ];
        $this->seeJsonContains($response);
    }
}
