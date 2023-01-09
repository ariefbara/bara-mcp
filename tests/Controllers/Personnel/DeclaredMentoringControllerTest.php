<?php

namespace Tests\Controllers\Personnel;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDeclaredMentoring;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\Mentoring\RecordOfMentorReport;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfMentoring;

class DeclaredMentoringControllerTest extends PersonnelTestCase
{
    protected $clientParticipant;
    protected $consultationSetupOne;
    protected $stringFieldOne;
    protected $declaredMentoringOne;
    protected $mentorReport_dm1;
    protected $stringFieldRecordOne;
    protected $declareRequest;
    protected $updateRequest;
    protected $submitReportRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('MentorReport')->truncate();
        
        $program = $this->mentor->program;
        $firm = $program->firm;
        
        $participantOne = new RecordOfParticipant($program, '1');
        $clientOne = new RecordOfClient($firm, '1');
        $this->clientParticipant = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $formOne = new RecordOfForm('1');
        $feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        $this->stringFieldOne = new RecordOfStringField($formOne, '1');
        $this->consultationSetupOne = new RecordOfConsultationSetup($program, null, $feedbackFormOne, '1');
        
        $mentoringOne = new RecordOfMentoring('1');
        $this->declaredMentoringOne = new RecordOfDeclaredMentoring($this->mentor, $participantOne, $this->consultationSetupOne, $mentoringOne);
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $this->stringFieldRecordOne = new RecordOfStringFieldRecord($formRecordOne, $this->stringFieldOne, '1');
        $this->mentorReport_dm1 = new RecordOfMentorReport($mentoringOne, $formRecordOne, '1');
        
        $this->declareRequest = [
            'participantId' => $participantOne->id,
            'consultationSetupId' => $this->consultationSetupOne->id,
            'startTime' => (new DateTimeImmutable('-49 hours'))->format('Y-m-d H:i:s'),
            'endTime' => (new DateTimeImmutable('-48 hours'))->format('Y-m-d H:i:s'),
            'mediaType' => 'new media type',
            'location' => 'new location',
        ];
        
        $this->updateRequest = [
            'startTime' => (new DateTimeImmutable('-49 hours'))->format('Y-m-d H:i:s'),
            'endTime' => (new DateTimeImmutable('-48 hours'))->format('Y-m-d H:i:s'),
            'mediaType' => 'new media type',
            'location' => 'new location',
        ];
        
