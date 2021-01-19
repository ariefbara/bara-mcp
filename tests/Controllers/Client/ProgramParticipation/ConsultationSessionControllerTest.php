<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\ {
    Client\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfParticipantFeedback,
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfConsultationSetup,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Shared\Form\RecordOfStringField,
    RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord,
    RecordPreparation\Shared\RecordOfForm,
    RecordPreparation\Shared\RecordOfFormRecord
};

class ConsultationSessionControllerTest extends ProgramParticipationTestCase
{
    protected $consultationSessionUri;
    protected $consultationSession;
    protected $consultationSessionOne;
    protected $participantFeedbackOne;
    protected $participantFeedbackInput;
    protected $stringFieldRecord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationSessionUri = $this->programParticipationUri . "/{$this->programParticipation->id}/consultation-sessions";
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantFeedback')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();

        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;

        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());

        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());

        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());

        $personnel = new RecordOfPersonnel($firm, 0, "personnel@email.org", 'password123');
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());

        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());

        $this->consultationSession = new RecordOfConsultationSession(
                $consultationSetup, $this->programParticipation->participant, $consultant, 0);
        $this->consultationSession->startDateTime = (new DateTimeImmutable('+48 hours'))->format('Y-m-d H:i:s');
        $this->consultationSession->endDateTime = (new DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionOne = new RecordOfConsultationSession(
                $consultationSetup, $this->programParticipation->participant, $consultant, 1);
        $this->consultationSessionOne->startDateTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionOne->endDateTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        $this->connection->table("ConsultationSession")->insert($this->consultationSession->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($this->consultationSessionOne->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($form, 1);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());

        $stringField = new RecordOfStringField($form, 1);
        $this->connection->table('StringField')->insert($stringField->toArrayForDbEntry());

        $this->stringFieldRecord = new RecordOfStringFieldRecord($formRecord, $stringField, 0);
        $this->connection->table('StringFieldRecord')->insert($this->stringFieldRecord->toArrayForDbEntry());

        $this->participantFeedbackOne = new RecordOfParticipantFeedback($this->consultationSessionOne, $formRecord);
        $this->connection->table('ParticipantFeedback')->insert($this->participantFeedbackOne->toArrayForDbEntry());

        $this->participantFeedbackInput = [
            "mentorRating" => 3,
            "stringFieldRecords" => [
                [
                    "fieldId" => $stringField->id,
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
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantFeedback')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->consultationSession->id,
            "startTime" => $this->consultationSession->startDateTime,
            "endTime" => $this->consultationSession->endDateTime,
            "media" => $this->consultationSession->media,
            "address" => $this->consultationSession->address,
            "consultant" => [
                "id" => $this->consultationSession->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSession->consultant->personnel->id,
                    "name" => $this->consultationSession->consultant->personnel->getFullName(),
                ],
            ],
            "participantFeedback" => null,
        ];
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}";
        $this->get($uri, $this->client->token)
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
                    "hasParticipantFeedback" => false,
                    "consultationSetup" => [
                        "id" => $this->consultationSession->consultationSetup->id,
                        "name" => $this->consultationSession->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationSession->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationSession->consultant->personnel->id,
                            "name" => $this->consultationSession->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
                [
                    "id" => $this->consultationSessionOne->id,
                    "startTime" => $this->consultationSessionOne->startDateTime,
                    "endTime" => $this->consultationSessionOne->endDateTime,
                    "media" => $this->consultationSessionOne->media,
                    "address" => $this->consultationSessionOne->address,
                    "hasParticipantFeedback" => true,
                    "consultationSetup" => [
                        "id" => $this->consultationSessionOne->consultationSetup->id,
                        "name" => $this->consultationSessionOne->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationSessionOne->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationSessionOne->consultant->personnel->id,
                            "name" => $this->consultationSessionOne->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
            ],
        ];
        $this->get($this->consultationSessionUri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_maxEndTimeAndParticipantFeedbackSetFilter()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->consultationSessionOne->id,
        ];
        $maxEndTimeString = (new DateTime())->format('Y-m-d H:i:s');
        $uri = $this->consultationSessionUri
                . "?maxEndTime=$maxEndTimeString"
                . "&containParticipantFeedback=true";

        $this->get($uri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse);
    }

    public function test_submitReport_200()
    {
        $this->connection->table("StringField")->truncate();
        $this->connection->table("StringFieldRecord")->truncate();
        $this->participantFeedbackInput["stringFieldRecords"] = [];
        
        $response = [
            "id" => $this->consultationSession->id,
            "startTime" => $this->consultationSession->startDateTime,
            "endTime" => $this->consultationSession->endDateTime,
            "consultationSetup" => [
                "id" => $this->consultationSession->consultationSetup->id,
                "name" => $this->consultationSession->consultationSetup->name,
                "participantFeedbackForm" => [
                    "id" => $this->consultationSession->consultationSetup->consultantFeedbackForm->id,
                    "name" => $this->consultationSession->consultationSetup->consultantFeedbackForm->form->name,
                    "description" => $this->consultationSession->consultationSetup->consultantFeedbackForm->form->description,
                    "stringFields" => [],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "consultant" => [
                "id" => $this->consultationSession->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSession->consultant->personnel->id,
                    "name" => $this->consultationSession->consultant->personnel->getFullName(),
                ],
            ],
            "participantFeedback" => [
                "mentorRating" => $this->participantFeedbackInput["mentorRating"],
                "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
                "stringFieldRecords" => [],
                "integerFieldRecords" => [],
                "textAreaFieldRecords" => [],
                "attachmentFieldRecords" => [],
                "singleSelectFieldRecords" => [],
                "multiSelectFieldRecords" => [],
            ],
        ];
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}/submit-report";
        $this->put($uri, $this->participantFeedbackInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $participantFeedbackEntry = [
            "ConsultationSession_id" => $this->consultationSession->id,
            "mentorRating" => $this->participantFeedbackInput["mentorRating"],
        ];
        $this->seeInDatabase("ParticipantFeedback", $participantFeedbackEntry);

        $formRecordEntry = [
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("FormRecord", $formRecordEntry);
    }
    public function test_submitReport_consultationSessionAlreadyHasParticipantFeedback_updateExistingParticipantFeedback()
    {
        $response = [
            "id" => $this->consultationSessionOne->id,
            "startTime" => $this->consultationSessionOne->startDateTime,
            "endTime" => $this->consultationSessionOne->endDateTime,
            "consultationSetup" => [
                "id" => $this->consultationSessionOne->consultationSetup->id,
                "name" => $this->consultationSessionOne->consultationSetup->name,
                "participantFeedbackForm" => [
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
            "consultant" => [
                "id" => $this->consultationSessionOne->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSessionOne->consultant->personnel->id,
                    "name" => $this->consultationSessionOne->consultant->personnel->getFullName(),
                ],
            ],
            "participantFeedback" => [
                "mentorRating" => $this->participantFeedbackInput["mentorRating"],
                "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
                "stringFieldRecords" => [
                    [
                        "id" => $this->stringFieldRecord->id,
                        "stringField" => [
                            "id" => $this->stringFieldRecord->stringField->id,
                            "name" => $this->stringFieldRecord->stringField->name,
                            "position" => $this->stringFieldRecord->stringField->position,
                        ],
                        "value" => $this->participantFeedbackInput['stringFieldRecords'][0]['value'],
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
        $this->put($uri, $this->participantFeedbackInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $stringFieldRecordEntry = [
            "id" => $this->stringFieldRecord->id,
            "value" => $this->participantFeedbackInput['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordEntry);
    }
    public function test_submitReport_logActivity()
    {
        $uri = $this->consultationSessionUri . "/{$this->consultationSessionOne->id}/submit-report";
        $this->put($uri, $this->participantFeedbackInput, $this->client->token)
                ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "participant submitted consultation report",
            "occuredTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultationSessionActivityLogEntry = [
            "ConsultationSession_id" => $this->consultationSessionOne->id,
        ];
        $this->seeInDatabase("ConsultationSessionActivityLog", $consultationSessionActivityLogEntry);
    }
}
