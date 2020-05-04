<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTime;
use Tests\Controllers\RecordPreparation\{
    Firm\Program\Participant\ConsultationSession\RecordOfParticipantFeedback,
    Firm\Program\Participant\RecordOfConsultationSession,
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfConsultationSetup,
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfPersonnel,
    Shared\Form\RecordOfStringField,
    Shared\FormRecord\RecordOfStringFieldRecord,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class ConsultationSessionControllerTest extends ConsultationSessionTestCase
{

    protected $consultationSessionOne;
    protected $participantFeedbackOne;
    protected $participantFeedbackInput;
    protected $stringFieldRecord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ParticipantFeedback')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();

        $form = new RecordOfForm(1);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());

        $feedbackForm = new RecordOfFeedbackForm($this->programParticipation->program->firm,
                $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());

        $consultationSetup = new RecordOfConsultationSetup($this->programParticipation->program,
                $feedbackForm, $feedbackForm, 1);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());

        $personnel = new RecordOfPersonnel($this->programParticipation->program->firm, 1, "personnel_1@email.org",
                'password123');
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());


        $consultant = new RecordOfConsultant($this->programParticipation->program, $personnel, 1);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());

        $this->consultationSessionOne = new RecordOfConsultationSession($consultationSetup, $this->programParticipation,
                $consultant, 1);
        $this->connection->table("ConsultationSession")->insert($this->consultationSessionOne->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($feedbackForm->form, 1);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());

        $stringField = new RecordOfStringField($form, 1);
        $this->connection->table('StringField')->insert($stringField->toArrayForDbEntry());

        $this->stringFieldRecord = new RecordOfStringFieldRecord($formRecord, $stringField, 0);
        $this->connection->table('StringFieldRecord')->insert($this->stringFieldRecord->toArrayForDbEntry());

        $this->participantFeedbackOne = new RecordOfParticipantFeedback($this->consultationSessionOne, $formRecord);
        $this->connection->table('ParticipantFeedback')->insert($this->participantFeedbackOne->toArrayForDbEntry());

        $this->participantFeedbackInput = [
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
        $this->connection->table('FormRecord')->truncate();
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
            ],
            "consultant" => [
                "id" => $this->consultationSession->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSession->consultant->personnel->id,
                    "name" => $this->consultationSession->consultant->personnel->name,
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
                    "consultationSetup" => [
                        "id" => $this->consultationSession->consultationSetup->id,
                        "name" => $this->consultationSession->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationSession->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationSession->consultant->personnel->id,
                            "name" => $this->consultationSession->consultant->personnel->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->consultationSessionOne->id,
                    "startTime" => $this->consultationSessionOne->startDateTime,
                    "endTime" => $this->consultationSessionOne->endDateTime,
                    "consultationSetup" => [
                        "id" => $this->consultationSessionOne->consultationSetup->id,
                        "name" => $this->consultationSessionOne->consultationSetup->name,
                    ],
                    "consultant" => [
                        "id" => $this->consultationSessionOne->consultant->id,
                        "personnel" => [
                            "id" => $this->consultationSessionOne->consultant->personnel->id,
                            "name" => $this->consultationSessionOne->consultant->personnel->name,
                        ],
                    ],
                ],
            ],
        ];
        $this->get($this->consultationSessionUri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_setParticipantFeedback()
    {
        $this->participantFeedbackInput["stringFieldRecords"] = [];
        $response = [
            "id" => $this->consultationSession->id,
            "startTime" => $this->consultationSession->startDateTime,
            "endTime" => $this->consultationSession->endDateTime,
            "consultationSetup" => [
                "id" => $this->consultationSession->consultationSetup->id,
                "name" => $this->consultationSession->consultationSetup->name,
            ],
            "consultant" => [
                "id" => $this->consultationSession->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSession->consultant->personnel->id,
                    "name" => $this->consultationSession->consultant->personnel->name,
                ],
            ],
            "participantFeedback" => [
                "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
                "stringFieldRecords" => [],
                "integerFieldRecords" => [],
                "textAreaFieldRecords" => [],
                "attachmentFieldRecords" => [],
                "singleSelectFieldRecords" => [],
                "multiSelectFieldRecords" => [],
            ],
        ];
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}/participant-feedback";
        $this->put($uri, $this->participantFeedbackInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);

        $participantFeedbackEntry = [
            "ConsultationSession_id" => $this->consultationSession->id,
        ];
        $this->seeInDatabase("ParticipantFeedback", $participantFeedbackEntry);

        $formRecordEntry = [
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("FormRecord", $formRecordEntry);
    }

    public function test_setParticipantFeedback_consultationSessionAlreadyHasParticipantFeedback_updateExistingParticipantFeedback()
    {
        $response = [
            "id" => $this->consultationSessionOne->id,
            "startTime" => $this->consultationSessionOne->startDateTime,
            "endTime" => $this->consultationSessionOne->endDateTime,
            "consultationSetup" => [
                "id" => $this->consultationSessionOne->consultationSetup->id,
                "name" => $this->consultationSessionOne->consultationSetup->name,
            ],
            "consultant" => [
                "id" => $this->consultationSessionOne->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSessionOne->consultant->personnel->id,
                    "name" => $this->consultationSessionOne->consultant->personnel->name,
                ],
            ],
            "participantFeedback" => [
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
        
        $uri = $this->consultationSessionUri . "/{$this->consultationSessionOne->id}/participant-feedback";
        $this->put($uri, $this->participantFeedbackInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $stringFieldRecordEntry = [
            "id" => $this->stringFieldRecord->id,
            "value" => $this->participantFeedbackInput['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordEntry);
    }

}
 