        $this->submitReportRequest = [
            'participantRating' => 567,
            'stringFieldRecords' => [
                [
                    'fieldId' => $this->stringFieldOne->id,
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
//        $this->connection->table('Participant')->truncate();
//        $this->connection->table('Client')->truncate();
//        $this->connection->table('ClientParticipant')->truncate();
//        $this->connection->table('Form')->truncate();
//        $this->connection->table('FeedbackForm')->truncate();
//        $this->connection->table('StringField')->truncate();
//        $this->connection->table('ConsultationSetup')->truncate();
//        $this->connection->table('Mentoring')->truncate();
//        $this->connection->table('DeclaredMentoring')->truncate();
//        $this->connection->table('FormRecord')->truncate();
//        $this->connection->table('StringFieldRecord')->truncate();
//        $this->connection->table('MentorReport')->truncate();
    }
    
    protected function declare()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/declared-mentorings";
        $this->post($uri, $this->declareRequest, $this->personnel->token);
    }
    public function test_declare_201()
    {
        $this->declare();
        $this->seeStatusCode(201);
        
        $response = [
            'startTime' => $this->declareRequest['startTime'],
            'endTime' => $this->declareRequest['endTime'],
            'mediaType' => $this->declareRequest['mediaType'],
            'location' => $this->declareRequest['location'],
            'declaredStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[DeclaredMentoringStatus::DECLARED_BY_MENTOR],
            'consultationSetup' => [
                'id' => $this->consultationSetupOne->id,
                'name' => $this->consultationSetupOne->name,
                'mentorFeedbackForm' => [
                    'name' => $this->consultationSetupOne->consultantFeedbackForm->form->name,
                    'description' => $this->consultationSetupOne->consultantFeedbackForm->form->description,
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
                    'sections' => [],
                    'integerFields' => [],
                    'textAreaFields' => [],
                    'attachmentFields' => [],
                    'singleSelectFields' => [],
                    'multiSelectFields' => [],
                ],
            ],
            'participant' => [
                'id' => $this->clientParticipant->participant->id,
                'client' => [
                    'id' => $this->clientParticipant->client->id,
                    'name' => $this->clientParticipant->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'mentorReport' => null,
        ];
        $this->seeJsonContains($response);
        
        $declaredMentoringRecord = [
            'startTime' => $this->declareRequest['startTime'],
            'endTime' => $this->declareRequest['endTime'],
            'mediaType' => $this->declareRequest['mediaType'],
            'location' => $this->declareRequest['location'],
            'declaredStatus' => DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            'Consultant_id' => $this->mentor->id,
            'participant_id' => $this->declareRequest['participantId'],
            'consultationSetup_id' => $this->declareRequest['consultationSetupId'],
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_declare_notPastEvent_400()
    {
        $this->declareRequest['startTime'] = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->declareRequest['endTime'] = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->declare();
        $this->seeStatusCode(400);
    }
    public function test_declare_inactiveMentor_403()
    {
        $this->mentor->active = false;
        $this->declare();
        $this->seeStatusCode(403);
    }
    public function test_declare_unusableParticipant_inactive_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->declare();
        $this->seeStatusCode(403);
    }
    public function test_declare_unusableParticipant_belongsToOtherProgram_403()
    {
        $program = new RecordOfProgram($this->mentor->program->firm, 'zzz');
        $program->insert($this->connection);
        $this->clientParticipant->participant->program = $program;
        
        $this->declare();
        $this->seeStatusCode(403);
    }
    public function test_declare_unusableConsultationSetup_inactive_403()
    {
        $this->consultationSetupOne->removed = true;
        $this->declare();
        $this->seeStatusCode(403);
    }
    public function test_declare_unusableConsultationSetup_belongsToOtherProgram_403()
    {
        $program = new RecordOfProgram($this->mentor->program->firm, 'zzz');
        $program->insert($this->connection);
        $this->consultationSetupOne->program = $program;
        $this->declare();
        $this->seeStatusCode(403);
    }
    
    protected function update()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $this->declaredMentoringOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/declared-mentorings/{$this->declaredMentoringOne->id}/update";
        $this->patch($uri, $this->updateRequest, $this->personnel->token);
    }
    public function test_update_200()
    {
        $this->update();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->declaredMentoringOne->id,
            'startTime' => $this->updateRequest['startTime'],
            'endTime' => $this->updateRequest['endTime'],
            'mediaType' => $this->updateRequest['mediaType'],
            'location' => $this->updateRequest['location'],
            'declaredStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[DeclaredMentoringStatus::DECLARED_BY_MENTOR],
            'consultationSetup' => [
                'id' => $this->consultationSetupOne->id,
                'name' => $this->consultationSetupOne->name,
                'mentorFeedbackForm' => [
                    'name' => $this->consultationSetupOne->consultantFeedbackForm->form->name,
                    'description' => $this->consultationSetupOne->consultantFeedbackForm->form->description,
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
                    'sections' => [],
                    'integerFields' => [],
                    'textAreaFields' => [],
                    'attachmentFields' => [],
                    'singleSelectFields' => [],
                    'multiSelectFields' => [],
                ],
            ],
            'participant' => [
                'id' => $this->clientParticipant->participant->id,
                'client' => [
                    'id' => $this->clientParticipant->client->id,
                    'name' => $this->clientParticipant->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'mentorReport' => null,
        ];
        $this->seeJsonContains($response);
        
        $declaredMentoringRecord = [
            'id' => $this->declaredMentoringOne->id,
            'startTime' => $this->updateRequest['startTime'],
            'endTime' => $this->updateRequest['endTime'],
            'mediaType' => $this->updateRequest['mediaType'],
            'location' => $this->updateRequest['location'],
            'declaredStatus' => DeclaredMentoringStatus::DECLARED_BY_MENTOR,
            'Consultant_id' => $this->declaredMentoringOne->mentor->id,
            'Participant_id' => $this->declaredMentoringOne->participant->id,
            'ConsultationSetup_id' => $this->declaredMentoringOne->consultationSetup->id,
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_update_notPastEvent_400()
    {
        $this->updateRequest['startTime'] = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->updateRequest['endTime'] = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_updatingNonDeclaredByMentorState_403()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inactiveMentor_403()
    {
        $this->mentor->active = false;
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_unamangedDeclaredMentoring_notOwned()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $personnel->insert($this->connection);
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        $mentor->insert($this->connection);
        
        $this->declaredMentoringOne->mentor = $mentor;
        $this->update();
        $this->seeStatusCode(403);
    }
    
    protected function cancel()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $this->declaredMentoringOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/declared-mentorings/{$this->declaredMentoringOne->id}/cancel";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_cancel_200()
    {
        $this->cancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[DeclaredMentoringStatus::CANCELLED],
        ];
        $this->seeJsonContains($response);
        
        $declaredMentoringRecord = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::CANCELLED,
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_cancel_nonDeclaredByMentorState_403()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_inactiveMentor_403()
    {
        $this->mentor->active = false;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_unamangedDeclaredMentoring_notOwned()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $personnel->insert($this->connection);
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        $mentor->insert($this->connection);
        
        $this->declaredMentoringOne->mentor = $mentor;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    
    protected function approve()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $this->declaredMentoringOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/declared-mentorings/{$this->declaredMentoringOne->id}/approve";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_approve_200()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $this->approve();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[DeclaredMentoringStatus::APPROVED_BY_MENTOR],
        ];
        $this->seeJsonContains($response);
        
        $declaredMentoringRecord = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::APPROVED_BY_MENTOR,
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_approve_nonDeclaredByParticipantState_403()
    {
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_inactiveMentor_403()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $this->mentor->active = false;
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_unamangedDeclaredMentoring_notOwned()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $personnel->insert($this->connection);
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        $mentor->insert($this->connection);
        
        $this->declaredMentoringOne->mentor = $mentor;
        $this->approve();
        $this->seeStatusCode(403);
    }
    
    protected function deny()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $this->declaredMentoringOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/declared-mentorings/{$this->declaredMentoringOne->id}/deny";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_deny_200()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $this->deny();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[DeclaredMentoringStatus::DENIED_BY_MENTOR],
        ];
        $this->seeJsonContains($response);
        
        $declaredMentoringRecord = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::DENIED_BY_MENTOR,
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_deny_nonDeclaredByParticipantState_403()
    {
        $this->deny();
        $this->seeStatusCode(403);
    }
    public function test_deny_inactiveMentor_403()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;
        $this->mentor->active = false;
        $this->deny();
        $this->seeStatusCode(403);
    }
    public function test_deny_unamangedDeclaredMentoring_notOwned()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $personnel->insert($this->connection);
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        $mentor->insert($this->connection);
        
        $this->declaredMentoringOne->mentor = $mentor;
        $this->deny();
        $this->seeStatusCode(403);
    }
    
    protected function submitReport()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $this->declaredMentoringOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/mentors/{$this->mentor->id}/declared-mentorings/{$this->declaredMentoringOne->id}/submit-report";
        $this->put($uri, $this->submitReportRequest, $this->personnel->token);
    }
    public function test_submitReport_200()
    {
$this->disableExceptionHandling();
        $submitTime = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $mentorReportRecord = [
            'participantRating' => $this->submitReportRequest['participantRating'],
            'Mentoring_id' => $this->declaredMentoringOne->mentoring->id,
        ];
        $this->seeInDatabase('MentorReport', $mentorReportRecord);
        
        $formRecordRecord = [
            'Form_id' => $this->consultationSetupOne->consultantFeedbackForm->form->id,
            'submitTime' => $submitTime,
        ];
        $this->seeInDatabase('FormRecord', $formRecordRecord);
        
        $stringFieldRecord = [
            'stringField_id' => $this->stringFieldOne->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecord);
    }
    public function test_submitReport_alreadySubmitted_updateExisting_200()
    {
        $this->mentorReport_dm1->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $mentorReportRecord = [
            'id' => $this->mentorReport_dm1->id,
            'participantRating' => $this->submitReportRequest['participantRating'],
        ];
        $this->seeInDatabase('MentorReport', $mentorReportRecord);
        
        $stringFieldRecord = [
            'id' => $this->stringFieldRecordOne->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecord);
    }
    public function test_submitReport_unreportableDeclaratino_cancelled()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::CANCELLED;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_unreportableDeclaratino_deniedByMentor()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DENIED_BY_MENTOR;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_unreportableDeclaratino_deniedByParticipant()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DENIED_BY_PARTICIPANT;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_inactiveMentor_403()
    {
        $this->mentor->active = false;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_unamangedDeclaredMentoring_notOwned()
    {
        $program = $this->mentor->program;
        $firm = $program->firm;
        $personnel = new RecordOfPersonnel($firm, 'zzz');
        $personnel->insert($this->connection);
        $mentor = new RecordOfConsultant($program, $personnel, 'zzz');
        $mentor->insert($this->connection);
        
        $this->declaredMentoringOne->mentor = $mentor;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->insertMentorDependency();
        
        $this->clientParticipant->client->insert($this->connection);
        $this->clientParticipant->insert($this->connection);
        
        $this->consultationSetupOne->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $this->declaredMentoringOne->insert($this->connection);
        
        $this->mentorReport_dm1->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/declared-mentorings/{$this->declaredMentoringOne->id}";
//echo $uri;
        $this->get($uri, $this->personnel->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
//$this->seeJsonContains(['print']);
        
        $response = [
            'startTime' => $this->declaredMentoringOne->startTime,
            'endTime' => $this->declaredMentoringOne->endTime,
            'mediaType' => $this->declaredMentoringOne->mediaType,
            'location' => $this->declaredMentoringOne->location,
            'declaredStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[$this->declaredMentoringOne->declaredStatus],
            'consultationSetup' => [
                'id' => $this->consultationSetupOne->id,
                'name' => $this->consultationSetupOne->name,
                'mentorFeedbackForm' => [
                    'name' => $this->consultationSetupOne->consultantFeedbackForm->form->name,
                    'description' => $this->consultationSetupOne->consultantFeedbackForm->form->description,
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
                    'sections' => [],
                    'integerFields' => [],
                    'textAreaFields' => [],
                    'attachmentFields' => [],
                    'singleSelectFields' => [],
                    'multiSelectFields' => [],
                ],
            ],
            'participant' => [
                'id' => $this->clientParticipant->participant->id,
                'client' => [
                    'id' => $this->clientParticipant->client->id,
                    'name' => $this->clientParticipant->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'mentorReport' => [
                'participantRating' => $this->mentorReport_dm1->participantRating,
                'submitTime' => $this->mentorReport_dm1->formRecord->submitTime,
                'stringFieldRecords' => [
                    [
                        "id" => $this->stringFieldRecordOne->id,
                        "stringField" => [
                            "id" => $this->stringFieldRecordOne->stringField->id,
                            "name" => $this->stringFieldRecordOne->stringField->name,
                            "position" => $this->stringFieldRecordOne->stringField->position,
                        ],
                        "value" => $this->stringFieldRecordOne->value,
                    ],
                ],
                'integerFieldRecords' => [],
                'textAreaFieldRecords' => [],
                'attachmentFieldRecords' => [],
                'singleSelectFieldRecords' => [],
                'multiSelectFieldRecords' => [],
            ],
        ];
        $this->seeJsonContains($response);
    }
}
