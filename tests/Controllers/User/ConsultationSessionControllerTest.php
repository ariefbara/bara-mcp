<?php

namespace Tests\Controllers\User;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\RecordOfFirm;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ConsultationSessionControllerTest extends UserTestCase
{
    protected $consultationSessionUri;
    protected $consultationSessionOne;
    protected $consultationSessionTwo;
    protected $consultationSessionThree;
    protected $userParticipantOne;
    protected $userParticipantTwo;
    protected $userParticipantThree_inactive;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionUri = $this->userUri . "/consultation-sessions";
        
        $this->connection->table('Firm')->truncate();
        $this->connection->table('Program')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
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
        
        $this->consultationSessionOne = new RecordOfConsultationSession($consultationSetupOne, $participantOne, $consultantOne, '1');
        $this->consultationSessionTwo = new RecordOfConsultationSession($consultationSetupTwo, $participantTwo, $consultantTwo, '2');
        $this->consultationSessionTwo->startDateTime = (new DateTime('+1 months'))->format('Y-m-d H:i:s');
        $this->consultationSessionThree = new RecordOfConsultationSession($consultationSetupThree, $participantThree, $consultantThree, '3');
        $this->consultationSessionThree->startDateTime = (new DateTime('+2 months'))->format('Y-m-d H:i:s');
        $this->connection->table('ConsultationSession')->insert($this->consultationSessionOne->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($this->consultationSessionTwo->toArrayForDbEntry());
        $this->connection->table('ConsultationSession')->insert($this->consultationSessionThree->toArrayForDbEntry());
        
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
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('UserParticipant')->truncate();
    }
    
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $uri = $this->consultationSessionUri;
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        $consultationSessionOneResponse = [
            "id" => $this->consultationSessionOne->id,
            "startTime" => $this->consultationSessionOne->startDateTime,
            "endTime" => $this->consultationSessionOne->endDateTime,
            "media" => $this->consultationSessionOne->media,
            "address" => $this->consultationSessionOne->address,
            "hasParticipantFeedback" => false,
            "participant" => [
                "id" => $this->consultationSessionOne->participant->id,
                'program' => [
                    "id" => $this->consultationSessionOne->participant->program->id,
                    "name" => $this->consultationSessionOne->participant->program->name,
                ],
            ],
            'consultant' => [
                "id" => $this->consultationSessionOne->consultant->id,
                'personnel' => [
                    "id" => $this->consultationSessionOne->consultant->personnel->id,
                    "name" => $this->consultationSessionOne->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($consultationSessionOneResponse);
        $consultationSessionTwoResponse = [
            "id" => $this->consultationSessionTwo->id,
            "startTime" => $this->consultationSessionTwo->startDateTime,
            "endTime" => $this->consultationSessionTwo->endDateTime,
            "media" => $this->consultationSessionTwo->media,
            "address" => $this->consultationSessionTwo->address,
            "hasParticipantFeedback" => false,
            "participant" => [
                "id" => $this->consultationSessionTwo->participant->id,
                'program' => [
                    "id" => $this->consultationSessionTwo->participant->program->id,
                    "name" => $this->consultationSessionTwo->participant->program->name,
                ],
            ],
            'consultant' => [
                "id" => $this->consultationSessionTwo->consultant->id,
                'personnel' => [
                    "id" => $this->consultationSessionTwo->consultant->personnel->id,
                    "name" => $this->consultationSessionTwo->consultant->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($consultationSessionTwoResponse);
    }
    public function test_showAll_minMaxStartTimeFilter()
    {
        $minStarTime = (new DateTime('first day of this month'))->setTime(00, 00, 00)->format('Y-m-d H:i:s');
        $maxStarTime = (new DateTime('last day of this month'))->setTime(23, 59, 59)->format('Y-m-d H:i:s');
        
        $uri = $this->consultationSessionUri
                . "?minStartTime={$minStarTime}"
                . "&maxStartTime={$maxStarTime}";
        $this->get($uri, $this->user->token)
                ->seeStatusCode(200);
        $totalResponse = ['total' => 1];
        $response = [
            'id' => $this->consultationSessionOne->id,
        ];
        $this->seeJsonContains($response);
    }
}
