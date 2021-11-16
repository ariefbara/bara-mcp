<?php

namespace Tests\Controllers\Personnel;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfMentorReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class BookedMentoringControllerTest extends MentorTestCase
{
    protected $clientParticipantOne;
    protected $stringField;
    protected $bookedMentoringSlotOne;
    protected $mentorReport;
    protected $stringFieldRecord;
    protected $submitReportRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentorReport')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        
        $program = $this->mentorOne->program;
        $firm = $program->firm;
        
        $formOne = new RecordOfForm('1');
        $this->stringField = new RecordOfStringField($formOne, '1');
        $consultantFeedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        $consultationSetup = new RecordOfConsultationSetup($program, null, $consultantFeedbackFormOne, '1');
        $mentoringSlotOne = new RecordOfMentoringSlot($this->mentorOne, $consultationSetup, '1');
        $mentoringOne = new RecordOfMentoring('1');
        $participantOne = new RecordOfParticipant($program, '1');
        $this->bookedMentoringSlotOne = new RecordOfBookedMentoringSlot($mentoringSlotOne, $mentoringOne, $participantOne);
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $this->mentorReport = new RecordOfMentorReport($mentoringOne, $formRecordOne, '1');
        
        $this->stringFieldRecord = new RecordOfStringFieldRecord($formRecordOne, $this->stringField, '1');

        $clientOne = new RecordOfClient($firm, '1');        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $this->submitReportRequest = [
            'participantRating' => 523,
            'stringFieldRecords' => [
                [
                    'fieldId' => $this->stringField->id,
                    'value' => 'new string field record value',
                ],
            ],
            'integerFieldRecords' => [],
            'textAreaFieldRecords' => [],
            'attachmentFieldRecords' => [],
            'singleSelectFieldRecords' => [],
            'multiSelectFieldRecords' => [],
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('BookedMentoringSlot')->truncate();
        $this->connection->table('MentorReport')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
    }
    
    protected function cancel()
    {
        $this->mentorOne->program->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->consultantFeedbackForm->insert($this->connection);
        $this->stringField->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->insert($this->connection);
        
        $this->bookedMentoringSlotOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/program-consultation/{$this->mentorOne->id}/booked-mentorings/{$this->bookedMentoringSlotOne->id}";
        $this->delete($uri, [], $this->mentorOne->personnel->token);
    }
    public function test_cancel_200()
    {
        $this->cancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->bookedMentoringSlotOne->id,
            'cancelled' => true,
        ];
        $this->seeJsonContains($response);
        
