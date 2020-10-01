<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\ConsultationSession\RecordOfConsultantFeedback,
    Firm\Program\Participant\ConsultationSession\RecordOfParticipantFeedback,
    Firm\Program\Participant\RecordOfConsultationSession,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfFeedbackForm,
    RecordOfUser,
    Shared\Form\RecordOfStringField,
    Shared\FormRecord\RecordOfStringFieldRecord,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord,
    User\RecordOfUserParticipant
};

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
        $this->connection->table('StringField')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();

        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());

        $feedbackForm = new RecordOfFeedbackForm($this->programConsultation->program->firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());

        $consultationSetup = new RecordOfConsultationSetup($this->programConsultation->program, $feedbackForm,
                $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());

        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());

        $participant = new RecordOfParticipant($this->programConsultation->program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());

        $this->userParticipant = new RecordOfUserParticipant($user, $participant);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());

        $this->consultationSession = new RecordOfConsultationSession(
                $consultationSetup, $participant, $this->programConsultation, 0);
        $this->consultationSession->startDateTime = (new DateTimeImmutable('+48 hours'))->format('Y-m-d H:i:s');
        $this->consultationSession->endDateTime = (new DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');

        $this->consultationSessionOne = new RecordOfConsultationSession(
                $consultationSetup, $participant, $this->programConsultation, 1);
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
    }

    public function test_show()
    {
        $response = [
            "id" => $this->consultationSession->id,
            "startTime" => $this->consultationSession->startDateTime,
            "endTime" => $this->consultationSession->endDateTime,
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
                "clientParticipant" => null,
                "userParticipant" => [
                    "id" => $this->userParticipant->user->id,
                    "name" => $this->userParticipant->user->getFullName(),
                ],
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
                    "hasConsultantFeedback" => false,
                    "participant" => [
                        "id" => $this->consultationSession->participant->id,
                        "clientParticipant" => null,
                        "userParticipant" => [
                            "id" => $this->userParticipant->user->id,
                            "name" => $this->userParticipant->user->getFullName(),
                        ],
                    ],
                ],
                [
                    "id" => $this->consultationSessionOne->id,
                    "startTime" => $this->consultationSessionOne->startDateTime,
                    "endTime" => $this->consultationSessionOne->endDateTime,
                    "hasConsultantFeedback" => true,
                    "participant" => [
                        "id" => $this->consultationSessionOne->participant->id,
                        "clientParticipant" => null,
                        "userParticipant" => [
                            "id" => $this->userParticipant->user->id,
                            "name" => $this->userParticipant->user->getFullName(),
                        ],
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
            "list" => [
                [
                    "id" => $this->consultationSession->id,
                    "startTime" => $this->consultationSession->startDateTime,
                    "endTime" => $this->consultationSession->endDateTime,
                    "hasConsultantFeedback" => false,
                    "participant" => [
                        "id" => $this->consultationSession->participant->id,
                        "clientParticipant" => null,
                        "userParticipant" => [
                            "id" => $this->userParticipant->user->id,
                            "name" => $this->userParticipant->user->getFullName(),
                        ],
                    ],
                ],
            ],
        ];
        $minStartTimeString = (new DateTime())->format('Y-m-d H:i:s');
        $uri = $this->consultationSessionUri
                . "?minStartTime=$minStartTimeString";

        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_maxStartTimeAndConsultantFeedbackSetFilter()
    {
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->consultationSessionOne->id,
                    "startTime" => $this->consultationSessionOne->startDateTime,
                    "endTime" => $this->consultationSessionOne->endDateTime,
                    "hasConsultantFeedback" => true,
                    "participant" => [
                        "id" => $this->consultationSessionOne->participant->id,
                        "clientParticipant" => null,
                        "userParticipant" => [
                            "id" => $this->userParticipant->user->id,
                            "name" => $this->userParticipant->user->getFullName(),
                        ],
                    ],
                ],
            ],
        ];
        $maxStartTimeString = (new DateTime())->format('Y-m-d H:i:s');
        $uri = $this->consultationSessionUri
                . "?maxStartTime=$maxStartTimeString"
                . "&containConsultantFeedback=true";

        $this->get($uri, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_setConsultantFeedback()
    {
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
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}/consultant-feedback";
        $this->put($uri, $this->consultantFeedbackInput, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $consultantFeedbackEntry = [
            "ConsultationSession_id" => $this->consultationSession->id,
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

    public function test_setConsultantFeedback_consultationSessionAlreadyHasConsultantFeedback_updateExistingConsultantFeedback()
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
                "clientParticipant" => null,
                "userParticipant" => [
                    "id" => $this->userParticipant->user->id,
                    "name" => $this->userParticipant->user->getFullName(),
                ],
            ],
            "consultantFeedback" => [
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

        $uri = $this->consultationSessionUri . "/{$this->consultationSessionOne->id}/consultant-feedback";
        $this->put($uri, $this->consultantFeedbackInput, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $stringFieldRecordEntry = [
            "id" => $this->stringFieldRecord->id,
            "value" => $this->consultantFeedbackInput['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordEntry);
    }

}
