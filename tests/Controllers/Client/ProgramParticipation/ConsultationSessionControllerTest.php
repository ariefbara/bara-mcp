<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\Client\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfParticipantFeedback;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultationSetup;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class ConsultationSessionControllerTest extends ProgramParticipationTestCase
{
    protected $consultationSessionUri;
    protected $consultationSession;
    protected $consultationSessionOne;
    protected $participantFeedback;
    protected $participantFeedbackInput;
    protected $stringFieldRecord;
    protected $consultationSetup;
    protected $consultant;
    protected $declareRequest;
    protected $feedbackForm;
    protected $stringField;

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

        $this->feedbackForm = new RecordOfFeedbackForm($firm, $form);

        $this->consultationSetup = new RecordOfConsultationSetup($program, $this->feedbackForm, $this->feedbackForm, 0);

        $personnel = new RecordOfPersonnel($firm, 0, "personnel@email.org", 'password123');

        $this->consultant = new RecordOfConsultant($program, $personnel, 0);

        $this->consultationSession = new RecordOfConsultationSession(
                $this->consultationSetup, $this->programParticipation->participant, $this->consultant, 0);
        $this->consultationSession->startDateTime = (new DateTimeImmutable('+48 hours'))->format('Y-m-d H:i:s');
        $this->consultationSession->endDateTime = (new DateTimeImmutable('+49 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionOne = new RecordOfConsultationSession(
                $this->consultationSetup, $this->programParticipation->participant, $this->consultant, 1);
        $this->consultationSessionOne->startDateTime = (new DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionOne->endDateTime = (new DateTimeImmutable('-23 hours'))->format('Y-m-d H:i:s');
        $this->consultationSessionOne->sessionType = 1;

        $formRecord = new RecordOfFormRecord($form, 1);

        $this->stringField = new RecordOfStringField($form, 1);

        $this->stringFieldRecord = new RecordOfStringFieldRecord($formRecord, $this->stringField, 0);

        $this->participantFeedback = new RecordOfParticipantFeedback($this->consultationSession, $formRecord);

        $this->participantFeedbackInput = [
            "mentorRating" => 3,
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
        
        $this->declareRequest = [
            'consultationSetupId' => $this->consultationSetup->id,
            'consultantId' => $this->consultant->id,
            'startTime' => (new \DateTimeImmutable('+32 hours'))->format('Y-m-d H:i:s'),
            'endTime' => (new \DateTimeImmutable("33 hours"))->format('Y-m-d H:i:s'),
            'media' => 'new media',
            'address' => 'new address',
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
    
    protected function show()
    {
        $this->consultationSession->consultationSetup->participantFeedbackForm->insert($this->connection);
        $this->consultationSession->consultationSetup->insert($this->connection);
        
        $this->consultationSession->consultant->personnel->insert($this->connection);
        $this->consultationSession->consultant->insert($this->connection);
        
        $this->consultationSession->insert($this->connection);
        
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}";
        $this->get($uri, $this->client->token);
    }
    public function test_show()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->consultationSession->id,
            "startTime" => $this->consultationSession->startDateTime,
            "endTime" => $this->consultationSession->endDateTime,
            "media" => $this->consultationSession->media,
            "address" => $this->consultationSession->address,
            "sessionType" => 'HANDSHAKING',
            "approvedByMentor" => null,
            "consultant" => [
                "id" => $this->consultationSession->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSession->consultant->personnel->id,
                    "name" => $this->consultationSession->consultant->personnel->getFullName(),
                ],
            ],
            "participantFeedback" => null,
        ];
        $this->seeJsonContains($response);
    }

    protected function showAll()
    {
        $this->consultationSession->consultationSetup->participantFeedbackForm->insert($this->connection);
        $this->consultationSession->consultationSetup->insert($this->connection);
        
        $this->consultationSession->consultant->personnel->insert($this->connection);
        $this->consultationSession->consultant->insert($this->connection);
        
        $this->consultationSession->insert($this->connection);
        $this->consultationSessionOne->insert($this->connection);
        
        $this->participantFeedback->insert($this->connection);
        $this->stringFieldRecord->insert($this->connection);
        
        $this->stringField->insert($this->connection);
        
        $this->get($this->consultationSessionUri, $this->client->token);
    }
    public function test_showAll()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->consultationSession->id,
                    "startTime" => $this->consultationSession->startDateTime,
                    "endTime" => $this->consultationSession->endDateTime,
                    "media" => $this->consultationSession->media,
                    "address" => $this->consultationSession->address,
                    "cancelled" => $this->consultationSession->cancelled,
                    "sessionType" => 'HANDSHAKING',
                    "approvedByMentor" => null,
                    "hasParticipantFeedback" => true,
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
                    "cancelled" => $this->consultationSessionOne->cancelled,
                    "hasParticipantFeedback" => false,
                    "sessionType" => 'DECLARED',
                    "approvedByMentor" => null,
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
        $this->seeJsonContains($response);
    }
    public function test_showAll_maxEndTimeAndParticipantFeedbackSetFilter()
    {
        $maxEndTimeString = (new DateTime('+72 hours'))->format('Y-m-d H:i:s');
        
        $this->consultationSessionUri .= "?maxEndTime={$maxEndTimeString}&containParticipantFeedback=true";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = [
            "total" => 1,
        ];
        $this->seeJsonContains($totalResponse);

        $objectReponse = [
            "id" => $this->consultationSession->id,
        ];
        $this->seeJsonContains($objectReponse);
    }

    protected function submitReport()
    {
        $this->consultationSession->consultationSetup->participantFeedbackForm->insert($this->connection);
        $this->consultationSession->consultationSetup->insert($this->connection);
        
        $this->consultationSession->consultant->personnel->insert($this->connection);
        $this->consultationSession->consultant->insert($this->connection);
        
        $this->consultationSession->insert($this->connection);
        
        $uri = $this->consultationSessionUri . "/{$this->consultationSession->id}/submit-report";
        $this->put($uri, $this->participantFeedbackInput, $this->client->token);
    }
    public function test_submitReport_200()
    {
        $this->participantFeedbackInput["stringFieldRecords"] = [];
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
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
        $this->seeJsonContains($response);

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
        $this->stringField->insert($this->connection);
        $this->participantFeedback->insert($this->connection);
        $this->stringFieldRecord->insert($this->connection);
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
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
                "id" => $this->consultationSession->consultant->id,
                "personnel" => [
                    "id" => $this->consultationSession->consultant->personnel->id,
                    "name" => $this->consultationSession->consultant->personnel->getFullName(),
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
        $this->seeJsonContains($response);

        $stringFieldRecordEntry = [
            "id" => $this->stringFieldRecord->id,
            "value" => $this->participantFeedbackInput['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase("StringFieldRecord", $stringFieldRecordEntry);
    }
    public function test_submitReport_logActivity()
    {
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "participant submitted consultation report",
            "occuredTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultationSessionActivityLogEntry = [
            "ConsultationSession_id" => $this->consultationSession->id,
        ];
        $this->seeInDatabase("ConsultationSessionActivityLog", $consultationSessionActivityLogEntry);
    }
    
    protected function declare()
    {
        $this->consultant->personnel->insert($this->connection);
        $this->consultant->insert($this->connection);
        
        $this->consultationSetup->consultantFeedbackForm->insert($this->connection);
        $this->consultationSetup->insert($this->connection);
        
        $this->post($this->consultationSessionUri, $this->declareRequest, $this->client->token);
    }
    public function test_declare_201()
    {
        $this->declare();
        $this->seeStatusCode(201);
        
        $response = [
            "startTime" => $this->declareRequest['startTime'],
            "endTime" => $this->declareRequest['endTime'],
            "media" => $this->declareRequest['media'],
            "address" => $this->declareRequest['address'],
            "sessionType" => 'DECLARED',
            "approvedByMentor" => null,
            "consultant" => [
                "id" => $this->consultant->id,
                "personnel" => [
                    "id" => $this->consultant->personnel->id,
                    "name" => $this->consultant->personnel->getFullName(),
                ],
            ],
            "participantFeedback" => null,
        ];
        $this->seeJsonContains($response);
        
        $record = [
            "startDateTime" => $this->declareRequest['startTime'],
            "endDateTime" => $this->declareRequest['endTime'],
            "media" => $this->declareRequest['media'],
            "address" => $this->declareRequest['address'],
            "sessionType" => 1,
            "approvedByMentor" => null,
            'Consultant_id' => $this->consultant->id,
            'ConsultationSetup_id' => $this->consultationSetup->id,
            
        ];
        $this->seeInDatabase('ConsultationSession', $record);
    }
    public function test_declare_unuseableConsultationSetup_403()
    {
        $this->consultationSetup->removed = true;
        $this->declare();
        $this->seeStatusCode(403);
    }
    public function test_declare_unuseableMentor_403()
    {
        $this->consultant->active = false;
        $this->declare();
        $this->seeStatusCode(403);
    }
    
    protected function cancel()
    {
        $this->consultationSessionOne->consultationSetup->participantFeedbackForm->insert($this->connection);
        $this->consultationSessionOne->consultationSetup->insert($this->connection);
        
        $this->consultationSessionOne->consultant->personnel->insert($this->connection);
        $this->consultationSessionOne->consultant->insert($this->connection);
        
        $this->consultationSessionOne->insert($this->connection);
        
        $uri = $this->consultationSessionUri . "/{$this->consultationSessionOne->id}/cancel";
        $this->patch($uri, [], $this->client->token);
    }
    public function test_cancel_200()
    {
        $this->cancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->consultationSessionOne->id,
            'cancelled' => true,
        ];
        $this->seeJsonContains($response);
        
        $record = [
            'id' => $this->consultationSessionOne->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('ConsultationSession', $record);
    }
    
    public function test_cancel_unmanagedConsultationSession_403()
    {
        $otherParticipant = new RecordOfParticipant($this->consultant->program, 'other');
        $otherParticipant->insert($this->connection);
        $this->consultationSessionOne->participant = $otherParticipant;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_nonDeclaredType_403()
    {
        $this->consultationSessionOne->sessionType = 0;
        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_cancelledSession_403()
    {
        $this->consultationSessionOne->cancelled = true;
        $this->cancel();
        $this->seeStatusCode(403);
    }
}
