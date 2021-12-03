<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MentoringRequest\RecordOfNegotiatedMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMentoringRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfParticipantReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class NegotiatedMentoringControllerTest extends ExtendedTeamParticipantTestCase
{
    protected $stringFieldOne;
    protected $negotiatedMentoringOne;
    protected $participantReportOne;
    protected $stringFieldRecordOne;
    
    protected $submitReportRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        
        $participant = $this->teamParticipant->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $mentorOne = new RecordOfConsultant($program, $personnelOne, '1');
        
        $formOne = new RecordOfForm('1');
        $this->stringFieldOne = new RecordOfStringField($formOne, '1');
        $participantFeedbackForm = new RecordOfFeedbackForm($firm, $formOne);
        $consultationSetupOne = new RecordOfConsultationSetup($program, $participantFeedbackForm, null, '1');
        
        $mentoringRequestOne = new RecordOfMentoringRequest($participant, $mentorOne, $consultationSetupOne, '1');
        $mentoringRequestOne->startTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $mentoringRequestOne->endTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        $mentoring = new RecordOfMentoring('1');
        $this->negotiatedMentoringOne = new RecordOfNegotiatedMentoring($mentoringRequestOne, $mentoring);
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $this->stringFieldRecordOne = new RecordOfStringFieldRecord($formRecordOne, $this->stringFieldOne, '1');
        $this->participantReportOne = new RecordOfParticipantReport($mentoring, $formRecordOne, '1');
        
        $this->submitReportRequest = [
            'mentorRating' => 66,
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
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('MentoringRequest')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('NegotiatedMentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
    }
    
    protected function submit()
    {
        parent::prepareRecord();
        
        $this->negotiatedMentoringOne->mentoringRequest->mentor->personnel->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->mentor->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->participantFeedbackForm->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->insert($this->connection);
        
        $this->negotiatedMentoringOne->insert($this->connection);
        
        $uri = $this->teamParticipantUri . "/negotiated-mentorings/{$this->negotiatedMentoringOne->id}/submit-report";
        $this->put($uri, $this->submitReportRequest, $this->teamMember->client->token);
    }
    public function test_submit_200()
    {
        $this->submit();
        $this->seeStatusCode(200);
        
        $participantReportResponse = [
            'mentorRating' => $this->submitReportRequest['mentorRating'],
            'submitTime' => $this->currentTimeString(),
        ];
        $this->seeJsonContains($participantReportResponse);
        
        $stringFieldRecordResponse = [
            "stringField" => [
                "id" => $this->stringFieldRecordOne->stringField->id,
                "name" => $this->stringFieldRecordOne->stringField->name,
                "position" => $this->stringFieldRecordOne->stringField->position,
            ],
            "value" => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeJsonContains($stringFieldRecordResponse);
        
        $participantReportRecord = [
            'mentorRating' => $this->submitReportRequest['mentorRating'],
            'Mentoring_id' => $this->negotiatedMentoringOne->mentoring->id,
        ];
        $this->seeInDatabase('ParticipantReport', $participantReportRecord);
        
        $formRecordRecord = [
            'Form_id' => $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->participantFeedbackForm->form->id,
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
        $this->participantReportOne->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $this->submit();
        $this->seeStatusCode(200);
        
        $participantReportResponse = [
            'mentorRating' => $this->submitReportRequest['mentorRating'],
        ];
        $this->seeJsonContains($participantReportResponse);
        
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
        
        $participantReportRecord = [
            'id' => $this->participantReportOne->id,
            'mentorRating' => $this->submitReportRequest['mentorRating'],
            'Mentoring_id' => $this->negotiatedMentoringOne->mentoring->id,
        ];
        $this->seeInDatabase('ParticipantReport', $participantReportRecord);
        
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
        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_unamnagedNegotiatedMentoring_notOwned_403()
    {
        $participant = new RecordOfParticipant($this->teamParticipant->participant->program, 'zzz');
        $participant->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->participant = $participant;
        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_inactiveParticipant_403()
    {
        $this->teamParticipant->participant->active = false;
        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_inactiveMember_403()
    {
        $this->teamMember->active = false;
        $this->submit();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        parent::prepareRecord();
        
        $this->negotiatedMentoringOne->mentoringRequest->mentor->personnel->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->mentor->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->participantFeedbackForm->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->insert($this->connection);
        
        $this->negotiatedMentoringOne->mentoringRequest->insert($this->connection);
        
        $this->negotiatedMentoringOne->insert($this->connection);
        
        $this->participantReportOne->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $uri = $this->teamParticipantUri . "/negotiated-mentorings/{$this->negotiatedMentoringOne->id}";
        $this->get($uri, $this->teamMember->client->token);
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
                        'name' => $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->participantFeedbackForm->form->name,
                        'description' => $this->negotiatedMentoringOne->mentoringRequest->consultationSetup->participantFeedbackForm->form->description,
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
            'participantReport' => [
                'mentorRating' => $this->participantReportOne->mentorRating,
                'submitTime' => $this->participantReportOne->formRecord->submitTime,
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
    public function test_show_unamangedNegotiateMentoring_notOwned_404()
    {
        $participant = new RecordOfParticipant($this->teamParticipant->participant->program, 'zzz');
        $participant->insert($this->connection);
        $this->negotiatedMentoringOne->mentoringRequest->participant = $participant;
        $this->show();
        $this->seeStatusCode(404);
    }
    public function test_show_inactiveParticipant_403()
    {
        $this->teamParticipant->participant->active = false;
        $this->show();
        $this->seeStatusCode(403);
    }
    public function test_show_inactiveMember_403()
    {
        $this->teamMember->active = false;
        $this->show();
        $this->seeStatusCode(403);
    }
}
