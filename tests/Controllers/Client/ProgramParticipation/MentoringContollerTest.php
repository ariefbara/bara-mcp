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

class MentoringContollerTest extends \Tests\Controllers\Client\ProgramParticipationTestCase
{
    protected $showAllUri;

    protected $consultantOne;
    protected $consultantTwo;
    
    protected $mentoringSlot_11_c1;
    protected $mentoringSlot_21_c2;
    
    protected $bookedMentoringSlot_111_ms11;
    protected $bookedMentoringSlot_112_ms11_otherParticipant;
    protected $bookedMentoringSlot_211_ms21;
    
    protected $mentorReport_111_bms111;
    protected $mentorReport_112_bms112;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        
        $this->showAllUri = $this->programParticipationUri . "/{$this->programParticipation->id}/mentorings";
        
        $program = $this->programParticipation->participant->program;
        
        $firm = $program->firm;
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, '1');
        $this->consultantTwo = new RecordOfConsultant($program, $personnelTwo, '2');
        
        $consultationSetupOne = new RecordOfConsultationSetup($program, null, null, '1');
        $consultationSetupTwo = new RecordOfConsultationSetup($program, null, null, '2');
        
        $this->mentoringSlot_11_c1 = new RecordOfMentoringSlot($this->consultantOne, $consultationSetupOne, '11');
        $this->mentoringSlot_21_c2 = new RecordOfMentoringSlot($this->consultantTwo, $consultationSetupTwo, '21');
        
        $mentoringOne = new RecordOfMentoring('1');
        $mentoringTwo = new RecordOfMentoring('2');
        $mentoringThree = new RecordOfMentoring('3');
        
        $otherParticipant = new RecordOfParticipant($program, '99');
        
        $this->bookedMentoringSlot_111_ms11 = new RecordOfBookedMentoringSlot($this->mentoringSlot_11_c1, $mentoringOne, $this->programParticipation->participant);
        $this->bookedMentoringSlot_211_ms21 = new RecordOfBookedMentoringSlot($this->mentoringSlot_21_c2, $mentoringTwo, $this->programParticipation->participant);
        $this->bookedMentoringSlot_112_ms11_otherParticipant = new RecordOfBookedMentoringSlot($this->mentoringSlot_11_c1, $mentoringThree, $otherParticipant);
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
    
    protected function showAll()
    {
$this->disableExceptionHandling();
        $this->mentoringSlot_11_c1->consultant->personnel->insert($this->connection);
        $this->mentoringSlot_21_c2->consultant->personnel->insert($this->connection);
        
        $this->mentoringSlot_11_c1->consultant->insert($this->connection);
        $this->mentoringSlot_21_c2->consultant->insert($this->connection);
        
        $this->mentoringSlot_11_c1->consultationSetup->insert($this->connection);
        $this->mentoringSlot_21_c2->consultationSetup->insert($this->connection);
        
        $this->mentoringSlot_11_c1->insert($this->connection);
        $this->mentoringSlot_21_c2->insert($this->connection);
        
        $this->bookedMentoringSlot_112_ms11_otherParticipant->participant->insert($this->connection);
        
        $this->bookedMentoringSlot_111_ms11->insert($this->connection);
        $this->bookedMentoringSlot_211_ms21->insert($this->connection);
        $this->bookedMentoringSlot_112_ms11_otherParticipant->insert($this->connection);
        
        $this->get($this->showAllUri, $this->programParticipation->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '2',
            'list' => [
                [
                    'bookedMentoringSlotId' => $this->bookedMentoringSlot_111_ms11->mentoring->id,
                    'reportId' => null,
                    'cancelled' => '0',
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'programId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->program->id,
                    'programName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->program->name,
                    'consultationSetupId'=> $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName'=> $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                ],
                [
                    'bookedMentoringSlotId' => $this->bookedMentoringSlot_211_ms21->mentoring->id,
                    'reportId' => null,
                    'cancelled' => '0',
                    'startTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->personnel->getFullName(),
                    'programId' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->program->id,
                    'programName' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->program->name,
                    'consultationSetupId'=> $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultationSetup->id,
                    'consultationSetupName'=> $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultationSetup->name,
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
