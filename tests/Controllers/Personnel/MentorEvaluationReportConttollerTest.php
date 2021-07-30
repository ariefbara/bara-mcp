<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\DedicatedMentor\RecordOfMentorEvaluationReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\Form\RecordOfStringField;
use Tests\Controllers\RecordPreparation\Shared\FormRecord\RecordOfStringFieldRecord;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class MentorEvaluationReportConttollerTest extends PersonnelTestCase
{
    protected $showAllUri;
    protected $programOne;
    protected $mentorOne;
    protected $participantOne;
    protected $participantTwo;
    protected $clientOne;
    protected $clientTwo;
    protected $clientParticipantOne;
    protected $clientParticipantTwo;
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;
    protected $feedbackformOne;
    protected $evaluationPlanOne;
    protected $evaluationPlanTwo;
    protected $stringFieldOne;
    protected $mentorEvaluationReport_dm1_ep1;
    protected $mentorEvaluationReport_dm2_ep2;
    protected $stringFieldRecordOne;

    protected $submitRequest = [];

    protected function setUp(): void
    {
        parent::setUp();
        
        
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
        
        $firm = $this->personnel->firm;
        
        $this->programOne = new RecordOfProgram($firm, '1');
        $this->programOne->insert($this->connection);
        
        $this->mentorOne = new RecordOfConsultant($this->programOne, $this->personnel, '1');
        $this->participantOne = new RecordOfParticipant($this->programOne, '1');
        $this->participantTwo = new RecordOfParticipant($this->programOne, '2');
        $this->clientOne = new RecordOfClient($firm, '1');
        $this->clientTwo = new RecordOfClient($firm, '2');
        $this->clientParticipantOne = new RecordOfClientParticipant($this->clientOne, $this->participantOne);
        $this->clientParticipantTwo = new RecordOfClientParticipant($this->clientTwo, $this->participantTwo);
        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($this->participantOne, $this->mentorOne, '1');
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($this->participantTwo, $this->mentorOne, '2');
        
        $formOne = new RecordOfForm('1');
        $this->stringFieldOne = new RecordOfStringField($formOne, '1');
        $this->feedbackformOne = new RecordOfFeedbackForm($firm, $formOne);
        $this->evaluationPlanOne = new RecordOfEvaluationPlan($this->programOne, $this->feedbackformOne, '1');
        $this->evaluationPlanTwo = new RecordOfEvaluationPlan($this->programOne, $this->feedbackformOne, '2');
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $formRecordTwo = new RecordOfFormRecord($formOne, '2');
        $this->stringFieldRecordOne = new RecordOfStringFieldRecord($formRecordOne, $this->stringFieldOne, '1');
        $this->mentorEvaluationReport_dm1_ep1 = new RecordOfMentorEvaluationReport($this->dedicatedMentorOne, $this->evaluationPlanOne, $formRecordOne);
        $this->mentorEvaluationReport_dm2_ep2 = new RecordOfMentorEvaluationReport($this->dedicatedMentorTwo, $this->evaluationPlanTwo, $formRecordTwo);
        
        $this->submitRequest = [
            'stringFieldRecords' => [
                ['fieldId' => $this->stringFieldOne->id, 'value' => 'new string field value'],
            ],
            'integerFieldRecords' => [],
            'textAreaFieldRecords' => [],
            'attachmentFieldRecords' => [],
            'singleSelectFieldRecords' => [],
            'multiSelectFieldRecords' => [],
        ];
        
        $this->showAllUri = $this->personnelUri . "/programs/{$this->programOne->id}/evaluation-reports";
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('StringField')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('StringFieldRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
    }
    
    protected function executeSubmit()
    {
        $this->mentorOne->insert($this->connection);
        $this->participantOne->insert($this->connection);
        $this->clientOne->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);
        $this->feedbackformOne->insert($this->connection);
        $this->evaluationPlanOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/dedicated-mentors/{$this->dedicatedMentorOne->id}/evaluation-reports/{$this->evaluationPlanOne->id}";
        $this->put($uri, $this->submitRequest, $this->personnel->token);
    }
    public function test_submit_201()
    {
        $this->executeSubmit();
        $this->seeStatusCode(201);
        
        $mentorEvaluationReportRecord = [
            'DedicatedMentor_id' => $this->dedicatedMentorOne->id,
            'EvaluationPlan_id' => $this->evaluationPlanOne->id,
            'cancelled' => false,
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeInDatabase('MentorEvaluationReport', $mentorEvaluationReportRecord);
        
        $response = [
            'cancelled' => false,
            'modifiedTime' => $this->currentTimeString(),
            'evaluationPlan' => [
                'id' => $this->evaluationPlanOne->id,
                'name' => $this->evaluationPlanOne->name,
            ],
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'user' => null,
                'team' => null,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_submit_aggregateFieldRecords()
    {
        $this->executeSubmit();
        $stringFieldRecord = [
            'StringField_id' => $this->stringFieldOne->id,
            'value' => $this->submitRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecord);
    }
    public function test_submit_hadReportCorrespondToSamePlan_update()
    {
        $this->mentorEvaluationReport_dm1_ep1->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $this->executeSubmit();
        $stringFieldRecordEntry = [
            'id' => $this->stringFieldRecordOne->id,
            'value' => $this->submitRequest['stringFieldRecords'][0]['value'],
        ];
        $this->seeInDatabase('StringFieldRecord', $stringFieldRecordEntry);
    }
    public function test_submit_existingReportAlreadyCancelled_addNewReport()
    {
        $this->mentorEvaluationReport_dm1_ep1->cancelled = true;
        $this->mentorEvaluationReport_dm1_ep1->insert($this->connection);
        
        $this->executeSubmit();
        $existingMentorEvaluationReportEntry = [
            'id' => $this->mentorEvaluationReport_dm1_ep1->formRecord->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('MentorEvaluationReport', $existingMentorEvaluationReportEntry);
        $newMentorEvaluationReportEntry = [
            'EvaluationPlan_id' => $this->evaluationPlanOne->id,
            'DedicatedMentor_id' => $this->dedicatedMentorOne->id,
            'cancelled' => false,
        ];
        $this->seeInDatabase('MentorEvaluationReport', $newMentorEvaluationReportEntry);
    }
    public function test_submit_inactiveDedicatedMentor_403()
    {
        $this->dedicatedMentorOne->cancelled = true;
        $this->executeSubmit();
        $this->seeStatusCode(403);
    }
    public function test_submit_disabledEvaluationPlan_403()
    {
        $this->evaluationPlanOne->disabled = true;
        $this->executeSubmit();
        $this->seeStatusCode(403);
    }
    
    protected function executeCancel()
    {
        $this->mentorOne->insert($this->connection);
        $this->participantOne->insert($this->connection);
        $this->clientOne->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);
        $this->feedbackformOne->insert($this->connection);
        $this->evaluationPlanOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        $this->mentorEvaluationReport_dm1_ep1->insert($this->connection);
        
        $uri = $this->personnelUri . "/dedicated-mentors/{$this->dedicatedMentorOne->id}/evaluation-reports/{$this->mentorEvaluationReport_dm1_ep1->formRecord->id}";
        $this->delete($uri, [], $this->personnel->token);
    }
    public function test_cancel_200()
    {
        $this->executeCancel();
        $this->seeStatusCode(200);
        $response = [
            'id' => $this->mentorEvaluationReport_dm1_ep1->formRecord->id,
            'cancelled' => true,
        ];
        $this->seeJsonContains($response);
        
        $mentorEvaluationReportEntry = [
            'id' => $this->mentorEvaluationReport_dm1_ep1->formRecord->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('MentorEvaluationReport', $mentorEvaluationReportEntry);
    }
    public function test_cancel_inactiveDedicatedMentor_403()
    {
        $this->dedicatedMentorOne->cancelled = true;
        $this->executeCancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_reportBelongsToOtherDedicatedMentor_403()
    {
        $otherDedicatedMentor = new RecordOfDedicatedMentor($this->participantOne, $this->mentorOne, 'other');
        $otherDedicatedMentor->insert($this->connection);
        $this->mentorEvaluationReport_dm1_ep1->dedicatedMentor = $otherDedicatedMentor;
        $this->executeCancel();
        $this->seeStatusCode(403);
    }
    
    protected function executeShow()
    {
        $this->mentorOne->insert($this->connection);
        $this->participantOne->insert($this->connection);
        $this->clientOne->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);
        $this->feedbackformOne->insert($this->connection);
        $this->evaluationPlanOne->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        $this->mentorEvaluationReport_dm1_ep1->insert($this->connection);
        $this->stringFieldRecordOne->insert($this->connection);
        
        $uri = $this->personnelUri . "/evaluation-reports/{$this->mentorEvaluationReport_dm1_ep1->formRecord->id}";
        $this->get($uri, $this->personnel->token);
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->mentorEvaluationReport_dm1_ep1->formRecord->id,
            'cancelled' => $this->mentorEvaluationReport_dm1_ep1->cancelled,
            'modifiedTime' => $this->mentorEvaluationReport_dm1_ep1->modifiedTime,
            'evaluationPlan' => [
                'id' => $this->mentorEvaluationReport_dm1_ep1->evaluationPlan->id,
                'name' => $this->mentorEvaluationReport_dm1_ep1->evaluationPlan->name,
            ],
            'participant' => [
                'id' => $this->mentorEvaluationReport_dm1_ep1->dedicatedMentor->participant->id,
                'user' => null,
                'team' => null,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
            ],
            'stringFieldRecords' => [
                [
                    'id' => $this->stringFieldRecordOne->id,
                    'value' => $this->stringFieldRecordOne->value,
                    'stringField' => [
                        'id' => $this->stringFieldRecordOne->stringField->id,
                        'name' => $this->stringFieldRecordOne->stringField->name,
                        'position' => $this->stringFieldRecordOne->stringField->position,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeShowAll()
    {
        $this->mentorOne->insert($this->connection);
        $this->participantOne->insert($this->connection);
        $this->participantTwo->insert($this->connection);
        $this->clientOne->insert($this->connection);
        $this->clientTwo->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->clientParticipantTwo->insert($this->connection);
        $this->dedicatedMentorOne->insert($this->connection);
        $this->dedicatedMentorTwo->insert($this->connection);
        $this->feedbackformOne->insert($this->connection);
        $this->evaluationPlanOne->insert($this->connection);
        $this->evaluationPlanTwo->insert($this->connection);
        $this->stringFieldOne->insert($this->connection);
        $this->mentorEvaluationReport_dm1_ep1->insert($this->connection);
        $this->mentorEvaluationReport_dm2_ep2->insert($this->connection);
        
        $this->get($this->showAllUri, $this->personnel->token);
    }
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 4];
        $this->seeJsonContains($totalResponse);
        
        $mentorEvaluationReport_dm1_ep1_response = [
            'dedicatedMentorId' => $this->mentorEvaluationReport_dm1_ep1->dedicatedMentor->id,
            'participantId' => $this->mentorEvaluationReport_dm1_ep1->dedicatedMentor->participant->id,
            'participantName' => $this->clientParticipantOne->client->getFullName(),
            'evaluationPlanId' => $this->mentorEvaluationReport_dm1_ep1->evaluationPlan->id,
            'evaluationPlanName' => $this->mentorEvaluationReport_dm1_ep1->evaluationPlan->name,
            'evaluationPlanIntervalDay' => strval($this->mentorEvaluationReport_dm1_ep1->evaluationPlan->interval),
            'mentorEvaluationReportId' => $this->mentorEvaluationReport_dm1_ep1->formRecord->id,
        ];
        $this->seeJsonContains($mentorEvaluationReport_dm1_ep1_response);
        
        $mentorEvaluationReport_dm1_ep2_response = [
            'dedicatedMentorId' => $this->dedicatedMentorOne->id,
            'participantId' => $this->dedicatedMentorOne->participant->id,
            'participantName' => $this->clientParticipantOne->client->getFullName(),
            'evaluationPlanId' => $this->evaluationPlanTwo->id,
            'evaluationPlanName' => $this->evaluationPlanTwo->name,
            'mentorEvaluationReportId' => null,
        ];
        $this->seeJsonContains($mentorEvaluationReport_dm1_ep2_response);
        
        $mentorEvaluationReport_dm2_ep1_response = [
            'dedicatedMentorId' => $this->mentorEvaluationReport_dm2_ep2->dedicatedMentor->id,
            'participantId' => $this->mentorEvaluationReport_dm2_ep2->dedicatedMentor->participant->id,
            'participantName' => $this->clientParticipantTwo->client->getFullName(),
            'evaluationPlanId' => $this->mentorEvaluationReport_dm2_ep2->evaluationPlan->id,
            'evaluationPlanName' => $this->mentorEvaluationReport_dm2_ep2->evaluationPlan->name,
            'mentorEvaluationReportId' => $this->mentorEvaluationReport_dm2_ep2->formRecord->id,
        ];
        $this->seeJsonContains($mentorEvaluationReport_dm2_ep1_response);
        
        $mentorEvaluationReport_dm2_ep1_response = [
            'dedicatedMentorId' => $this->dedicatedMentorTwo->id,
            'participantId' => $this->dedicatedMentorTwo->participant->id,
            'participantName' => $this->clientParticipantTwo->client->getFullName(),
            'evaluationPlanId' => $this->evaluationPlanOne->id,
            'evaluationPlanName' => $this->evaluationPlanOne->name,
            'mentorEvaluationReportId' => null,
        ];
        $this->seeJsonContains($mentorEvaluationReport_dm1_ep1_response);
    }
    public function test_showAll_submittedStatusFilterOn_200()
    {
        $this->showAllUri .= "?submittedStatus=true";
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $reportOneEntry = [
            'mentorEvaluationReportId' => $this->mentorEvaluationReport_dm1_ep1->formRecord->id,
        ];
        $this->seeJsonContains($reportOneEntry);
        $reportTwoEntry = [
            'mentorEvaluationReportId' => $this->mentorEvaluationReport_dm2_ep2->formRecord->id,
        ];
        $this->seeJsonContains($reportTwoEntry);
    }
    public function test_showAll_evaluationPlanIdFilterOn_200()
    {
        $this->showAllUri .= "?evaluationPlanId={$this->evaluationPlanOne->id}";
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $reportOneEntry = [
            'mentorEvaluationReportId' => $this->mentorEvaluationReport_dm1_ep1->formRecord->id,
            'evaluationPlanId' => $this->evaluationPlanOne->id,
            'participantId' => $this->participantOne->id,
        ];
        $this->seeJsonContains($reportOneEntry);
        $reportTwoEntry = [
            'mentorEvaluationReportId' => null,
            'evaluationPlanId' => $this->evaluationPlanOne->id,
            'participantId' => $this->participantTwo->id,
        ];
        $this->seeJsonContains($reportTwoEntry);
    }
    public function test_showAll_participantNameFilterOn_200()
    {
        $this->showAllUri .= "?participantName=1";
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $reportOneEntry = [
            'mentorEvaluationReportId' => $this->mentorEvaluationReport_dm1_ep1->formRecord->id,
            'evaluationPlanId' => $this->evaluationPlanOne->id,
            'participantId' => $this->participantOne->id,
        ];
        $this->seeJsonContains($reportOneEntry);
        $reportTwoEntry = [
            'mentorEvaluationReportId' => null,
            'participantId' => $this->participantOne->id,
            'evaluationPlanId' => $this->evaluationPlanTwo->id,
        ];
        $this->seeJsonContains($reportTwoEntry);
    }
}
