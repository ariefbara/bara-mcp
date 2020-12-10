<?php

namespace Tests\Controllers\Personnel\Coordinator;

use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\EvaluationPlan\RecordOfEvaluationReport,
    Firm\Program\RecordOfEvaluationPlan,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfFeedbackForm,
    Shared\Form\RecordOfStringField,
    Shared\FormRecord\RecordOfStringFieldRecord,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class EvaluationReportControllerTest extends CoordinatorTestCase
{
    protected $evaluationReportUri;
    protected $evaluationReportOne;
    protected $evaluationReportTwo;
    protected $clientParticipant;
    protected $clientParticipantOne;
    protected $clientParticipantTwo;
    protected $participant;
    protected $evaluationPlan;
    protected $submitInput;
    protected $stringField;
    protected $stringFieldRecord_formRecordOne;

    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationReportUri = $this->coordinatorUri . "/{$this->coordinator->id}/evaluation-reports";
        
        $this->connection->table("Form")->truncate();
        $this->connection->table("StringField")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("StringFieldRecord")->truncate();
        $this->connection->table("EvaluationReport")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;

        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $this->evaluationPlan = new RecordOfEvaluationPlan($program, $feedbackForm, 0);
        $evaluationPlanOne = new RecordOfEvaluationPlan($program, $feedbackForm, 1);
        $evaluationPlanTwo = new RecordOfEvaluationPlan($program, $feedbackForm, 2);
        $this->connection->table("EvaluationPlan")->insert($this->evaluationPlan->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($evaluationPlanOne->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($evaluationPlanTwo->toArrayForDbEntry());
        
        $this->participant = new RecordOfParticipant($program, 0);
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        $this->connection->table("Participant")->insert($this->participant->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantOne->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantTwo->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $clientOne = new RecordOfClient($firm, 1);
        $clientTwo = new RecordOfClient($firm, 2);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientTwo->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $this->participant);
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        $this->clientParticipantTwo = new RecordOfClientParticipant($clientTwo, $participantTwo);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        $this->connection->table("ClientParticipant")->insert($this->clientParticipantOne->toArrayForDbEntry());
        $this->connection->table("ClientParticipant")->insert($this->clientParticipantTwo->toArrayForDbEntry());
        
        $formRecordOne = new RecordOfFormRecord($form, 1);
        $formRecordTwo = new RecordOfFormRecord($form, 2);
        $this->connection->table("FormRecord")->insert($formRecordOne->toArrayForDbEntry());
        $this->connection->table("FormRecord")->insert($formRecordTwo->toArrayForDbEntry());
        
        $this->evaluationReportOne = new RecordOfEvaluationReport($evaluationPlanOne, $this->coordinator, $participantOne, $formRecordOne);
        $this->evaluationReportTwo = new RecordOfEvaluationReport($evaluationPlanTwo, $this->coordinator, $participantTwo, $formRecordTwo);
        $this->connection->table("EvaluationReport")->insert($this->evaluationReportOne->toArrayForDbEntry());
        $this->connection->table("EvaluationReport")->insert($this->evaluationReportTwo->toArrayForDbEntry());
        
        $this->submitInput = [
            "stringFieldRecords" => [],
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
        $this->connection->table("Form")->truncate();
        $this->connection->table("StringField")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("FormRecord")->truncate();
        $this->connection->table("StringFieldRecord")->truncate();
        $this->connection->table("EvaluationReport")->truncate();
    }
    
    public function test_submit_200()
    {
        $response = [
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "participant" => [
                "id" => $this->participant->id,
                "name" => $this->clientParticipant->client->getFullName(),
            ],
            "evaluationPlan" => [
                "id" => $this->evaluationPlan->id,
                "name" => $this->evaluationPlan->name,
            ],
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        
        $uri = $this->evaluationReportUri . "/{$this->participant->id}/{$this->evaluationPlan->id}";
        $this->put($uri, $this->submitInput, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $evaluationReportEntry = [
            "Coordinator_id" => $this->coordinator->id,
            "Participant_id" => $this->participant->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("EvaluationReport", $evaluationReportEntry);
    }
    public function test_submit_alreayHasReportOfSameEvaluationPLanForToParticipant_updateReport()
    {
        $stringField = new RecordOfStringField($this->evaluationReportOne->evaluationPlan->feedbackForm->form, 0);
        $this->connection->table("StringField")->insert($stringField->toArrayForDbEntry());
        
        $stringFieldRecord = new RecordOfStringFieldRecord($this->evaluationReportOne->formRecord, $stringField, 0);
        $this->connection->table("StringFieldRecord")->insert($stringFieldRecord->toArrayForDbEntry());
        
        $this->submitInput["stringFieldRecords"] = [
            [
                "fieldId" => $stringField->id,
                "value" => "new string value",
            ],
        ];
        
        $response = [
            "id" => $this->evaluationReportOne->id,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "participant" => [
                "id" => $this->evaluationReportOne->participant->id,
                "name" => $this->clientParticipantOne->client->getFullName(),
            ],
            "evaluationPlan" => [
                "id" => $this->evaluationReportOne->evaluationPlan->id,
                "name" => $this->evaluationReportOne->evaluationPlan->name,
            ],
            "stringFieldRecords" => [
                [
                    "id" => $stringFieldRecord->id,
                    "stringField" => [
                        "id" => $stringField->id,
                        "name" => $stringField->name,
                        "position" => $stringField->position,
                    ],
                    "value" => "new string value",
                ],
            ],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        
        $uri = $this->evaluationReportUri . "/{$this->evaluationReportOne->participant->id}/{$this->evaluationReportOne->evaluationPlan->id}";
        $this->put($uri, $this->submitInput, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $stringFieldRecordEntry = [
            "StringField_id" => $stringField->id,
            "id" => $stringFieldRecord->id,
            "value" => "new string value",
        ];
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->evaluationReportOne->id,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "participant" => [
                "id" => $this->evaluationReportOne->participant->id,
                "name" => $this->clientParticipantOne->client->getFullName(),
            ],
            "evaluationPlan" => [
                "id" => $this->evaluationReportOne->evaluationPlan->id,
                "name" => $this->evaluationReportOne->evaluationPlan->name,
            ],
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
        
        $uri = $this->evaluationReportUri . "/{$this->evaluationReportOne->participant->id}/{$this->evaluationReportOne->evaluationPlan->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->evaluationReportOne->id,
                    "submitTime" => $this->evaluationReportOne->formRecord->submitTime,
                    "participant" => [
                        "id" => $this->evaluationReportOne->participant->id,
                        "name" => $this->clientParticipantOne->client->getFullName(),
                    ],
                    "evaluationPlan" => [
                        "id" => $this->evaluationReportOne->evaluationPlan->id,
                        "name" => $this->evaluationReportOne->evaluationPlan->name,
                    ],
                ],
                [
                    "id" => $this->evaluationReportTwo->id,
                    "submitTime" => $this->evaluationReportTwo->formRecord->submitTime,
                    "participant" => [
                        "id" => $this->evaluationReportTwo->participant->id,
                        "name" => $this->clientParticipantTwo->client->getFullName(),
                    ],
                    "evaluationPlan" => [
                        "id" => $this->evaluationReportTwo->evaluationPlan->id,
                        "name" => $this->evaluationReportTwo->evaluationPlan->name,
                    ],
                ],
            ],
        ];
        $this->get($this->evaluationReportUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
