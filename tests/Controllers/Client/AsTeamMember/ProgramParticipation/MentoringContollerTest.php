<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\Client\AsTeamMember\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfParticipantReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class MentoringContollerTest extends ProgramParticipationTestCase
{
    protected $showAllUri;
    
    protected $consultantOne;
    protected $consultantTwo;
    
    protected $feedbackForm;
    protected $consultationSetupOne;
    protected $consultationSetupTwo;

    protected $otherClientParticipant;

    protected $mentoringSlot_11_c1;
    protected $mentoringSlot_21_c2;
    
    protected $bookedMentoringSlot_111_ms11;
    protected $bookedMentoringSlot_112_ms11_otherParticipant;
    protected $bookedMentoringSlot_211_ms21;
    
    protected $mentoringRequestOne;
    protected $mentoringRequestTwo;
    protected $mentoringRequest_otherParticipant;
    
    protected $negotiatedMentoringOne_mr1;

    protected $participantReport_bms111;
    protected $participantReport_nm1;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        
        $this->showAllUri = $this->programParticipationUri . "/{$this->programParticipation->id}/mentorings";
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, '1');
        $this->consultantTwo = new RecordOfConsultant($program, $personnelTwo, '2');
        
        $form = new RecordOfForm('999');
        
        $this->feedbackForm = new RecordOfFeedbackForm($firm, $form);
        
        $this->consultationSetupOne = new RecordOfConsultationSetup($program, null, $this->feedbackForm, '1');
        $this->consultationSetupTwo = new RecordOfConsultationSetup($program, null, $this->feedbackForm, '2');
        
        $this->mentoringSlot_11_c1 = new RecordOfMentoringSlot($this->consultantOne, $this->consultationSetupOne, '11');
        $this->mentoringSlot_21_c2 = new RecordOfMentoringSlot($this->consultantTwo, $this->consultationSetupTwo, '21');
        
        $mentoring_bms111 = new RecordOfMentoring('bms111');
        $mentoring_bms211 = new RecordOfMentoring('bms211');
        $mentoring_bms112 = new RecordOfMentoring('bms112');
        $mentoring_nm1 = new RecordOfMentoring('nm1');
        
        $otherParticipant = new RecordOfParticipant($program, 'other');
        $otherClient = new RecordOfClient($firm, 'other');
        $this->otherClientParticipant = new RecordOfClientParticipant($otherClient, $otherParticipant);
        
        $this->bookedMentoringSlot_111_ms11 = new RecordOfBookedMentoringSlot($this->mentoringSlot_11_c1, $mentoring_bms111, $participant);
        $this->bookedMentoringSlot_211_ms21 = new RecordOfBookedMentoringSlot($this->mentoringSlot_21_c2, $mentoring_bms211, $participant);
        $this->bookedMentoringSlot_112_ms11_otherParticipant = new RecordOfBookedMentoringSlot($this->mentoringSlot_11_c1, $mentoring_bms112, $otherParticipant);
        
        $this->mentoringRequestOne = new RecordOfMentoringRequest($participant, $this->consultantOne, $this->consultationSetupOne, '1');
        $this->mentoringRequestTwo = new RecordOfMentoringRequest($participant, $this->consultantTwo, $this->consultationSetupTwo, '2');
        $this->mentoringRequest_otherParticipant = new RecordOfMentoringRequest($otherParticipant, $this->consultantTwo, $this->consultationSetupOne, 'other');
        
        $this->negotiatedMentoringOne_mr1 = new RecordOfNegotiatedMentoring($this->mentoringRequestOne, $mentoring_nm1);
        
        $formRecord_pr_bms111 = new RecordOfFormRecord($form, 'pr_bms111');
        $formRecord_pr_nm1 = new RecordOfFormRecord($form, 'nm1');
        
        $this->participantReport_bms111 = new RecordOfParticipantReport($mentoring_bms111, $formRecord_pr_bms111, 'bms111');
        $this->participantReport_nm1 = new RecordOfParticipantReport($mentoring_nm1, $formRecord_pr_nm1, 'nm1');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
    }
    
    protected function showAll()
    {
        
        $this->consultantOne->personnel->insert($this->connection);
        $this->consultantTwo->personnel->insert($this->connection);
        
        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        
        $this->feedbackForm->insert($this->connection);
        
        $this->consultationSetupOne->insert($this->connection);
        $this->consultationSetupTwo->insert($this->connection);
        
        $this->otherClientParticipant->client->insert($this->connection);
        $this->otherClientParticipant->insert($this->connection);
        
        $this->mentoringSlot_11_c1->insert($this->connection);
        $this->mentoringSlot_21_c2->insert($this->connection);
        
        $this->bookedMentoringSlot_111_ms11->insert($this->connection);
        $this->bookedMentoringSlot_211_ms21->insert($this->connection);
        $this->bookedMentoringSlot_112_ms11_otherParticipant->insert($this->connection);
        
        $this->mentoringRequestOne->insert($this->connection);
        $this->mentoringRequestTwo->insert($this->connection);
        $this->mentoringRequest_otherParticipant->insert($this->connection);
        
        $this->negotiatedMentoringOne_mr1->insert($this->connection);
        
        $this->participantReport_bms111->insert($this->connection);
        $this->participantReport_nm1->insert($this->connection);
        
        $this->get($this->showAllUri, $this->teamMember->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '4',
            'list' => [
                [
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_111_ms11->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_111_ms11->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => $this->participantReport_bms111->id,
                ],
                [
                    'startTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_211_ms21->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_211_ms21->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => null,
                ],
                [
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'mentorId' => $this->mentoringRequestOne->mentor->id,
                    'mentorName' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestOne->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestOne->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_mr1->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
                    'participantReportId' => $this->participantReport_nm1->id,
                ],
                [
                    'startTime' => $this->mentoringRequestTwo->startTime,
                    'endTime' => $this->mentoringRequestTwo->endTime,
                    'mentorId' => $this->mentoringRequestTwo->mentor->id,
                    'mentorName' => $this->mentoringRequestTwo->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestTwo->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestTwo->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestTwo->id,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestTwo->requestStatus],
                    'participantReportId' => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_orderFilter_200()
    {
        $this->mentoringSlot_11_c1->startTime = (new DateTimeImmutable('+300 hours'));
        $this->mentoringSlot_11_c1->endTime = (new DateTimeImmutable('+302 hours'));
        
        $this->showAllUri .= "?order=DESC&pageSize=1";
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '4',
            'list' => [
                [
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_111_ms11->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_111_ms11->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => $this->participantReport_bms111->id,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_fromFilter_200()
    {
        $this->mentoringSlot_11_c1->startTime = (new DateTimeImmutable('+300 hours'));
        $this->mentoringSlot_11_c1->endTime = (new DateTimeImmutable('+302 hours'));
        
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('+350 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('+351 hours'))->format('Y-m-d H:i:s');
        
        $from = (new \DateTimeImmutable('+250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?from={$from}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '2',
            'list' => [
                [
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_111_ms11->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_111_ms11->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => $this->participantReport_bms111->id,
                ],
                [
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'mentorId' => $this->mentoringRequestOne->mentor->id,
                    'mentorName' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestOne->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestOne->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_mr1->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
                    'participantReportId' => $this->participantReport_nm1->id,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_toFilter_200()
    {
        $this->mentoringSlot_11_c1->startTime = (new DateTimeImmutable('-302 hours'));
        $this->mentoringSlot_11_c1->endTime = (new DateTimeImmutable('-300 hours'));
        
        $this->mentoringRequestOne->startTime = (new DateTimeImmutable('-351 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequestOne->endTime = (new DateTimeImmutable('-350 hours'))->format('Y-m-d H:i:s');
        
        $to = (new \DateTimeImmutable('-250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?to={$to}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '2',
            'list' => [
                [
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_111_ms11->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_111_ms11->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => $this->participantReport_bms111->id,
                ],
                [
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'mentorId' => $this->mentoringRequestOne->mentor->id,
                    'mentorName' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestOne->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestOne->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_mr1->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
                    'participantReportId' => $this->participantReport_nm1->id,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_mentoringSlotCancelledFilter_200()
    {
        $this->bookedMentoringSlot_111_ms11->cancelled = false;
        $this->bookedMentoringSlot_211_ms21->cancelled = true;
        
        $this->showAllUri .= "?mentoringSlotFilter[cancelledStatus]=false";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '3',
            'list' => [
                [
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_111_ms11->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_111_ms11->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => $this->participantReport_bms111->id,
                ],
                [
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'mentorId' => $this->mentoringRequestOne->mentor->id,
                    'mentorName' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestOne->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestOne->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_mr1->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
                    'participantReportId' => $this->participantReport_nm1->id,
                ],
                [
                    'startTime' => $this->mentoringRequestTwo->startTime,
                    'endTime' => $this->mentoringRequestTwo->endTime,
                    'mentorId' => $this->mentoringRequestTwo->mentor->id,
                    'mentorName' => $this->mentoringRequestTwo->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestTwo->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestTwo->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestTwo->id,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestTwo->requestStatus],
                    'participantReportId' => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_mentoringSlotReportCompletedStatusFilter_200()
    {
        $this->showAllUri .= "?mentoringSlotFilter[reportCompletedStatus]=true";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '3',
            'list' => [
                [
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_111_ms11->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_111_ms11->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => $this->participantReport_bms111->id,
                ],
                [
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'mentorId' => $this->mentoringRequestOne->mentor->id,
                    'mentorName' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestOne->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestOne->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_mr1->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
                    'participantReportId' => $this->participantReport_nm1->id,
                ],
                [
                    'startTime' => $this->mentoringRequestTwo->startTime,
                    'endTime' => $this->mentoringRequestTwo->endTime,
                    'mentorId' => $this->mentoringRequestTwo->mentor->id,
                    'mentorName' => $this->mentoringRequestTwo->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestTwo->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestTwo->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestTwo->id,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestTwo->requestStatus],
                    'participantReportId' => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_mentoringReportStatusListFilter_200()
    {
        $this->mentoringRequestOne->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $approvedStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $acceptedStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->showAllUri .= "?mentoringRequestFilter[requestStatusList][]=$approvedStatus";
        $this->showAllUri .= "&mentoringRequestFilter[requestStatusList][]=$acceptedStatus";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '3',
            'list' => [
                [
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_111_ms11->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_111_ms11->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => $this->participantReport_bms111->id,
                ],
                [
                    'startTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_211_ms21->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_211_ms21->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => null,
                ],
                [
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'mentorId' => $this->mentoringRequestOne->mentor->id,
                    'mentorName' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestOne->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestOne->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_mr1->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
                    'participantReportId' => $this->participantReport_nm1->id,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_mentoringReportReportCompletedStatusFilter200()
    {
        $this->showAllUri .= "?mentoringRequestFilter[reportCompletedStatus]=true";
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '3',
            'list' => [
                [
                    'startTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_111_ms11->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_111_ms11->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_111_ms11->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => $this->participantReport_bms111->id,
                ],
                [
                    'startTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->id,
                    'mentorName' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultant->personnel->getFullName(),
                    'consultationSetupId' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultationSetup->id,
                    'consultationSetupName' => $this->bookedMentoringSlot_211_ms21->mentoringSlot->consultationSetup->name,
                    'bookedMentoringId' => $this->bookedMentoringSlot_211_ms21->id,
                    'bookedMentoringCancelledStatus' => strval(intval($this->bookedMentoringSlot_211_ms21->cancelled)),
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentoringRequestStatus' => null,
                    'participantReportId' => null,
                ],
                [
                    'startTime' => $this->mentoringRequestOne->startTime,
                    'endTime' => $this->mentoringRequestOne->endTime,
                    'mentorId' => $this->mentoringRequestOne->mentor->id,
                    'mentorName' => $this->mentoringRequestOne->mentor->personnel->getFullName(),
                    'consultationSetupId' => $this->mentoringRequestOne->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequestOne->consultationSetup->name,
                    'bookedMentoringId' => null,
                    'bookedMentoringCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequestOne->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoringOne_mr1->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequestOne->requestStatus],
                    'participantReportId' => $this->participantReport_nm1->id,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
