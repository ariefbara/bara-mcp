<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\MentoringSlot\RecordOfBookedMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfMentoringSlot;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfParticipantReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class BookedMentoringSlotControllerTest extends ParticipantTestCase
{
    protected $feedbackFormOne;
    protected $stringFieldOne;

    protected $mentoringSlotOne;
    protected $mentoringSlotTwo;
    
    protected $bookedMentoringSlotOne;
    protected $bookedMentoringSlotTwo;
    
    protected $participantReportOne;
    protected $stringFieldRecordOne;
    
    protected $submitReportRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        $this->connection->table('AttachmentFieldRecord')->truncate();
        $this->connection->table('SingleSelectFieldRecord')->truncate();
        $this->connection->table('MultiSelectFieldRecord')->truncate();
        
        $this->connection->table('FeedbackForm')->truncate();
        
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentorReport')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        
        $this->connection->table('BookedMentoringSlot')->truncate();
        
        $participant = $this->clientParticipant->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $consultantOne = new RecordOfConsultant($program, $personnelOne, '1');
        $consultantTwo = new RecordOfConsultant($program, $personnelTwo, '2');
        
        $formOne = new RecordOfForm('1');
        $this->stringFieldOne = new RecordOfStringField($formOne, '1');
        $this->feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        
        $consultationSetupOne = new RecordOfConsultationSetup($program, null, null, '1');
        $consultationSetupTwo = new RecordOfConsultationSetup($program, null, null, '2');
        
        $this->mentoringSlotOne = new RecordOfMentoringSlot($consultantOne, $consultationSetupOne, '1');
        $this->mentoringSlotTwo = new RecordOfMentoringSlot($consultantTwo, $consultationSetupTwo, '2');
        
        $mentoringOne = new RecordOfMentoring('1');
        $mentoringTwo = new RecordOfMentoring('2');
        
        $this->bookedMentoringSlotOne = new RecordOfBookedMentoringSlot($this->mentoringSlotOne, $mentoringOne, $participant);
        $this->bookedMentoringSlotTwo = new RecordOfBookedMentoringSlot($this->mentoringSlotTwo, $mentoringTwo, $participant);
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $this->stringFieldRecordOne = new RecordOfStringFieldRecord($formRecordOne, $this->stringFieldOne, '1');
        $this->participantReportOne = new RecordOfParticipantReport($mentoringOne, $formRecordOne, '1');
        
        $this->submitReportRequest = [
            'mentorRating' => 9,
            'stringFieldRecords' => [
                [
                    'fieldId' => $this->stringFieldOne->id,
                    'value' => 'new string field value',
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
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        $this->connection->table('AttachmentFieldRecord')->truncate();
        $this->connection->table('SingleSelectFieldRecord')->truncate();
        $this->connection->table('MultiSelectFieldRecord')->truncate();
        
        $this->connection->table('FeedbackForm')->truncate();
        
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringSlot')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('MentorReport')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        
        $this->connection->table('BookedMentoringSlot')->truncate();
    }
    
    protected function book()
    {
        $this->clientParticipant->participant->program->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->mentoringSlotOne->consultant->personnel->insert($this->connection);
        $this->mentoringSlotOne->consultant->insert($this->connection);
        $this->mentoringSlotOne->consultationSetup->insert($this->connection);
        $this->mentoringSlotOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/mentoring-slots/{$this->mentoringSlotOne->id}/booked-mentoring-slots";
        $this->post($uri, [], $this->clientParticipant->client->token);
    }
    public function test_book_200()
    {
        $this->book();
        $this->seeStatusCode(201);
        
        $response = [
            'cancelled' => false,
            'mentoringSlot' => [
                'id' => $this->mentoringSlotOne->id,
                'cancelled' => $this->mentoringSlotOne->cancelled,
                'capacity' => $this->mentoringSlotOne->capacity,
                'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                'mediaType' => $this->mentoringSlotOne->mediaType,
                'location' => $this->mentoringSlotOne->location,
                'consultationSetup' => [
                    'id' => $this->mentoringSlotOne->consultationSetup->id,
                    'name' => $this->mentoringSlotOne->consultationSetup->name,
                    'participantFeedbackForm' => null
                ],
            ],
            'participantReport' => null,
        ];
        $this->seeJsonContains($response);
        
        $record = [
            'MentoringSlot_id' => $this->mentoringSlotOne->id,
            'cancelled' => false,
            'Participant_id' => $this->clientParticipant->participant->id,
        ];
        $this->seeInDatabase('BookedMentoringSlot', $record);
    }
    public function test_book_fullCapacity_403()
    {
        $this->mentoringSlotOne->capacity = 1;
        
        $mentoring = new RecordOfMentoring('zzz');
        $participant = new RecordOfParticipant($this->mentoringSlotOne->consultant->program, 'zzz');
        $participant->insert($this->connection);
        
        $bookedMentoringSlot = new RecordOfBookedMentoringSlot($this->mentoringSlotOne, $mentoring, $participant);
        $bookedMentoringSlot->insert($this->connection);
        
        $this->book();
        $this->seeStatusCode(403);
    }
    public function test_book_notUpcomingSchedule_403()
    {
        $this->mentoringSlotOne->startTime = new DateTimeImmutable('-24 hours');
        
        $this->book();
        $this->seeStatusCode(403);
    }
    public function test_book_alreadyBooked_403()
    {
        $this->bookedMentoringSlotOne->insert($this->connection);
        
        $this->book();
        $this->seeStatusCode(403);
    }
    public function test_book_unuseableMentoringSlot_cancelled_403()
    {
        $this->mentoringSlotOne->cancelled = true;;
        
        $this->book();
        $this->seeStatusCode(403);
    }
    public function test_book_unuseableMentoringSlot_fromDifferentProgram_403()
    {
        $firm = $this->clientParticipant->participant->program->firm;
        
        $program = new RecordOfProgram($firm, 'zzz');
        $program->insert($this->connection);
        
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $consultant = new RecordOfConsultant($program, $personnel, 'zzz');
        
        $consultationSetup = new RecordOfConsultationSetup($program, null, null, 'zzz');
        
        $this->mentoringSlotOne->consultant = $consultant;
        $this->mentoringSlotOne->consultationSetup = $consultationSetup;
        
        $this->book();
        $this->seeStatusCode(403);
    }
    public function test_book_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->book();
        $this->seeStatusCode(403);
    }
    
    protected function cancel()
    {
        $this->clientParticipant->participant->program->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultant->personnel->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultant->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/booked-mentoring-slots/{$this->bookedMentoringSlotOne->mentoring->id}";
        $this->delete($uri, [], $this->clientParticipant->client->token);
    }
    public function test_cancel_200()
    {
        $this->cancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->bookedMentoringSlotOne->mentoring->id,
            'cancelled' => true,
            'mentoringSlot' => [
                'id' => $this->mentoringSlotOne->id,
                'cancelled' => $this->mentoringSlotOne->cancelled,
                'capacity' => $this->mentoringSlotOne->capacity,
                'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                'mediaType' => $this->mentoringSlotOne->mediaType,
                'location' => $this->mentoringSlotOne->location,
                'consultationSetup' => [
                    'id' => $this->mentoringSlotOne->consultationSetup->id,
                    'name' => $this->mentoringSlotOne->consultationSetup->name,
                    'participantFeedbackForm' => null
                ],
            ],
            'participantReport' => null,
        ];
        $this->seeJsonContains($response);
        
        $record = [
            'id' => $this->bookedMentoringSlotOne->mentoring->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('BookedMentoringSlot', $record);
    }
    public function test_cancel_notAnUpcomingSchedule_403()
    {
        $this->bookedMentoringSlotOne->mentoringSlot->startTime = new \DateTimeImmutable('-2 hours');
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_unmanagedBookedMentoringSlot_belongsToOtherParticipant_403()
    {
        $program = $this->clientParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->bookedMentoringSlotOne->participant = $participant;
        
        $this->cancel();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->clientParticipant->participant->program->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultant->personnel->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultant->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/booked-mentoring-slots/{$this->bookedMentoringSlotOne->mentoring->id}";
        $this->get($uri, $this->clientParticipant->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->bookedMentoringSlotOne->mentoring->id,
            'cancelled' => false,
            'mentoringSlot' => [
                'id' => $this->mentoringSlotOne->id,
                'cancelled' => $this->mentoringSlotOne->cancelled,
                'capacity' => $this->mentoringSlotOne->capacity,
                'startTime' => $this->mentoringSlotOne->startTime->format('Y-m-d H:i:s'),
                'endTime' => $this->mentoringSlotOne->endTime->format('Y-m-d H:i:s'),
                'mediaType' => $this->mentoringSlotOne->mediaType,
                'location' => $this->mentoringSlotOne->location,
                'consultationSetup' => [
                    'id' => $this->mentoringSlotOne->consultationSetup->id,
                    'name' => $this->mentoringSlotOne->consultationSetup->name,
                    'participantFeedbackForm' => null
                ],
            ],
            'participantReport' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_inviewableBookedMentoringSlot_belongsToOtherParticipant_404()
    {
        $program = $this->clientParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->bookedMentoringSlotOne->participant = $participant;
        
        $this->show();
        $this->seeStatusCode(404);
    }
    
    protected function submitReport()
    {
        $this->clientParticipant->participant->program->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultant->personnel->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultant->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->participantFeedbackForm = $this->feedbackFormOne;
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->participantFeedbackForm->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        $this->bookedMentoringSlotOne->mentoringSlot->consultationSetup->insert($this->connection);
        
        $this->bookedMentoringSlotOne->mentoringSlot->insert($this->connection);
        $this->bookedMentoringSlotOne->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/booked-mentoring-slots/{$this->bookedMentoringSlotOne->mentoring->id}/submit-report";
        $this->put($uri, $this->submitReportRequest, $this->clientParticipant->client->token);
    }
    public function test_submitReport_200()
    {
        $this->mentoringSlotOne->startTime = new DateTimeImmutable('-24 hours');
        $this->mentoringSlotOne->endTime = new DateTimeImmutable('-22 hours');
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $consultationSetupResponse = [
            'id' => $this->mentoringSlotOne->consultationSetup->id,
            'name' => $this->mentoringSlotOne->consultationSetup->name,
            'participantFeedbackForm' => [
                'id' => $this->feedbackFormOne->id,
                'name' => $this->feedbackFormOne->form->name,
                'description' => $this->feedbackFormOne->form->description,
                'stringFields' => [
                    [
                        "id" => $this->stringFieldOne->id,
                        "name" => $this->stringFieldOne->name,
                        "description" => $this->stringFieldOne->description,
                        "position" => $this->stringFieldOne->position,
                        "mandatory" => $this->stringFieldOne->mandatory,
                        "defaultValue" => $this->stringFieldOne->defaultValue,
                        "minValue" => $this->stringFieldOne->minValue,
                        "maxValue" => $this->stringFieldOne->maxValue,
                        "placeholder" => $this->stringFieldOne->placeholder,
                    ],
                ],
                'integerFields' => [],
                'textAreaFields' => [],
                'attachmentFields' => [],
                'singleSelectFields' => [],
                'multiSelectFields' => [],
            ]
        ];
        $this->seeJsonContains($consultationSetupResponse);
        
        $participantReportResponse = [
            'mentorRating' => $this->submitReportRequest['mentorRating'],
            'submitTime' => $this->currentTimeString(),
        ];
        $this->seeJsonContains($participantReportResponse);
        
        $stringFieldRecordResponse = [
            'stringField' => [
                'id' => $this->stringFieldOne->id,
                'name' => $this->stringFieldOne->name,
                'position' => $this->stringFieldOne->position,
            ],
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeJsonContains($stringFieldRecordResponse);
        
        $participantReportRecord = [
            'mentorRating' => $this->submitReportRequest['mentorRating'],
            'Mentoring_id' => $this->bookedMentoringSlotOne->mentoring->id,
        ];
        $this->seeInDatabase('ParticipantReport', $participantReportRecord);
        
        $formRecordRecord = [
            'submitTime' => $this->currentTimeString(),
            'Form_id' => $this->feedbackFormOne->form->id,
        ];
        $this->seeInDatabase('FormRecord', $formRecordRecord);
        
        $stringFieldRecord = [
            'removed' => false,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
            'StringField_id' => $this->stringFieldOne->id,
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecord);
    }
    public function test_submitReport_alreadyReported_update_200()
    {
        $this->mentoringSlotOne->startTime = new DateTimeImmutable('-24 hours');
        $this->mentoringSlotOne->endTime = new DateTimeImmutable('-22 hours');
        $this->participantReportOne->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $participantReportResponse = [
            'id' => $this->participantReportOne->id,
            'mentorRating' => $this->submitReportRequest['mentorRating'],
        ];
        $this->seeJsonContains($participantReportResponse);
        
        $stringFieldRecordResponse = [
            'id' => $this->stringFieldRecordOne->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeJsonContains($stringFieldRecordResponse);
        
        $participantReportRecord = [
            'id' => $this->participantReportOne->id,
            'mentorRating' => $this->submitReportRequest['mentorRating'],
        ];
        $this->seeInDatabase('ParticipantReport', $participantReportRecord);
        
        $stringFieldRecordRecord = [
            'id' => $this->stringFieldRecordOne->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecordRecord);
    }
    public function test_submitReport_notPastSchedule_403()
    {
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_unamagedBooking_cancelled_403()
    {
        $this->bookedMentoringSlotOne->cancelled = true;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_unamagedBooking_notOwnBooking_403()
    {
        $program = $this->clientParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->bookedMentoringSlotOne->participant = $participant;
        
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
}
