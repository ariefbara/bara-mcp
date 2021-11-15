<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\Client\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class MentoringSlotControllerTest extends ProgramParticipationTestCase
{
    protected $mentoringSlotOne;
    protected $mentoringSlotTwo;
    
    protected $bookedMentoringSlot_11_ms1;
    protected $bookedMentoringSlot_12_ms1;

    protected $showAllUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        
        $program = $this->programParticipation->participant->program;
        
        $firm = $program->firm;
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        
        $consultantOne = new RecordOfConsultant($program, $personnelOne, '1');
        
        $consultationSetupOne = new RecordOfConsultationSetup($program, null, null, '1');
        
        $this->mentoringSlotOne = new RecordOfMentoringSlot($consultantOne, $consultationSetupOne, '1');
        $this->mentoringSlotTwo = new RecordOfMentoringSlot($consultantOne, $consultationSetupOne, '2');
        
        $mentoringOne = new RecordOfMentoring('1');
        $mentoringTwo = new RecordOfMentoring('2');
        
        $participantOne = new RecordOfParticipant($program, '1');
        $participantTwo = new RecordOfParticipant($program, '2');
        
        $this->bookedMentoringSlot_11_ms1 = new RecordOfBookedMentoringSlot($this->mentoringSlotOne, $mentoringOne, $participantOne);
        $this->bookedMentoringSlot_12_ms1 = new RecordOfBookedMentoringSlot($this->mentoringSlotOne, $mentoringTwo, $participantTwo);
        
        $this->showAllUri = $this->programParticipationUri . "/{$this->programParticipation->id}/mentoring-slots";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
    }
    
    protected function show()
    {
        $this->mentoringSlotOne->consultant->personnel->insert($this->connection);
        
        $this->mentoringSlotOne->consultant->insert($this->connection);
        
        $this->mentoringSlotOne->consultationSetup->insert($this->connection);
        
        $this->mentoringSlotOne->insert($this->connection);
        
        $this->bookedMentoringSlot_11_ms1->participant->insert($this->connection);
        $this->bookedMentoringSlot_12_ms1->participant->insert($this->connection);
        
        $this->bookedMentoringSlot_11_ms1->insert($this->connection);
        $this->bookedMentoringSlot_12_ms1->insert($this->connection);
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->id}/mentoring-slots/{$this->mentoringSlotOne->id}";
        $this->get($uri, $this->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        $response = [
            'id' => $this->mentoringSlotOne->id,
            'cancelled' => $this->mentoringSlotOne->cancelled,
            'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
            'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
            'mediaType' => $this->mentoringSlotOne->mediaType,
            'location' => $this->mentoringSlotOne->location,
            'capacity' => $this->mentoringSlotOne->capacity,
            'bookedSlotCount' => 2,
            'consultant' => [
                'id' => $this->mentoringSlotOne->consultant->id,
                'personnel' => [
                    'id' => $this->mentoringSlotOne->consultant->personnel->id,
                    'name' => $this->mentoringSlotOne->consultant->personnel->getFullName(),
                ],
            ],
            'consultationSetup' => [
                'id' => $this->mentoringSlotOne->consultationSetup->id,
                'name' => $this->mentoringSlotOne->consultationSetup->name,
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function showAll()
    {
        $this->mentoringSlotOne->consultant->personnel->insert($this->connection);
        
        $this->mentoringSlotOne->consultant->insert($this->connection);
        
        $this->mentoringSlotOne->consultationSetup->insert($this->connection);
        
        $this->mentoringSlotOne->insert($this->connection);
        $this->mentoringSlotTwo->insert($this->connection);
        
        $this->bookedMentoringSlot_11_ms1->participant->insert($this->connection);
        $this->bookedMentoringSlot_12_ms1->participant->insert($this->connection);
        
        $this->bookedMentoringSlot_11_ms1->insert($this->connection);
        $this->bookedMentoringSlot_12_ms1->insert($this->connection);
        
        $this->get($this->showAllUri, $this->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->mentoringSlotOne->id,
                    'cancelled' => $this->mentoringSlotOne->cancelled,
                    'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotOne->mediaType,
                    'location' => $this->mentoringSlotOne->location,
                    'capacity' => $this->mentoringSlotOne->capacity,
                    'bookedSlotCount' => 2,
                    'consultant' => [
                        'id' => $this->mentoringSlotOne->consultant->id,
                        'personnel' => [
                            'id' => $this->mentoringSlotOne->consultant->personnel->id,
                            'name' => $this->mentoringSlotOne->consultant->personnel->getFullName(),
                        ],
                    ],
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotOne->consultationSetup->id,
                        'name' => $this->mentoringSlotOne->consultationSetup->name,
                    ],
                ],
                [
                    'id' => $this->mentoringSlotTwo->id,
                    'cancelled' => $this->mentoringSlotTwo->cancelled,
                    'startTime' => $this->mentoringSlotTwo->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotTwo->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotTwo->mediaType,
                    'location' => $this->mentoringSlotTwo->location,
                    'capacity' => $this->mentoringSlotTwo->capacity,
                    'bookedSlotCount' => 0,
                    'consultant' => [
                        'id' => $this->mentoringSlotTwo->consultant->id,
                        'personnel' => [
                            'id' => $this->mentoringSlotTwo->consultant->personnel->id,
                            'name' => $this->mentoringSlotTwo->consultant->personnel->getFullName(),
                        ],
                    ],
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotTwo->consultationSetup->id,
                        'name' => $this->mentoringSlotTwo->consultationSetup->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_fromFilter_200()
    {
        $this->mentoringSlotOne->startTime = new \DateTimeImmutable('+300 hours');
        $this->mentoringSlotOne->endTime = new \DateTimeImmutable('+305 hours');
        $from = (new \DateTimeImmutable('+250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?from={$from}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    'id' => $this->mentoringSlotOne->id,
                    'cancelled' => $this->mentoringSlotOne->cancelled,
                    'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotOne->mediaType,
                    'location' => $this->mentoringSlotOne->location,
                    'capacity' => $this->mentoringSlotOne->capacity,
                    'bookedSlotCount' => 2,
                    'consultant' => [
                        'id' => $this->mentoringSlotOne->consultant->id,
                        'personnel' => [
                            'id' => $this->mentoringSlotOne->consultant->personnel->id,
                            'name' => $this->mentoringSlotOne->consultant->personnel->getFullName(),
                        ],
                    ],
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotOne->consultationSetup->id,
                        'name' => $this->mentoringSlotOne->consultationSetup->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_toFilter_200()
    {
        $this->mentoringSlotOne->startTime = new \DateTimeImmutable('-300 hours');
        $this->mentoringSlotOne->endTime = new \DateTimeImmutable('-290 hours');
        $to = (new \DateTimeImmutable('-250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?to={$to}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    'id' => $this->mentoringSlotOne->id,
                    'cancelled' => $this->mentoringSlotOne->cancelled,
                    'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotOne->mediaType,
                    'location' => $this->mentoringSlotOne->location,
                    'capacity' => $this->mentoringSlotOne->capacity,
                    'bookedSlotCount' => 2,
                    'consultant' => [
                        'id' => $this->mentoringSlotOne->consultant->id,
                        'personnel' => [
                            'id' => $this->mentoringSlotOne->consultant->personnel->id,
                            'name' => $this->mentoringSlotOne->consultant->personnel->getFullName(),
                        ],
                    ],
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotOne->consultationSetup->id,
                        'name' => $this->mentoringSlotOne->consultationSetup->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_consultationSetupIdFilter_200()
    {
        $consultationSetupTwo = new RecordOfConsultationSetup($this->programParticipation->participant->program, null, null, '2');
        $consultationSetupTwo->insert($this->connection);
        
        $this->mentoringSlotTwo->consultationSetup = $consultationSetupTwo;
        
        $this->showAllUri .= "?consultationSetupId={$this->mentoringSlotOne->consultationSetup->id}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    'id' => $this->mentoringSlotOne->id,
                    'cancelled' => $this->mentoringSlotOne->cancelled,
                    'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotOne->mediaType,
                    'location' => $this->mentoringSlotOne->location,
                    'capacity' => $this->mentoringSlotOne->capacity,
                    'bookedSlotCount' => 2,
                    'consultant' => [
                        'id' => $this->mentoringSlotOne->consultant->id,
                        'personnel' => [
                            'id' => $this->mentoringSlotOne->consultant->personnel->id,
                            'name' => $this->mentoringSlotOne->consultant->personnel->getFullName(),
                        ],
                    ],
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotOne->consultationSetup->id,
                        'name' => $this->mentoringSlotOne->consultationSetup->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_consultantIdFilter_200()
    {
        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;
        
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        $personnelTwo->insert($this->connection);
        
        $consultantTwo = new RecordOfConsultant($this->programParticipation->participant->program, $personnelTwo, '2');
        $consultantTwo->insert($this->connection);
        
        $this->mentoringSlotTwo->consultant = $consultantTwo;
        
        $this->showAllUri .= "?consultantId={$this->mentoringSlotOne->consultant->id}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 1,
            'list' => [
                [
                    'id' => $this->mentoringSlotOne->id,
                    'cancelled' => $this->mentoringSlotOne->cancelled,
                    'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                    'mediaType' => $this->mentoringSlotOne->mediaType,
                    'location' => $this->mentoringSlotOne->location,
                    'capacity' => $this->mentoringSlotOne->capacity,
                    'bookedSlotCount' => 2,
                    'consultant' => [
                        'id' => $this->mentoringSlotOne->consultant->id,
                        'personnel' => [
                            'id' => $this->mentoringSlotOne->consultant->personnel->id,
                            'name' => $this->mentoringSlotOne->consultant->personnel->getFullName(),
                        ],
                    ],
                    'consultationSetup' => [
                        'id' => $this->mentoringSlotOne->consultationSetup->id,
                        'name' => $this->mentoringSlotOne->consultationSetup->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