        $bookedMentoringRecord = [
            'id' => $this->bookedMentoringSlotOne->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('BookedMentoringSlot', $bookedMentoringRecord);
    }
    public function test_cancel_notAnUpcomingMentorin_403()
    {
        $this->bookedMentoringSlotOne->mentoringSlot->startTime = new DateTimeImmutable('-24 hours');
        $this->bookedMentoringSlotOne->mentoringSlot->endTime = new DateTimeImmutable('-23 hours');
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_unamangedBookedMentoring_cancelled_403()
    {
        $this->bookedMentoringSlotOne->cancelled = true;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_unamangedBookedMentoring_belongsToOtherMentor_403()
    {
        $program = $this->mentorOne->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $personnel->insert($this->connection);
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        $mentor->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultant = $mentor;
        
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_inactiveMentor_403()
    {
        $this->mentorOne->active = false;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->mentorOne->program->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->consultantFeedbackForm->insert($this->connection);
        $this->stringField->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->insert($this->connection);
        
        $this->bookedMentoringSlotOne->insert($this->connection);
        $this->mentorReport->insert($this->connection);
        $this->stringFieldRecord->insert($this->connection);
        
        $uri = $this->personnelUri . "/program-consultation/{$this->mentorOne->id}/booked-mentorings/{$this->bookedMentoringSlotOne->id}";
        $this->get($uri, $this->mentorOne->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->bookedMentoringSlotOne->id,
            'cancelled' => $this->bookedMentoringSlotOne->cancelled,
            'participant' => [
                'id' => $this->bookedMentoringSlotOne->participant->id,
                'name' => $this->clientParticipantOne->client->getFullName(),
            ],
            'mentorReport' => [
                'id' => $this->mentorReport->id,
                'participantRating' => $this->mentorReport->participantRating,
                'submitTime' => $this->mentorReport->formRecord->submitTime,
                'stringFieldRecords' => [
                    [
                        'id' => $this->stringFieldRecord->id,
                        'value' => $this->stringFieldRecord->value,
                        'stringField' => [
                            'id' => $this->stringFieldRecord->stringField->id,
                            'name' => $this->stringFieldRecord->stringField->name,
                            'position' => $this->stringFieldRecord->stringField->position,
                        ],
                    ],
                ],
                'integerFieldRecords' => [],
                'textAreaFieldRecords' => [],
                'attachmentFieldRecords' => [],
                'singleSelectFieldRecords' => [],
                'multiSelectFieldRecords' => [],
            ],
            'participantReport' => null,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function submitReport()
    {
        $this->mentorOne->program->insert($this->connection);
        $this->mentorOne->insert($this->connection);
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->consultantFeedbackForm->insert($this->connection);
        $this->stringField->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/program-consultation/{$this->mentorOne->id}/booked-mentorings/{$this->bookedMentoringSlotOne->id}/submit-report";
        $this->put($uri, $this->submitReportRequest, $this->mentorOne->personnel->token);
    }
    public function test_submitReport_200()
    {
        $this->bookedMentoringSlotOne->mentoringSlot->startTime = new DateTimeImmutable('-24 hours');
        $this->bookedMentoringSlotOne->mentoringSlot->endTime = new DateTimeImmutable('-23 hours');
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $mentorReportResponse = [
            'participantRating' => $this->submitReportRequest['participantRating'],
            'submitTime' => $this->currentTimeString(),
        ];
        $this->seeJsonContains($mentorReportResponse);
        
        $stringFieldRecordResponse= [
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
            'stringField' => [
                'id' => $this->stringField->id,
                'name' => $this->stringField->name,
                'position' => $this->stringField->position,
            ],
        ];
        $this->seeJsonContains($stringFieldRecordResponse);
        
        $mentorReportRecord = [
            'participantRating' => $this->submitReportRequest['participantRating'],
        ];
        $this->seeInDatabase('MentorReport', $mentorReportRecord);
        
        $formRecordRecord = [
            'submitTime' => $this->currentTimeString(),
        ];
        $this->seeInDatabase('FormRecord', $formRecordRecord);
        
        $stringFieldRecordRecord = [
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
            'StringField_id' => $this->stringField->id,
        ];
    }
    public function test_submitReport_alreadySubmittedReport_updatePreviousReport_200()
    {
        $this->bookedMentoringSlotOne->mentoringSlot->startTime = new DateTimeImmutable('-24 hours');
        $this->bookedMentoringSlotOne->mentoringSlot->endTime = new DateTimeImmutable('-23 hours');
        
        $this->mentorReport->insert($this->connection);
        $this->stringFieldRecord->insert($this->connection);
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $mentorReportResponse = [
            'id' => $this->mentorReport->id,
            'participantRating' => $this->submitReportRequest['participantRating'],
        ];
        $this->seeJsonContains($mentorReportResponse);
        
        $stringFieldRecordResponse= [
            'id' => $this->stringFieldRecord->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
            'stringField' => [
                'id' => $this->stringField->id,
                'name' => $this->stringField->name,
                'position' => $this->stringField->position,
            ],
        ];
        $this->seeJsonContains($stringFieldRecordResponse);
        
        $mentorReportRecord = [
            'id' => $this->mentorReport->id,
            'participantRating' => $this->submitReportRequest['participantRating'],
        ];
        $this->seeInDatabase('MentorReport', $mentorReportRecord);
        
        $stringFieldRecordRecord = [
            'id' => $this->stringFieldRecord->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
            'StringField_id' => $this->stringField->id,
        ];
    }
    public function test_submitReport_notAPastMentoring_403()
    {
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_unamanagedBooking_cancelled_403()
    {
        $this->bookedMentoringSlotOne->cancelled = true;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_unamanagedBooking_belongsToOtherMentor_403()
    {
        $program = $this->mentorOne->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $personnel->insert($this->connection);
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        $mentor->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultant = $mentor;
        
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_inactiveMentor_403()
    {
        $this->mentorOne->active = false;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
}
