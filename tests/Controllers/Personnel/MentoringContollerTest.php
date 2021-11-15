<?php

namespace Tests\Controllers\Personnel;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class MentoringContollerTest extends PersonnelTestCase
{
    protected $showAllUri;

    protected $consultantOne;
    protected $consultantTwo;
    protected $consultantThree_otherPersonnel;
    
    protected $mentoringSlot_11_c1;
    protected $mentoringSlot_21_c2;
    protected $mentoringSlot_31_c3;
    
    protected $bookedMentoringSlot_111_ms11;
    protected $bookedMentoringSlot_112_ms11;
    
    protected $mentorReport_111_bms111;
    protected $mentorReport_112_bms112;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        
        $this->showAllUri = $this->personnelUri . "/mentorings";
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        
        $otherPersonnel = new RecordOfPersonnel($firm, 'other');
        
        $this->consultantOne = new RecordOfConsultant($programOne, $this->personnel, '1');
        $this->consultantTwo = new RecordOfConsultant($programTwo, $this->personnel, '2');
        $this->consultantThree_otherPersonnel = new RecordOfConsultant($programOne, $otherPersonnel, '3');
        
        $consultationSetupOne = new RecordOfConsultationSetup($programOne, null, null, '1');
        $consultationSetupTwo = new RecordOfConsultationSetup($programTwo, null, null, '2');
        
        $this->mentoringSlot_11_c1 = new RecordOfMentoringSlot($this->consultantOne, $consultationSetupOne, '11');
        $this->mentoringSlot_21_c2 = new RecordOfMentoringSlot($this->consultantTwo, $consultationSetupTwo, '21');
        $this->mentoringSlot_31_c3 = new RecordOfMentoringSlot($this->consultantThree_otherPersonnel, $consultationSetupOne, '31');
        
        $mentoringOne = new RecordOfMentoring('1');
        $mentoringTwo = new RecordOfMentoring('2');
        
        $participantOne = new RecordOfParticipant($programOne, '1');
        $participantTwo = new RecordOfParticipant($programOne, '2');
        
