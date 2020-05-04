<?php

namespace Tests\Controllers\Personnel\ProgramConsultant;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\ConsultationSession\RecordOfConsultantFeedback,
    Firm\Program\Participant\RecordOfConsultationSession,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfFeedbackForm,
    RecordOfClient,
    Shared\Form\RecordOfStringField,
    Shared\FormRecord\RecordOfStringFieldRecord,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class ConsultationSessionControllerTest extends ConsultationSessionTestCase
{

    protected $consultationSessionOne;
    protected $consultantFeedbackOne;
    protected $consultantFeedbackInput;
    protected $stringFieldRecord;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('ConsultantFeedback')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();

        $form = new RecordOfForm(1);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());

        $feedbackForm = new RecordOfFeedbackForm($this->programConsultant->program->firm,
                $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());

        $consultationSetup = new RecordOfConsultationSetup($this->programConsultant->program,
                $feedbackForm, $feedbackForm, 1);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());

        $clientOne = new RecordOfClient(1, 'clientOne@email.org', 'password123');
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($this->programConsultant->program, $clientOne, 1);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->consultationSessionOne = new RecordOfConsultationSession($consultationSetup, $participant, $this->programConsultant, 1);
        $this->connection->table("ConsultationSession")->insert($this->consultationSessionOne->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($feedbackForm->form, 1);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());

        $stringField = new RecordOfStringField($form, 1);
        $this->connection->table('StringField')->insert($stringField->toArrayForDbEntry());

        $this->stringFieldRecord = new RecordOfStringFieldRecord($formRecord, $stringField, 0);
        $this->connection->table('StringFieldRecord')->insert($this->stringFieldRecord->toArrayForDbEntry());

        $this->consultantFeedbackOne = new RecordOfConsultantFeedback($this->consultationSessionOne, $formRecord);
        $this->connection->table('ConsultantFeedback')->insert($this->consultantFeedbackOne->toArrayForDbEntry());

        $this->consultantFeedbackInput = [
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
        $this->connection->table('ConsultantFeedback')->truncate();
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
            "participant" => [
                "id" => $this->consultationSession->participant->id,
                "client" => [
                    "id" => $this->consultationSession->participant->client->id,
                    "name" => $this->consultationSession->participant->client->name,
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
                        "client" => [
                            "id" => $this->consultationSession->participant->client->id,
                            "name" => $this->consultationSession->participant->client->name,
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
                        "client" => [
                            "id" => $this->consultationSessionOne->participant->client->id,
                            "name" => $this->consultationSessionOne->participant->client->name,
                        ],
                    ],
                ],
            ],
        ];
        $this->get($this->consultationSessionUri, $this->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }

    public function test_setConsultantFeedback()
    {
        $this->consultantFeedbackInput["stringFieldRecords"] = [];
        $response = [
            "id" => $this->consultationSession->id,
            "startTime" => $this->consultationSession->startDateTime,
            "endTime" => $this->consultationSession->endDateTime,
            "consultationSetup" => [
                "id" => $this->consultationSession->consultationSetup->id,
                "name" => $this->consultationSession->consultationSetup->name,
            ],
            "participant" => [
                "id" => $this->consultationSession->participant->id,
                "client" => [
                    "id" => $this->consultationSession->participant->client->id,
                    "name" => $this->consultationSession->participant->client->name,
                ],
            ],
            "consultantFeedback" => [
                "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
                "stringFieldRecords" => [],
                "integerFieldRecords" => [],
                "textAreaFieldRecords" => [],
                "attachmentFieldRecords" => [],
                "singleSelectFieldRecords" => [],
                "multiSelectFieldRecords" => [],
            ],
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
            ],
            "participant" => [
                "id" => $this->consultationSessionOne->participant->id,
                "client" => [
                    "id" => $this->consultationSessionOne->participant->client->id,
                    "name" => $this->consultationSessionOne->participant->client->name,
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
 