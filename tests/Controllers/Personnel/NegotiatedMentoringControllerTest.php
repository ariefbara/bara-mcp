<?php

namespace Tests\Controllers\Personnel;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
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

class NegotiatedMentoringControllerTest extends PersonnelTestCase
{
    protected $clientParticipantOne;
    protected $stringFieldOne;
    protected $negotiatedMentoringOne;
    protected $mentorReportOne;
    protected $stringFieldRecordOne;
    
    protected $submitReportRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('MentorReport')->truncate();
        
        $program = $this->mentor->program;
        $firm = $program->firm;
        
        $clientOne = new RecordOfClient($firm, '1');
        $participantOne = new RecordOfParticipant($program, '1');
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $formOne = new RecordOfForm('1');
        $this->stringFieldOne = new RecordOfStringField($formOne, '1');
        $mentorFeedbackform = new RecordOfFeedbackForm($firm, $formOne);
        $consultationSetupOne = new RecordOfConsultationSetup($program, null, $mentorFeedbackform, '1');
        
        $mentoringRequestOne = new RecordOfMentoringRequest($participantOne, $this->mentor, $consultationSetupOne, '1');
        $mentoringRequestOne->startTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $mentoringRequestOne->endTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        $mentoring = new RecordOfMentoring('1');
        $this->negotiatedMentoringOne = new RecordOfNegotiatedMentoring($mentoringRequestOne, $mentoring);
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $this->stringFieldRecordOne = new RecordOfStringFieldRecord($formRecordOne, $this->stringFieldOne, '1');
        $this->mentorReportOne = new RecordOfMentorReport($mentoring, $formRecordOne, '1');
        
        $this->submitReportRequest = [
            'participantRating' => 34,
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
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('MentorReport')->truncate();
    }
    
    protected function submitReport()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->consultantFeedbackForm->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->insert($this->connection);
        
        $this->negotiatedMentoringOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/negotiated-mentorings/{$this->negotiatedMentoringOne->id}/submit-report";
        $this->put($uri, $this->submitReportRequest, $this->mentor->personnel->token);
    }
    public function test_submit_200()
    {
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $mentorReportRequest = [
            'participantRating' => $this->submitReportRequest['participantRating'],
            'submitTime' => $this->currentTimeString(),
        ];
        $this->seeJsonContains($mentorReportRequest);
        
        $stringFieldRecordResponse = [
            "stringField" => [
                "id" => $this->stringFieldRecordOne->stringField->id,
                "name" => $this->stringFieldRecordOne->stringField->name,
                "position" => $this->stringFieldRecordOne->stringField->position,
            ],
            "value" => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeJsonContains($stringFieldRecordResponse);
        
        $mentorReportRecord = [
            'participantRating' => $this->submitReportRequest['participantRating'],
            'Mentoring_id' => $this->negotiatedMentoringOne->mentoring->id,
        ];
        $this->seeInDatabase('MentorReport', $mentorReportRecord);
        
        $formRecordRecord = [
            'Form_id' => $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->consultantFeedbackForm->form->id,
            'submitTime' => $this->currentTimeString(),
        ];
        $this->seeInDatabase('FormRecord', $formRecordRecord);
        
        $stringFieldRecordRecord = [
            'StringField_id' => $this->stringFieldOne->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecordRecord);
    }
    public function test_submit_alreadySubmitReport_updateExistingReport_200()
    {
        $this->mentorReportOne->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $mentorReportResponse = [
            'participantRating' => $this->submitReportRequest['participantRating'],
        ];
        $this->seeJsonContains($mentorReportResponse);
        
        $stringFieldRecordResponse = [
            'id' => $this->stringFieldRecordOne->id,
            "stringField" => [
                "id" => $this->stringFieldRecordOne->stringField->id,
                "name" => $this->stringFieldRecordOne->stringField->name,
                "position" => $this->stringFieldRecordOne->stringField->position,
            ],
            "value" => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeJsonContains($stringFieldRecordResponse);
        
        $mentorReportRecord = [
            'id' => $this->mentorReportOne->id,
            'participantRating' => $this->submitReportRequest['participantRating'],
            'Mentoring_id' => $this->negotiatedMentoringOne->mentoring->id,
        ];
        $this->seeInDatabase('MentorReport', $mentorReportRecord);
        
        $stringFieldRecordRecord = [
            'id' => $this->stringFieldRecordOne->id,
            'StringField_id' => $this->stringFieldOne->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecordRecord);
    }
    public function test_submit_notPastSchedule_403()
    {
        $this->negotiatedMentoringOne->mentoringRequest->startTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->negotiatedMentoringOne->mentoringRequest->endTime = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submit_unamnagedNegotiatedMentoring_notOwned_403()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        
        $mentor->personnel->insert($this->connection);
        $mentor->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->mentor = $mentor;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submit_inactiveMentor_403()
    {
        $this->mentor->active = false;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->consultantFeedbackForm->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->insert($this->connection);
        
        $this->negotiatedMentoringOne->insert($this->connection);
        
        $this->mentorReportOne->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/negotiated-mentorings/{$this->negotiatedMentoringOne->id}";
        $this->get($uri, $this->mentor->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $reponse = [
            'id' => $this->negotiatedMentoringOne->id,
            'mentoringRequest' => [
                'id' => $this->negotiatedMentoringOne->mentoringRequest->id,
                'consultationSetup' => [
                    'id' => $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->id,
                    'participantFeedbackForm' => [
                        'name' => $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->consultantFeedbackForm->form->name,
                        'description' => $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->consultantFeedbackForm->form->description,
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
                    ],
                ],
            ],
            'mentorReport' => [
                'participantRating' => $this->mentorReportOne->participantRating,
                'submitTime' => $this->mentorReportOne->formRecord->submitTime,
                'stringFieldRecords' => [
                    [
                        'id' => $this->stringFieldRecordOne->id,
                        'stringField' => [
                            'id' => $this->stringFieldRecordOne->stringField->id,
                            'name' => $this->stringFieldRecordOne->stringField->name,
                            'position' => $this->stringFieldRecordOne->stringField->position,
                        ],
                        'value' => $this->stringFieldRecordOne->value,
                    ],
                ],
                'integerFieldRecords' => [],
                'textAreaFieldRecords' => [],
                'attachmentFieldRecords' => [],
                'singleSelectFieldRecords' => [],
                'multiSelectFieldRecords' => [],
            ],
        ];
        $this->seeJsonContains($reponse);
    }
}
