<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTime;
use DateTimeImmutable;
use SharedContext\Domain\ValueObject\ConsultationSessionType;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfConsultantFeedback;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfParticipantFeedback;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ConsultationSessionControllerTest extends ProgramConsultationTestCase
{

    protected $consultationSessionUri;
    protected $userParticipant;
    protected $consultationSession;
    protected $consultationSessionOne;
    protected $consultantFeedback;
    protected $consultantFeedbackInput;
    protected $stringField;
    protected $stringFieldRecord;
    protected $consultationSetup;
    protected $participant;
    protected $declareConsultationSessionRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionUri = $this->programConsultationUri . "/consultation-sessions";
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('IntegerField')->truncate();
        $this->connection->table('TextAreaField')->truncate();
        $this->connection->table('AttachmentField')->truncate();
        $this->connection->table('SingleSelectField')->truncate();
        $this->connection->table('MultiSelectField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();

        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();

        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ConsultantFeedback')->truncate();
        $this->connection->table('ParticipantFeedback')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('IntegerFieldRecord')->truncate();
        $this->connection->table('TextAreaFieldRecord')->truncate();
        $this->connection->table('AttachmentFieldRecord')->truncate();
        $this->connection->table('SingleSelectFieldRecord')->truncate();
        $this->connection->table('MultiSelectFieldRecord')->truncate();
        
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
        $this->connection->table('ConsultantActivityLog')->truncate();

        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());

        $feedbackForm = new RecordOfFeedbackForm($this->programConsultation->program->firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());

        $this->consultationSetup = new RecordOfConsultationSetup($this->programConsultation->program, $feedbackForm,
                $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($this->consultationSetup->toArrayForDbEntry());

        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());

        $this->participant = new RecordOfParticipant($this->programConsultation->program, 0);
        $this->connection->table("Participant")->insert($this->participant->toArrayForDbEntry());

        $this->userParticipant = new RecordOfUserParticipant($user, $this->participant);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());

        $this->consultationSession = new RecordOfConsultationSession(
                $this->consultationSetup, $this->participant, $this->programConsultation, 0);
        $this->consultationSession->startDateTime = (new DateTimeImmutable('-6 hours'))->format('Y-m-d H:i:s');
        $this->consultationSession->endDateTime = (new DateTimeImmutable('-4 hours'))->format('Y-m-d H:i:s');
        $this->consultationSession->sessionType = ConsultationSessionType::DECLARED_TYPE;

        $this->consultationSessionOne = new RecordOfConsultationSession(
                $this->consultationSetup, $this->participant, $this->programConsultation, 1);
        $this->consultationSessionOne->startDateTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionOne->endDateTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');

        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSessionOne->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($feedbackForm->form, 0);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());

        $this->stringField = new RecordOfStringField($form, 0);
        $this->connection->table('StringField')->insert($this->stringField->toArrayForDbEntry());

        $this->stringFieldRecord = new RecordOfStringFieldRecord($formRecord, $this->stringField, 0);
        $this->connection->table('StringFieldRecord')->insert($this->stringFieldRecord->toArrayForDbEntry());

        $this->consultantFeedback = new RecordOfConsultantFeedback($this->consultationSessionOne, $formRecord);
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedback->toArrayForDbEntry());

        $participantFeedback = new RecordOfParticipantFeedback($this->consultationSessionOne, $formRecord);
        $this->connection->table('ParticipantFeedback')->insert($participantFeedback->toArrayForDbEntry());

        $this->consultantFeedbackInput = [
            "participantRating" => 4,
            "stringFieldRecords" => [
                [
                    "fieldId" => $this->stringField->id,
                    "value" => "new string value",
                ],
            ],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        
        $this->declareConsultationSessionRequest = [
            'consultationSetupId' => $this->consultationSetup->id,
            'participantId' => $this->participant->id,
            'startTime' => (new \DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s'),
            'endTime' => (new \DateTimeImmutable('+25 hours'))->format('Y-m-d H:i:s'),
            'media' => 'new media',
            'address' => 'new address',
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
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('FeedbackForm')->truncate();

        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('UserParticipant')->truncate();

        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ConsultantFeedback')->truncate();
        $this->connection->table('ParticipantFeedback')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('ConsultationRequestActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
        $this->connection->table('ConsultantActivityLog')->truncate();
    }

    public function test_show()
    {
        $response = [
            "id" => $this->consultationSession->id,
            "startTime" => $this->consultationSession->startDateTime,
            "endTime" => $this->consultationSession->endDateTime,
            "media" => $this->consultationSession->media,
            "address" => $this->consultationSession->address,
            "consultationSetup" => [
                "id" => $this->consultationSession->consultationSetup->id,
                "name" => $this->consultationSession->consultationSetup->name,
                "consultantFeedbackForm" => [
                    "id" => $this->consultationSession->consultationSetup->consultantFeedbackForm->id,
                    "name" => $this->consultationSession->consultationSetup->consultantFeedbackForm->form->name,
                    "description" => $this->consultationSession->consultationSetup->consultantFeedbackForm->form->description,
                    "stringFields" => [
                        [
                            'id' => $this->stringField->id,
                            'name' => $this->stringField->name,
                            'description' => $this->stringField->description,
                            'placeholder' => $this->stringField->placeholder,
                            'mandatory' => $this->stringField->mandatory,
                            'defaultValue' => $this->stringField->defaultValue,
                            'maxValue' => $this->stringField->maxValue,
                            'minValue' => $this->stringField->minValue,
                            'position' => $this->stringField->position,
                        ],
                    ],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "participant" => [
                "id" => $this->consultationSession->participant->id,
                "client" => null,
                "user" => [
                    "id" => $this->userParticipant->user->id,
                    "name" => $this->userParticipant->user->getFullName(),
                ],
                "team" => null,
            ],
            "consultantFeedback" => null,
        ];
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}";
        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultationSession->id,
                    "startTime" => $this->consultationSession->startDateTime,
                    "endTime" => $this->consultationSession->endDateTime,
                    "media" => $this->consultationSession->media,
                    "address" => $this->consultationSession->address,
                    "sessionType" => 'DECLARED',
                    "approvedByMentor" => null,
                    'cancelled' => false,
                    "hasConsultantFeedback" => false,
                    "participant" => [
                        "id" => $this->consultationSession->participant->id,
                        "client" => null,
                        "user" => [
                            "id" => $this->userParticipant->user->id,
                            "name" => $this->userParticipant->user->getFullName(),
                        ],
                        "team" => null
                    ],
                ],
                [
                    "id" => $this->consultationSessionOne->id,
                    "startTime" => $this->consultationSessionOne->startDateTime,
                    "endTime" => $this->consultationSessionOne->endDateTime,
                    "media" => $this->consultationSessionOne->media,
                    "address" => $this->consultationSessionOne->address,
                    "sessionType" => 'HANDSHAKING',
                    "approvedByMentor" => null,
                    'cancelled' => false,
                    "hasConsultantFeedback" => true,
                    "participant" => [
                        "id" => $this->consultationSessionOne->participant->id,
                        "client" => null,
                        "user" => [
                            "id" => $this->userParticipant->user->id,
                            "name" => $this->userParticipant->user->getFullName(),
                        ],
                        "team" => null,
                    ],
                ],
            ],
        ];
        $this->get($this->consultationSessionUri, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_hasMinStartTimeFilter()
    {
        $response = [
            "total" => 1,
        ];
        $consultationSessionResponse = [
            "id" => $this->consultationSession->id
        ];
        $minStartTimeString = (new DateTime("-12 hours"))->format('Y-m-d H:i:s');
        $uri = $this->consultationSessionUri
                . "?minStartTime=$minStartTimeString";

        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeJsonContains($consultationSessionResponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_maxStartTimeAndConsultantFeedbackSetFilter()
    {
        $response = [
            "total" => 1,
        ];
        $consultationSessionResponse = [
            "id" => $this->consultationSessionOne->id
        ];
        $maxEndTimeString = (new DateTime())->format('Y-m-d H:i:s');
        $uri = $this->consultationSessionUri
                . "?maxEndTime=$maxEndTimeString"
                . "&containConsultantFeedback=true";

        $this->get($uri, $this->personnel->token)
                ->seeJsonContains($response)
                ->seeJsonContains($consultationSessionResponse)
                ->seeStatusCode(200);
    }

    public function test_submitReport()
    {
$this->disableExceptionHandling();
        $this->connection->table('ConsultantFeedback')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();

        $response = [
            "stringField" => [
                "id" => $this->stringField->id,
                "name" => $this->stringField->name,
                "position" => $this->stringField->position,
            ],
            "value" => $this->consultantFeedbackInput['stringFieldRecords'][0]['value'],
        ];
        $ratingResponse = [
            "participantRating" => $this->consultantFeedbackInput["participantRating"],
        ];
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}/submit-report";
        $this->put($uri, $this->consultantFeedbackInput, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $consultantFeedbackEntry = [
            "ConsultationSession_id" => $this->consultationSession->id,
            "participantRating" => $this->consultantFeedbackInput["participantRating"],
        ];
        $this->seeInDatabase("ConsultantFeedback", $consultantFeedbackEntry);

        $formRecordEntry = [
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("FormRecord", $formRecordEntry);

        $stringFieldRecordEntry = [
            "StringField_id" => $this->stringField->id,
            "value" => $this->consultantFeedbackInput['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordEntry);
    }
    public function test_submitReport_consultationSessionAlreadyHasConsultantFeedback_updateExistingConsultantFeedback()
    {
        $response = [
            "id" => $this->consultationSessionOne->id,
            "startTime" => $this->consultationSessionOne->startDateTime,
            "endTime" => $this->consultationSessionOne->endDateTime,
            "consultationSetup" => [
                "id" => $this->consultationSessionOne->consultationSetup->id,
                "name" => $this->consultationSessionOne->consultationSetup->name,
                "consultantFeedbackForm" => [
                    "id" => $this->consultationSessionOne->consultationSetup->consultantFeedbackForm->id,
                    "name" => $this->consultationSessionOne->consultationSetup->consultantFeedbackForm->form->name,
                    "description" => $this->consultationSessionOne->consultationSetup->consultantFeedbackForm->form->description,
                    "stringFields" => [
                        [
                            "id" => $this->stringFieldRecord->stringField->id,
                            "name" => $this->stringFieldRecord->stringField->name,
                            "description" => $this->stringFieldRecord->stringField->description,
                            "position" => $this->stringFieldRecord->stringField->position,
                            "mandatory" => $this->stringFieldRecord->stringField->mandatory,
                            "defaultValue" => $this->stringFieldRecord->stringField->defaultValue,
                            "minValue" => $this->stringFieldRecord->stringField->minValue,
                            "maxValue" => $this->stringFieldRecord->stringField->maxValue,
                            "placeholder" => $this->stringFieldRecord->stringField->placeholder,
                        ],
                    ],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "participant" => [
                "id" => $this->consultationSessionOne->participant->id,
                "client" => null,
                "user" => [
                    "id" => $this->userParticipant->user->id,
                    "name" => $this->userParticipant->user->getFullName(),
                ],
                "team" => null,
            ],
            "consultantFeedback" => [
                "participantRating" => $this->consultantFeedbackInput["participantRating"],
                "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
                "stringFieldRecords" => [
                    [
                        "id" => $this->stringFieldRecord->id,
                        "stringField" => [
                            "id" => $this->stringFieldRecord->stringField->id,
                            "name" => $this->stringFieldRecord->stringField->name,
                            "position" => $this->stringFieldRecord->stringField->position,
                        ],
                        "value" => $this->consultantFeedbackInput['stringFieldRecords'][0]['value'],
                    ],
                ],
                "integerFieldRecords" => [],
                "textAreaFieldRecords" => [],
                "attachmentFieldRecords" => [],
                "singleSelectFieldRecords" => [],
                "multiSelectFieldRecords" => [],
            ],
        ];

        $uri = $this->consultationSessionUri . "/{$this->consultationSessionOne->id}/submit-report";
        $this->put($uri, $this->consultantFeedbackInput, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $stringFieldRecordEntry = [
            "id" => $this->stringFieldRecord->id,
            "value" => $this->consultantFeedbackInput['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordEntry);
    }
    public function test_submitReport_logActivity()
    {
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}/submit-report";
        $this->put($uri, $this->consultantFeedbackInput, $this->personnel->token)
                ->seeStatusCode(200);
        $activityLogEntry = [
            "message" => "consultant report submitted",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultationSessionActivityLogEntry = [
            "ConsultationSession_id" => $this->consultationSession->id,
        ];
        $this->seeInDatabase("ConsultationSessionActivityLog", $consultationSessionActivityLogEntry);
        
        $consultantActivityLog = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantActivityLog", $consultantActivityLog);
    }
    
    public function declare()
    {
        $this->post($this->consultationSessionUri, $this->declareConsultationSessionRequest, $this->personnel->token);
    }
    public function test_declare_201()
    {
        $this->declare();
        $this->seeStatusCode(201);
        
        $response = [
            "startTime" => $this->declareConsultationSessionRequest['startTime'],
            "endTime" => $this->declareConsultationSessionRequest['endTime'],
            "media" => $this->declareConsultationSessionRequest['media'],
            "address" => $this->declareConsultationSessionRequest['address'],
            "sessionType" => 'DECLARED',
            "approvedByMentor" => true,
            "consultationSetup" => [
                "id" => $this->consultationSession->consultationSetup->id,
                "name" => $this->consultationSession->consultationSetup->name,
                "consultantFeedbackForm" => [
                    "id" => $this->consultationSession->consultationSetup->consultantFeedbackForm->id,
                    "name" => $this->consultationSession->consultationSetup->consultantFeedbackForm->form->name,
                    "description" => $this->consultationSession->consultationSetup->consultantFeedbackForm->form->description,
                    "stringFields" => [
                        [
                            'id' => $this->stringField->id,
                            'name' => $this->stringField->name,
                            'description' => $this->stringField->description,
                            'placeholder' => $this->stringField->placeholder,
                            'mandatory' => $this->stringField->mandatory,
                            'defaultValue' => $this->stringField->defaultValue,
                            'maxValue' => $this->stringField->maxValue,
                            'minValue' => $this->stringField->minValue,
                            'position' => $this->stringField->position,
                        ],
                    ],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "participant" => [
                "id" => $this->consultationSession->participant->id,
                "client" => null,
                "user" => [
                    "id" => $this->userParticipant->user->id,
                    "name" => $this->userParticipant->user->getFullName(),
                ],
                "team" => null,
            ],
            "consultantFeedback" => null,
        ];
        $this->seeJsonContains($response);
        
        $record = [
            "startDateTime" => $this->declareConsultationSessionRequest['startTime'],
            "endDateTime" => $this->declareConsultationSessionRequest['endTime'],
            "media" => $this->declareConsultationSessionRequest['media'],
            "address" => $this->declareConsultationSessionRequest['address'],
            "sessionType" => ConsultationSessionType::DECLARED_TYPE,
            "approvedByMentor" => true,
            'ConsultationSetup_id' => $this->consultationSetup->id,
            'participant_id' => $this->participant->id,
        ];
        $this->seeInDatabase('ConsultationSession', $record);
    }
    public function test_declare_declareUpcomingSession_403()
    {
        $this->declareConsultationSessionRequest['startTime'] = (new DateTime('+24 hours'))->format('Y-m-d H:i:s');
        $this->declareConsultationSessionRequest['endTime'] = (new DateTime('+25 hours'))->format('Y-m-d H:i:s');
        
        $this->declare();
        $this->seeStatusCode(403);
    }
    
    protected function cancel()
    {
        $this->connection->table('ConsultationSession')->truncate();
        $this->consultationSession->insert($this->connection);
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}/cancel";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_cancel_200()
    {
        $this->cancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->consultationSession->id,
            'cancelled' => true,
        ];
        $this->seeJsonContains($response);
        
        $record = [
            'id' => $this->consultationSession->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('ConsultationSession', $record);
    }
    public function test_cancel_nonDeclaredType_403()
    {
        $this->consultationSession->sessionType = ConsultationSessionType::HANDSHAKING_TYPE;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_alreadyCancelled_403()
    {
        $this->consultationSession->cancelled = true;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    
    protected function deny()
    {
        $this->connection->table('ConsultationSession')->truncate();
        $this->consultationSession->insert($this->connection);
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}/deny";
        $this->patch($uri, [], $this->personnel->token);
    }
    public function test_deny_200()
    {
        $this->deny();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->consultationSession->id,
            'approvedByMentor' => false,
            'cancelled' => true,
        ];
        $this->seeJsonContains($response);
        
        $record = [
            'id' => $this->consultationSession->id,
            'approvedByMentor' => false,
            'cancelled' => true,
        ];
        $this->seeInDatabase('ConsultationSession', $record);
    }
    public function test_deny_nonDeclaredSession_403()
    {
        $this->consultationSession->sessionType = ConsultationSessionType::HANDSHAKING_TYPE;
        $this->deny();
        $this->seeStatusCode(403);
    }
    public function test_deny_alreadyApproved_403()
    {
        $this->consultationSession->approvedByMentor = true;
        $this->deny();
        $this->seeStatusCode(403);
    }
    public function test_deny_alreadyDenied_403()
    {
        $this->consultationSession->approvedByMentor = false;
        $this->deny();
        $this->seeStatusCode(403);
    }
    public function test_deny_cancelledSession_403()
    {
        $this->consultationSession->cancelled = true;
        $this->deny();
        $this->seeStatusCode(403);
    }

}
