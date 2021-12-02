<?php

namespace Tests\Controllers\Personnel;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDeclaredMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfMentorReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class MentoringContollerTest extends PersonnelTestCase
{
    protected $showAllUri;
    
    protected $clientParticipantOne;
    protected $teamParticipantTwo;

    protected $consultantOne;
    protected $consultantTwo;
    protected $consultantThree_otherPersonnel;
    
    protected $mentoringSlot_11_c1;
    protected $mentoringSlot_21_c2;
    protected $mentoringSlot_31_c3;
    
    protected $mentoringRequest_11_c1;
    protected $mentoringRequest_21_c2;
    protected $mentoringRequest_31_c3;
    
    protected $negotiatedMentoring_11_mr11;

    protected $bookedMentoringSlot_111_ms11;
    protected $bookedMentoringSlot_112_ms11;
    
    protected $declaredMentoring_11_c1;
    protected $declaredMentoring_21_c2;
    protected $declaredMentoring_31_c3;

    protected $mentorReport_111_bms111;
    protected $mentorReport_112_bms112;
    protected $mentorReport_nm11;
    protected $mentorReport_dm11;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentorReport')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        
        $this->showAllUri = $this->personnelUri . "/mentorings";
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');
        
        $otherPersonnel = new RecordOfPersonnel($firm, 'other');
        
        $this->consultantOne = new RecordOfConsultant($programOne, $this->personnel, '1');
        $this->consultantTwo = new RecordOfConsultant($programTwo, $this->personnel, '2');
        $this->consultantThree_otherPersonnel = new RecordOfConsultant($programOne, $otherPersonnel, '3');
        
        $clientOne = new RecordOfClient($firm, '1');
        $teamOne = new RecordOfTeam($firm, $clientOne, '1');
        
        $participantOne = new RecordOfParticipant($programOne, '1');
        $participantTwo = new RecordOfParticipant($programTwo, '2');
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $formOne = new RecordOfForm('1');
        $feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        
        $consultationSetupOne = new RecordOfConsultationSetup($programOne, $feedbackFormOne, $feedbackFormOne, '1');
        $consultationSetupTwo = new RecordOfConsultationSetup($programTwo, $feedbackFormOne, $feedbackFormOne, '2');
        
        $this->mentoringSlot_11_c1 = new RecordOfMentoringSlot($this->consultantOne, $consultationSetupOne, '11');
        $this->mentoringSlot_21_c2 = new RecordOfMentoringSlot($this->consultantTwo, $consultationSetupTwo, '21');
        $this->mentoringSlot_31_c3 = new RecordOfMentoringSlot($this->consultantThree_otherPersonnel, $consultationSetupOne, '31');
        
        $this->mentoringRequest_11_c1 = new RecordOfMentoringRequest($participantOne, $this->consultantOne, $consultationSetupOne, '11');
        $this->mentoringRequest_11_c1->requestStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $this->mentoringRequest_21_c2 = new RecordOfMentoringRequest($participantTwo, $this->consultantTwo, $consultationSetupTwo, '21');
        $this->mentoringRequest_31_c3 = new RecordOfMentoringRequest($participantOne, $this->consultantThree_otherPersonnel, $consultationSetupOne, '31');
        
        $mentoringOne = new RecordOfMentoring('1');
        $mentoringTwo = new RecordOfMentoring('2');
        $mentoringThree = new RecordOfMentoring('3');
        $mentoring_dm11 = new RecordOfMentoring('dm11');
        $mentoring_dm21 = new RecordOfMentoring('dm21');
        $mentoring_dm31 = new RecordOfMentoring('dm31');
        
        $formRecordOne = new RecordOfFormRecord($formOne, 'bms111');
        $formRecordTwo = new RecordOfFormRecord($formOne, 'bms112');
        $formRecordThree = new RecordOfFormRecord($formOne, 'nm11');
        $formRecord_mr_dm11 = new RecordOfFormRecord($formOne, 'mr-dm-11');
        
        $this->negotiatedMentoring_11_mr11 = new RecordOfNegotiatedMentoring($this->mentoringRequest_11_c1, $mentoringThree);
        
