<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use DateTimeImmutable;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use Tests\Controllers\Client\AsTeamMember\ProgramParticipation\ExtendedTeamParticipantTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDeclaredMentoring;
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

class DeclaredMentoringControllerTest extends ExtendedTeamParticipantTestCase
{

    protected $feedbackFormOne;
    protected $stringFieldOne_ff1;
    protected $consultationSetupOne;
    protected $mentorOne;
    protected $declaredMentoringOne;
    protected $participantReportOne_dm1;
    protected $stringFieldRecordOne_pr_dm1;
    protected $declareRequest;
    protected $updateRequest;
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
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();

        $participant = $this->teamParticipant->participant;
        $program = $participant->program;
        $firm = $program->firm;

        $personnelOne = new RecordOfPersonnel($firm, '1');
        $this->mentorOne = new RecordOfConsultant($program, $personnelOne, '1');

        $formOne_ff1 = new RecordOfForm('1-ff1');
        $this->stringFieldOne_ff1 = new RecordOfStringField($formOne_ff1, '1-ff1');
        $this->feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne_ff1);
        $this->consultationSetupOne = new RecordOfConsultationSetup($program, $this->feedbackFormOne, null, '1');

        $mentoringOne_dm1 = new RecordOfMentoring('1_dm1');
        $this->declaredMentoringOne = new RecordOfDeclaredMentoring(
                $this->mentorOne, $participant, $this->consultationSetupOne, $mentoringOne_dm1);
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT;

        $formRecordOne_pr_dm1 = new RecordOfFormRecord($formOne_ff1, '1-pr-dm1');
        $this->participantReportOne_dm1 = new RecordOfParticipantReport($mentoringOne_dm1, $formRecordOne_pr_dm1,
                '1-dm1');
        $this->stringFieldRecordOne_pr_dm1 = new RecordOfStringFieldRecord($formRecordOne_pr_dm1,
                $this->stringFieldOne_ff1, '1-pr-dm1');

        $this->declareRequest = [
            'mentorId' => $this->mentorOne->id,
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
            'mentorRating' => 567,
            'stringFieldRecords' => [
                [
                    'fieldId' => $this->stringFieldOne_ff1->id,
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
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Mentoring')->truncate();
        $this->connection->table('DeclaredMentoring')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantReport')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
    }

    protected function declare()
    {
        parent::prepareRecord();

        $this->mentorOne->personnel->insert($this->connection);
        $this->mentorOne->insert($this->connection);

        $this->consultationSetupOne->participantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne_ff1->insert($this->connection);

        $uri = $this->teamParticipantUri . "/declared-mentorings";
        $this->post($uri, $this->declareRequest, $this->teamMember->client->token);
    }
    public function test_declared_200()
    {
        $this->declare();
        $this->seeStatusCode(200);

        $declaredMentoringRecord = [
            'Participant_id' => $this->teamParticipant->participant->id,
            'Consultant_id' => $this->mentorOne->id,
            'ConsultationSetup_id' => $this->consultationSetupOne->id,
            'startTime' => $this->declareRequest['startTime'],
            'endTime' => $this->declareRequest['endTime'],
            'mediaType' => $this->declareRequest['mediaType'],
            'location' => $this->declareRequest['location'],
            'declaredStatus' => DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
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
    public function test_declare_inactiveParticipant_403()
    {
        $this->teamParticipant->participant->active = false;
        $this->declare();
        $this->seeStatusCode(403);
    }
    public function test_declare_inactiveMember_403()
    {
        $this->teamMember->active = false;
        $this->declare();
        $this->seeStatusCode(403);
    }
    public function test_declare_unusableMentor_inactive_403()
    {
        $this->mentorOne->active = false;
        $this->declare();
        $this->seeStatusCode(403);
    }
    public function test_declare_unusableMentor_belongsToOtherProgram_403()
    {
        $program = new RecordOfProgram($this->teamParticipant->team->firm, 'zzz');
        $program->insert($this->connection);
        $this->mentorOne->program = $program;

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
        $program = new RecordOfProgram($this->teamParticipant->team->firm, 'zzz');
        $program->insert($this->connection);
        $this->consultationSetupOne->program = $program;
        $this->declare();
        $this->seeStatusCode(403);
    }
    
    protected function update()
    {
        parent::prepareRecord();

        $this->mentorOne->personnel->insert($this->connection);
        $this->mentorOne->insert($this->connection);

        $this->consultationSetupOne->participantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne_ff1->insert($this->connection);

        $this->declaredMentoringOne->insert($this->connection);

        $uri = $this->teamParticipantUri . "/declared-mentorings/{$this->declaredMentoringOne->id}/update";
        $this->patch($uri, $this->updateRequest, $this->teamMember->client->token);
    }
    public function test_update_200()
    {
        $this->update();
        $this->seeStatusCode(200);

        $declaredMentoringRecord = [
            'id' => $this->declaredMentoringOne->id,
            'startTime' => $this->updateRequest['startTime'],
            'endTime' => $this->updateRequest['endTime'],
            'mediaType' => $this->updateRequest['mediaType'],
            'location' => $this->updateRequest['location'],
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_update_nonPastMentoringEvent_400()
    {
        $this->updateRequest['startTime'] = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');
        $this->updateRequest['endTime'] = (new DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s');
        $this->update();
        $this->seeStatusCode(400);
    }
    public function test_update_nonDeclaredByParticipant_403()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inactiveParticipant_403()
    {
        $this->teamParticipant->participant->active = false;
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_inactiveMember_403()
    {
        $this->teamMember->active = false;
        $this->update();
        $this->seeStatusCode(403);
    }
    public function test_update_unamangedDeclaration_notOwned()
    {
        $program = $this->teamParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->declaredMentoringOne->participant = $participant;

        $this->update();
        $this->seeStatusCode(403);
    }

    protected function cancel()
    {
        parent::prepareRecord();

        $this->mentorOne->personnel->insert($this->connection);
        $this->mentorOne->insert($this->connection);

        $this->consultationSetupOne->participantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne_ff1->insert($this->connection);

        $this->declaredMentoringOne->insert($this->connection);

        $uri = $this->teamParticipantUri . "/declared-mentorings/{$this->declaredMentoringOne->id}/cancel";
        $this->patch($uri, [], $this->teamMember->client->token);
    }
    public function test_cancel_200()
    {
        $this->cancel();
        $this->seeStatusCode(200);

        $declaredMentoringRecord = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::CANCELLED,
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_cancel_nonDeclaredByParticipant_403()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_inactiveParticipant_403()
    {
        $this->teamParticipant->participant->active = false;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_inactiveMember_403()
    {
        $this->teamMember->active = false;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_unamangedDeclaration_notOwned()
    {
        $program = $this->teamParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->declaredMentoringOne->participant = $participant;

        $this->cancel();
        $this->seeStatusCode(403);
    }
    
    protected function approve()
    {
        parent::prepareRecord();

        $this->mentorOne->personnel->insert($this->connection);
        $this->mentorOne->insert($this->connection);

        $this->consultationSetupOne->participantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne_ff1->insert($this->connection);

        $this->declaredMentoringOne->insert($this->connection);

        $uri = $this->teamParticipantUri . "/declared-mentorings/{$this->declaredMentoringOne->id}/approve";
        $this->patch($uri, [], $this->teamMember->client->token);
    }
    public function test_approve_200()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $this->approve();
        $this->seeStatusCode(200);

        $declaredMentoringRecord = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_approve_nonDeclaredByParticipant_403()
    {
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_inactiveParticipant_403()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $this->teamParticipant->participant->active = false;
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_inactiveMember_403()
    {
        $this->teamMember->active = false;
        $this->approve();
        $this->seeStatusCode(403);
    }
    public function test_approve_unamangedDeclaration_notOwned()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $program = $this->teamParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->declaredMentoringOne->participant = $participant;

        $this->approve();
        $this->seeStatusCode(403);
    }

    protected function deny()
    {
        parent::prepareRecord();

        $this->mentorOne->personnel->insert($this->connection);
        $this->mentorOne->insert($this->connection);

        $this->consultationSetupOne->participantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne_ff1->insert($this->connection);

        $this->declaredMentoringOne->insert($this->connection);

        $uri = $this->teamParticipantUri . "/declared-mentorings/{$this->declaredMentoringOne->id}/deny";
        $this->patch($uri, [], $this->teamMember->client->token);
    }
    public function test_deny_200()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $this->deny();
        $this->seeStatusCode(200);

        $declaredMentoringRecord = [
            'id' => $this->declaredMentoringOne->id,
            'declaredStatus' => DeclaredMentoringStatus::DENIED_BY_PARTICIPANT,
        ];
        $this->seeInDatabase('DeclaredMentoring', $declaredMentoringRecord);
    }
    public function test_deny_nonDeclaredByParticipant_403()
    {
        $this->deny();
        $this->seeStatusCode(403);
    }
    public function test_deny_inactiveParticipant_403()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $this->teamParticipant->participant->active = false;
        $this->deny();
        $this->seeStatusCode(403);
    }
    public function test_deny_inactiveMember_403()
    {
        $this->teamMember->active = false;
        $this->deny();
        $this->seeStatusCode(403);
    }
    public function test_deny_unamangedDeclaration_notOwned()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DECLARED_BY_MENTOR;
        $program = $this->teamParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->declaredMentoringOne->participant = $participant;

        $this->deny();
        $this->seeStatusCode(403);
    }

    protected function submitReport()
    {
        parent::prepareRecord();

        $this->mentorOne->personnel->insert($this->connection);
        $this->mentorOne->insert($this->connection);

        $this->consultationSetupOne->participantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne_ff1->insert($this->connection);

        $this->declaredMentoringOne->insert($this->connection);

        $uri = $this->teamParticipantUri . "/declared-mentorings/{$this->declaredMentoringOne->id}/submit-report";
        $this->put($uri, $this->submitReportRequest, $this->teamMember->client->token);
    }
    public function test_submitReport_200()
    {
        $submitTime = (new DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->submitReport();
        $this->seeStatusCode(200);

        $participantReportRecord = [
            'mentorRating' => $this->submitReportRequest['mentorRating'],
            'Mentoring_id' => $this->declaredMentoringOne->mentoring->id,
            'id' => $this->declaredMentoringOne->mentoring->id,
            'FormRecord_id' => $this->declaredMentoringOne->mentoring->id,
        ];
        $this->seeInDatabase('ParticipantReport', $participantReportRecord);

        $formRecordRecord = [
            'Form_id' => $this->consultationSetupOne->participantFeedbackForm->form->id,
            'submitTime' => $submitTime,
            'id' => $this->declaredMentoringOne->mentoring->id,
        ];
        $this->seeInDatabase('FormRecord', $formRecordRecord);

        $stringFieldRecord = [
            'FormRecord_id' => $this->declaredMentoringOne->mentoring->id,
            'stringField_id' => $this->stringFieldOne_ff1->id,
            'value' => $this->submitReportRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecord);
    }
    public function test_submitReport_alreadySubmitted_updateExisting_200()
    {
        $this->participantReportOne_dm1->insert($this->connection);
        $this->stringFieldRecordOne_pr_dm1->insert($this->connection);

        $this->submitReport();
        $this->seeStatusCode(200);

        $participantReportRecord = [
            'id' => $this->participantReportOne_dm1->id,
            'mentorRating' => $this->submitReportRequest['mentorRating'],
        ];
        $this->seeInDatabase('ParticipantReport', $participantReportRecord);

        $stringFieldRecord = [
            'id' => $this->stringFieldRecordOne_pr_dm1->id,
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
    public function test_submitReport_unreportableDeclaration_deniedByParticipant()
    {
        $this->declaredMentoringOne->declaredStatus = DeclaredMentoringStatus::DENIED_BY_PARTICIPANT;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_inactiveParticipant_403()
    {
        $this->teamParticipant->participant->active = false;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_inactiveMember_403()
    {
        $this->teamMember->active = false;
        $this->submitReport();
        $this->seeStatusCode(403);
    }
    public function test_submitReport_unamangedDeclaration_notOwned()
    {
        $program = $this->teamParticipant->participant->program;
        $participant = new RecordOfParticipant($program, 'zzz');
        $participant->insert($this->connection);
        $this->declaredMentoringOne->participant = $participant;

        $this->submitReport();
        $this->seeStatusCode(403);
    }

    protected function show()
    {
        parent::prepareRecord();

        $this->mentorOne->personnel->insert($this->connection);
        $this->mentorOne->insert($this->connection);

        $this->consultationSetupOne->participantFeedbackForm->insert($this->connection);
        $this->consultationSetupOne->insert($this->connection);
        $this->stringFieldOne_ff1->insert($this->connection);

        $this->declaredMentoringOne->insert($this->connection);
        $this->participantReportOne_dm1->insert($this->connection);
        $this->stringFieldRecordOne_pr_dm1->insert($this->connection);

        $uri = $this->teamParticipantUri . "/declared-mentorings/{$this->declaredMentoringOne->id}";
        $this->get($uri, $this->teamMember->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);

        $response = [
            'id' => $this->declaredMentoringOne->id,
            'startTime' => $this->declaredMentoringOne->startTime,
            'endTime' => $this->declaredMentoringOne->endTime,
            'mediaType' => $this->declaredMentoringOne->mediaType,
            'location' => $this->declaredMentoringOne->location,
            'declaredStatus' => DeclaredMentoringStatus::DISPLAY_VALUES[$this->declaredMentoringOne->declaredStatus],
            'consultationSetup' => [
                'id' => $this->declaredMentoringOne->consultationSetup->id,
                'name' => $this->declaredMentoringOne->consultationSetup->name,
                'participantFeedbackForm' => [
                    'name' => $this->consultationSetupOne->participantFeedbackForm->form->name,
                    'description' => $this->consultationSetupOne->participantFeedbackForm->form->description,
                    'stringFields' => [
                        [
                            "id" => $this->stringFieldOne_ff1->id,
                            "name" => $this->stringFieldOne_ff1->name,
                            "description" => $this->stringFieldOne_ff1->description,
                            "position" => $this->stringFieldOne_ff1->position,
                            "mandatory" => $this->stringFieldOne_ff1->mandatory,
                            "defaultValue" => $this->stringFieldOne_ff1->defaultValue,
                            "minValue" => $this->stringFieldOne_ff1->minValue,
                            "maxValue" => $this->stringFieldOne_ff1->maxValue,
                            "placeholder" => $this->stringFieldOne_ff1->placeholder,
                        ],
                    ],
                    'integerFields' => [],
                    'textAreaFields' => [],
                    'attachmentFields' => [],
                    'singleSelectFields' => [],
                    'multiSelectFields' => [],
                ],
            ],
            'mentor' => [
                'id' => $this->declaredMentoringOne->mentor->id,
                'personnel' => [
                    'id' => $this->declaredMentoringOne->mentor->personnel->id,
                    'name' => $this->declaredMentoringOne->mentor->personnel->getFullName(),
                ],
            ],
            'participantReport' => [
                'mentorRating' => $this->participantReportOne_dm1->mentorRating,
                'submitTime' => $this->participantReportOne_dm1->formRecord->submitTime,
                'stringFieldRecords' => [
                    [
                        "id" => $this->stringFieldRecordOne_pr_dm1->id,
                        "stringField" => [
                            "id" => $this->stringFieldRecordOne_pr_dm1->stringField->id,
                            "name" => $this->stringFieldRecordOne_pr_dm1->stringField->name,
                            "position" => $this->stringFieldRecordOne_pr_dm1->stringField->position,
                        ],
                        "value" => $this->stringFieldRecordOne_pr_dm1->value,
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