        $this->bookedMentoringSlot_111_ms11 = new RecordOfBookedMentoringSlot($this->mentoringSlot_11_c1, $mentoringOne, $participantOne);
        $this->bookedMentoringSlot_112_ms11 = new RecordOfBookedMentoringSlot($this->mentoringSlot_11_c1, $mentoringTwo, $participantTwo);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
    }
    
    protected function showAll()
    {
        
        $this->consultantOne->program->insert($this->connection);
        $this->consultantTwo->program->insert($this->connection);
        
        $this->consultantThree_otherPersonnel->personnel->insert($this->connection);
        
        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        $this->consultantThree_otherPersonnel->insert($this->connection);
        
        $this->mentoringSlot_11_c1->consultationSetup->insert($this->connection);
        $this->mentoringSlot_21_c2->consultationSetup->insert($this->connection);
        
        $this->mentoringSlot_11_c1->insert($this->connection);
        $this->mentoringSlot_21_c2->insert($this->connection);
        $this->mentoringSlot_31_c3->insert($this->connection);
        
        $this->bookedMentoringSlot_111_ms11->participant->insert($this->connection);
        $this->bookedMentoringSlot_112_ms11->participant->insert($this->connection);
        
        $this->bookedMentoringSlot_111_ms11->insert($this->connection);
        $this->bookedMentoringSlot_112_ms11->insert($this->connection);
        
        $this->get($this->showAllUri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '2',
            'list' => [
                [
                    'mentoringSlotId' => $this->mentoringSlot_11_c1->id,
                    'bookedSlotCount' => '2',
                    'capacity' => strval($this->mentoringSlot_11_c1->capacity),
                    'submittedReportCount' => '0',
                    'cancelled' => '0',
                    'startTime' => $this->mentoringSlot_11_c1->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_11_c1->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_11_c1->consultant->id,
                    'programId' => $this->mentoringSlot_11_c1->consultant->program->id,
                    'programName' => $this->mentoringSlot_11_c1->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_11_c1->consultationSetup->name,
                ],
                [
                    'mentoringSlotId' => $this->mentoringSlot_21_c2->id,
                    'bookedSlotCount' => null,
                    'capacity' => strval($this->mentoringSlot_21_c2->capacity),
                    'submittedReportCount' => null,
                    'cancelled' => '0',
                    'startTime' => $this->mentoringSlot_21_c2->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_21_c2->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_21_c2->consultant->id,
                    'programId' => $this->mentoringSlot_21_c2->consultant->program->id,
                    'programName' => $this->mentoringSlot_21_c2->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_21_c2->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_21_c2->consultationSetup->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_order_200()
    {
        $this->mentoringSlot_11_c1->startTime = (new DateTimeImmutable('+300 hours'));
        $this->mentoringSlot_11_c1->endTime = (new DateTimeImmutable('+302 hours'));
        
        $from = (new \DateTimeImmutable('+250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?order=DESC&pageSize=1";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '2',
            'list' => [
                [
                    'mentoringSlotId' => $this->mentoringSlot_11_c1->id,
                    'bookedSlotCount' => '2',
                    'capacity' => strval($this->mentoringSlot_11_c1->capacity),
                    'submittedReportCount' => '0',
                    'cancelled' => '0',
                    'startTime' => $this->mentoringSlot_11_c1->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_11_c1->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_11_c1->consultant->id,
                    'programId' => $this->mentoringSlot_11_c1->consultant->program->id,
                    'programName' => $this->mentoringSlot_11_c1->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_11_c1->consultationSetup->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_fromFilter_200()
    {
        $this->mentoringSlot_11_c1->startTime = (new DateTimeImmutable('+300 hours'));
        $this->mentoringSlot_11_c1->endTime = (new DateTimeImmutable('+302 hours'));
        
        $from = (new \DateTimeImmutable('+250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?from={$from}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '1',
            'list' => [
                [
                    'mentoringSlotId' => $this->mentoringSlot_11_c1->id,
                    'bookedSlotCount' => '2',
                    'capacity' => strval($this->mentoringSlot_11_c1->capacity),
                    'submittedReportCount' => '0',
                    'cancelled' => '0',
                    'startTime' => $this->mentoringSlot_11_c1->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_11_c1->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_11_c1->consultant->id,
                    'programId' => $this->mentoringSlot_11_c1->consultant->program->id,
                    'programName' => $this->mentoringSlot_11_c1->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_11_c1->consultationSetup->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_toFilter_200()
    {
        $this->mentoringSlot_11_c1->startTime = (new DateTimeImmutable('-302 hours'));
        $this->mentoringSlot_11_c1->endTime = (new DateTimeImmutable('-300 hours'));
        
        $to = (new \DateTimeImmutable('-250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?to={$to}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '1',
            'list' => [
                [
                    'mentoringSlotId' => $this->mentoringSlot_11_c1->id,
                    'bookedSlotCount' => '2',
                    'capacity' => strval($this->mentoringSlot_11_c1->capacity),
                    'submittedReportCount' => '0',
                    'cancelled' => '0',
                    'startTime' => $this->mentoringSlot_11_c1->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_11_c1->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_11_c1->consultant->id,
                    'programId' => $this->mentoringSlot_11_c1->consultant->program->id,
                    'programName' => $this->mentoringSlot_11_c1->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_11_c1->consultationSetup->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_mentoringSlotsCancelledFilter_200()
    {
        $this->mentoringSlot_11_c1->cancelled = true;
        
        $this->showAllUri .= "?mentoringSlotFilter[cancelledStatus]=true";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '1',
            'list' => [
                [
                    'mentoringSlotId' => $this->mentoringSlot_11_c1->id,
                    'bookedSlotCount' => '2',
                    'capacity' => strval($this->mentoringSlot_11_c1->capacity),
                    'submittedReportCount' => '0',
                    'cancelled' => '1',
                    'startTime' => $this->mentoringSlot_11_c1->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_11_c1->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_11_c1->consultant->id,
                    'programId' => $this->mentoringSlot_11_c1->consultant->program->id,
                    'programName' => $this->mentoringSlot_11_c1->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_11_c1->consultationSetup->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_bookingAvailableStatusFilter_200()
    {
        $this->mentoringSlot_11_c1->capacity = 2;
        
        $this->showAllUri .= "?mentoringSlotFilter[bookingAvailableStatus]=false";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '1',
            'list' => [
                [
                    'mentoringSlotId' => $this->mentoringSlot_11_c1->id,
                    'bookedSlotCount' => '2',
                    'capacity' => strval($this->mentoringSlot_11_c1->capacity),
                    'submittedReportCount' => '0',
                    'cancelled' => '0',
                    'startTime' => $this->mentoringSlot_11_c1->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_11_c1->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_11_c1->consultant->id,
                    'programId' => $this->mentoringSlot_11_c1->consultant->program->id,
                    'programName' => $this->mentoringSlot_11_c1->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_11_c1->consultationSetup->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