        $this->bookedMentoringSlot_111_ms11 = new RecordOfBookedMentoringSlot($this->mentoringSlot_11_c1, $mentoringOne, $participantOne);
        $this->bookedMentoringSlot_112_ms11 = new RecordOfBookedMentoringSlot($this->mentoringSlot_11_c1, $mentoringTwo, $participantTwo);
        
        $this->declaredMentoring_11_c1 = new RecordOfDeclaredMentoring($this->consultantOne, $participantOne, $consultationSetupOne, $mentoring_dm11);
        $this->declaredMentoring_21_c2 = new RecordOfDeclaredMentoring($this->consultantTwo, $participantTwo, $consultationSetupTwo, $mentoring_dm21);
        $this->declaredMentoring_21_c2->declaredStatus = DeclaredMentoringStatus::DENIED_BY_MENTOR;
        $this->declaredMentoring_31_c3 = new RecordOfDeclaredMentoring($this->consultantThree_otherPersonnel, $participantOne, $consultationSetupOne, $mentoring_dm31);
        
        $this->mentorReport_111_bms111 = new RecordOfMentorReport($mentoringOne, $formRecordOne, 'bms111');
        $this->mentorReport_112_bms112 = new RecordOfMentorReport($mentoringTwo, $formRecordTwo, 'bms112');
        $this->mentorReport_nm11 = new RecordOfMentorReport($mentoringThree, $formRecordThree, 'nm11');
        $this->mentorReport_dm11 = new RecordOfMentorReport($mentoring_dm11, $formRecord_mr_dm11, 'mr-dm-11');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentorReport')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
    }
    
    protected function showAll()
    {
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
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
        
        $this->bookedMentoringSlot_111_ms11->insert($this->connection);
        $this->bookedMentoringSlot_112_ms11->insert($this->connection);
        
        $this->mentoringRequest_11_c1->insert($this->connection);
        $this->mentoringRequest_21_c2->insert($this->connection);
        $this->mentoringRequest_31_c3->insert($this->connection);
        
        $this->negotiatedMentoring_11_mr11->insert($this->connection);
        
        $this->declaredMentoring_11_c1->insert($this->connection);
        $this->declaredMentoring_21_c2->insert($this->connection);
        $this->declaredMentoring_31_c3->insert($this->connection);
        
        $this->mentorReport_111_bms111->insert($this->connection);
        $this->mentorReport_112_bms112->insert($this->connection);
        $this->mentorReport_nm11->insert($this->connection);
        $this->mentorReport_dm11->insert($this->connection);
        
        $this->get($this->showAllUri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => '6',
            'list' => [
                [
                    'startTime' => $this->mentoringSlot_11_c1->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_11_c1->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_11_c1->consultant->id,
                    'programId' => $this->mentoringSlot_11_c1->consultant->program->id,
                    'programName' => $this->mentoringSlot_11_c1->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_11_c1->consultationSetup->name,
                    'mentoringSlotId' => $this->mentoringSlot_11_c1->id,
                    'mentoringSlotCapacity' => strval($this->mentoringSlot_11_c1->capacity),
                    'bookedMentoringCount' => '2',
                    'mentoringSlotSubmittedReportCount' => '2',
                    'mentoringSlotCancelledStatus' => '0',
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorReportId' => null,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantId' => null,
                    'participantName' => null,
                ],
                [
                    'startTime' => $this->mentoringSlot_21_c2->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_21_c2->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_21_c2->consultant->id,
                    'programId' => $this->mentoringSlot_21_c2->consultant->program->id,
                    'programName' => $this->mentoringSlot_21_c2->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_21_c2->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_21_c2->consultationSetup->name,
                    'mentoringSlotId' => $this->mentoringSlot_21_c2->id,
                    'mentoringSlotCapacity' => strval($this->mentoringSlot_21_c2->capacity),
                    'bookedMentoringCount' => null,
                    'mentoringSlotSubmittedReportCount' => null,
                    'mentoringSlotCancelledStatus' => '0',
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorReportId' => null,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantId' => null,
                    'participantName' => null,
                ],
                [
                    'startTime' => $this->mentoringRequest_11_c1->startTime,
                    'endTime' => $this->mentoringRequest_11_c1->endTime,
                    'mentorId' => $this->mentoringRequest_11_c1->mentor->id,
                    'programId' => $this->mentoringRequest_11_c1->mentor->program->id,
                    'programName' => $this->mentoringRequest_11_c1->mentor->program->name,
                    'consultationSetupId' => $this->mentoringRequest_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequest_11_c1->consultationSetup->name,
                    'mentoringSlotId' => null,
                    'mentoringSlotCapacity' => null,
                    'bookedMentoringCount' => null,
                    'mentoringSlotSubmittedReportCount' => null,
                    'mentoringSlotCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequest_11_c1->id,
                    'negotiatedMentoringId' => $this->negotiatedMentoring_11_mr11->id,
                    'mentorReportId' => $this->mentorReport_nm11->id,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequest_11_c1->requestStatus],
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantId' => $this->mentoringRequest_11_c1->participant->id,
                    'participantName' => $this->clientParticipantOne->client->getFullName(),
                ],
                [
                    'startTime' => $this->mentoringRequest_21_c2->startTime,
                    'endTime' => $this->mentoringRequest_21_c2->endTime,
                    'mentorId' => $this->mentoringRequest_21_c2->mentor->id,
                    'programId' => $this->mentoringRequest_21_c2->mentor->program->id,
                    'programName' => $this->mentoringRequest_21_c2->mentor->program->name,
                    'consultationSetupId' => $this->mentoringRequest_21_c2->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringRequest_21_c2->consultationSetup->name,
                    'mentoringSlotId' => null,
                    'mentoringSlotCapacity' => null,
                    'bookedMentoringCount' => null,
                    'mentoringSlotSubmittedReportCount' => null,
                    'mentoringSlotCancelledStatus' => null,
                    'mentoringRequestId' => $this->mentoringRequest_21_c2->id,
                    'negotiatedMentoringId' => null,
                    'mentorReportId' => null,
                    'mentoringRequestStatus' => MentoringRequestStatus::DISPLAY_VALUE[$this->mentoringRequest_21_c2->requestStatus],
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantId' => $this->mentoringRequest_21_c2->participant->id,
                    'participantName' => $this->teamParticipantTwo->team->name,
                ],
                [
                    'startTime' => $this->declaredMentoring_11_c1->startTime,
                    'endTime' => $this->declaredMentoring_11_c1->endTime,
                    'mentorId' => $this->declaredMentoring_11_c1->mentor->id,
                    'programId' => $this->declaredMentoring_11_c1->mentor->program->id,
                    'programName' => $this->declaredMentoring_11_c1->mentor->program->name,
                    'consultationSetupId' => $this->declaredMentoring_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->declaredMentoring_11_c1->consultationSetup->name,
                    'mentoringSlotId' => null,
                    'mentoringSlotCapacity' => null,
                    'bookedMentoringCount' => null,
                    'mentoringSlotSubmittedReportCount' => null,
                    'mentoringSlotCancelledStatus' => null,
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorReportId' => $this->mentorReport_dm11->id,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => $this->declaredMentoring_11_c1->id,
                    'declaredMentoringStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[$this->declaredMentoring_11_c1->declaredStatus],
                    'participantId' => $this->declaredMentoring_11_c1->participant->id,
                    'participantName' => $this->clientParticipantOne->client->getFullName(),
                ],
                [
                    'startTime' => $this->declaredMentoring_21_c2->startTime,
                    'endTime' => $this->declaredMentoring_21_c2->endTime,
                    'mentorId' => $this->declaredMentoring_21_c2->mentor->id,
                    'programId' => $this->declaredMentoring_21_c2->mentor->program->id,
                    'programName' => $this->declaredMentoring_21_c2->mentor->program->name,
                    'consultationSetupId' => $this->declaredMentoring_21_c2->consultationSetup->id,
                    'consultationSetupName' => $this->declaredMentoring_21_c2->consultationSetup->name,
                    'mentoringSlotId' => null,
                    'mentoringSlotCapacity' => null,
                    'bookedMentoringCount' => null,
                    'mentoringSlotSubmittedReportCount' => null,
                    'mentoringSlotCancelledStatus' => null,
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorReportId' => null,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => $this->declaredMentoring_21_c2->id,
                    'declaredMentoringStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[$this->declaredMentoring_21_c2->declaredStatus],
                    'participantId' => $this->declaredMentoring_21_c2->participant->id,
                    'participantName' => $this->teamParticipantTwo->team->name,
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
            'total' => '6',
            'list' => [
                [
                    'startTime' => $this->mentoringSlot_11_c1->startTime->format('Y-m-d H:i:s'),
                    'endTime' => $this->mentoringSlot_11_c1->endTime->format('Y-m-d H:i:s'),
                    'mentorId' => $this->mentoringSlot_11_c1->consultant->id,
                    'programId' => $this->mentoringSlot_11_c1->consultant->program->id,
                    'programName' => $this->mentoringSlot_11_c1->consultant->program->name,
                    'consultationSetupId' => $this->mentoringSlot_11_c1->consultationSetup->id,
                    'consultationSetupName' => $this->mentoringSlot_11_c1->consultationSetup->name,
                    'mentoringSlotId' => $this->mentoringSlot_11_c1->id,
                    'mentoringSlotCapacity' => strval($this->mentoringSlot_11_c1->capacity),
                    'bookedMentoringCount' => '2',
                    'mentoringSlotSubmittedReportCount' => '2',
                    'mentoringSlotCancelledStatus' => '0',
                    'mentoringRequestId' => null,
                    'negotiatedMentoringId' => null,
                    'mentorReportId' => null,
                    'mentoringRequestStatus' => null,
                    'declaredMentoringId' => null,
                    'declaredMentoringStatus' => null,
                    'participantId' => null,
                    'participantName' => null,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_fromFilter_200()
    {
        $this->mentoringSlot_11_c1->startTime = (new DateTimeImmutable('+300 hours'));
        $this->mentoringSlot_11_c1->endTime = (new DateTimeImmutable('+302 hours'));
        $this->mentoringRequest_11_c1->startTime = (new DateTimeImmutable('+350 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequest_11_c1->endTime = (new DateTimeImmutable('+351 hours'))->format('Y-m-d H:i:s');
        $this->declaredMentoring_11_c1->startTime = (new DateTimeImmutable('+400 hours'))->format('Y-m-d H:i:s');
        $this->declaredMentoring_11_c1->endTime = (new DateTimeImmutable('+401 hours'))->format('Y-m-d H:i:s');
        
        $from = (new \DateTimeImmutable('+250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?from={$from}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        $totalResponse = ['total' => '3'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
    }
    public function test_showAll_toFilter_200()
    {
        $this->mentoringSlot_11_c1->startTime = (new DateTimeImmutable('-302 hours'));
        $this->mentoringSlot_11_c1->endTime = (new DateTimeImmutable('-300 hours'));
        $this->mentoringRequest_11_c1->startTime = (new DateTimeImmutable('-351 hours'))->format('Y-m-d H:i:s');
        $this->mentoringRequest_11_c1->endTime = (new DateTimeImmutable('-350 hours'))->format('Y-m-d H:i:s');
        $this->declaredMentoring_11_c1->startTime = (new DateTimeImmutable('-401 hours'))->format('Y-m-d H:i:s');
        $this->declaredMentoring_11_c1->endTime = (new DateTimeImmutable('-400 hours'))->format('Y-m-d H:i:s');
        
        $to = (new \DateTimeImmutable('-250 hours'))->format('Y-m-d H:i:s');
        $this->showAllUri .= "?to={$to}";
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => '3'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
    }
    public function test_showAll_mentoringSlotsCancelledFilter_200()
    {
        $this->mentoringSlot_11_c1->cancelled = true;
        
        $this->showAllUri .= "?mentoringSlotFilter[cancelledStatus]=true";
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => '5'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        $mentoringRequestTwoResponse = ['mentoringRequestId' => $this->mentoringRequest_21_c2->id];
        $this->seeJsonContains($mentoringRequestTwoResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
        $declaredMentoringTwoResponse = ['declaredMentoringId' => $this->declaredMentoring_21_c2->id];
        $this->seeJsonContains($declaredMentoringTwoResponse);
    }
    public function test_showAll_mentoringSlotsBookingAvailableStatusFilter_200()
    {
        $this->mentoringSlot_11_c1->capacity = 2;
        $this->showAllUri .= "?mentoringSlotFilter[bookingAvailableStatus]=false";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => '5'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        $mentoringRequestTwoResponse = ['mentoringRequestId' => $this->mentoringRequest_21_c2->id];
        $this->seeJsonContains($mentoringRequestTwoResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
        $declaredMentoringTwoResponse = ['declaredMentoringId' => $this->declaredMentoring_21_c2->id];
        $this->seeJsonContains($declaredMentoringTwoResponse);
    }
    public function test_showAll_mentoringSlotsReportCompletedStatusFilter_200()
    {
        $this->showAllUri .= "?mentoringSlotFilter[reportCompletedStatus]=true";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => '5'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        $mentoringRequestTwoResponse = ['mentoringRequestId' => $this->mentoringRequest_21_c2->id];
        $this->seeJsonContains($mentoringRequestTwoResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
        $declaredMentoringTwoResponse = ['declaredMentoringId' => $this->declaredMentoring_21_c2->id];
        $this->seeJsonContains($declaredMentoringTwoResponse);
    }
    public function test_showAll_mentoringRequestsStatusListFilter_200()
    {
        $approvedStatus = MentoringRequestStatus::APPROVED_BY_MENTOR;
        $acceptedStatus = MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT;
        $this->showAllUri .= "?mentoringRequestFilter[requestStatusList][]=$approvedStatus";
        $this->showAllUri .= "&mentoringRequestFilter[requestStatusList][]=$acceptedStatus";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => '5'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        $mentoringSlotTwoResponse = ['mentoringSlotId' => $this->mentoringSlot_21_c2->id];
        $this->seeJsonContains($mentoringSlotTwoResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
        $declaredMentoringTwoResponse = ['declaredMentoringId' => $this->declaredMentoring_21_c2->id];
        $this->seeJsonContains($declaredMentoringTwoResponse);
    }
    public function test_showAll_mentoringRequestReportCompletedStatusFilter_200()
    {
        $this->showAllUri .= "?mentoringRequestFilter[reportCompletedStatus]=true";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => '5'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        $mentoringSlotTwoResponse = ['mentoringSlotId' => $this->mentoringSlot_21_c2->id];
        $this->seeJsonContains($mentoringSlotTwoResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
        $declaredMentoringTwoResponse = ['declaredMentoringId' => $this->declaredMentoring_21_c2->id];
        $this->seeJsonContains($declaredMentoringTwoResponse);
    }
    public function test_showAll_declaredMentoringDeclaredStatusListFilter_200()
    {
        $declaredByMentorStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $declaredByParticipantStatus = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $this->showAllUri .= "?declaredMentoringFilter[declaredStatusList][]=$declaredByMentorStatus";
        $this->showAllUri .= "&declaredMentoringFilter[declaredStatusList][]=$declaredByParticipantStatus";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => '5'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        $mentoringSlotTwoResponse = ['mentoringSlotId' => $this->mentoringSlot_21_c2->id];
        $this->seeJsonContains($mentoringSlotTwoResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        $mentoringRequestTwoResponse = ['mentoringRequestId' => $this->mentoringRequest_21_c2->id];
        $this->seeJsonContains($mentoringRequestTwoResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
    }
    public function test_showAll_declaredMentoringReportCompletedStatusFilter_200()
    {
        $this->showAllUri .= "?declaredMentoringFilter[reportCompletedStatus]=true";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => '5'];
        $this->seeJsonContains($totalResponse);
        
        $mentoringSlotOneResponse = ['mentoringSlotId' => $this->mentoringSlot_11_c1->id];
        $this->seeJsonContains($mentoringSlotOneResponse);
        $mentoringSlotTwoResponse = ['mentoringSlotId' => $this->mentoringSlot_21_c2->id];
        $this->seeJsonContains($mentoringSlotTwoResponse);
        
        $mentoringRequestOneResponse = ['mentoringRequestId' => $this->mentoringRequest_11_c1->id];
        $this->seeJsonContains($mentoringRequestOneResponse);
        $mentoringRequestTwoResponse = ['mentoringRequestId' => $this->mentoringRequest_21_c2->id];
        $this->seeJsonContains($mentoringRequestTwoResponse);
        
        $declaredMentoringOneResponse = ['declaredMentoringId' => $this->declaredMentoring_11_c1->id];
        $this->seeJsonContains($declaredMentoringOneResponse);
var_dump($this->response->content());
    }
    
}
