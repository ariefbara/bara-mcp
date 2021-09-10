<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\Client\AsTeamMember\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\DedicatedMentor\RecordOfMentorEvaluationReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;
use Tests\Controllers\RecordPreparation\Firm\RecordOfFeedbackForm;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFormRecord;

class EvaluationReportControllerTest extends ProgramParticipationTestCase
{
    protected $evaluationReportUri;
    protected $evaluationReportOne;
    protected $evaluationReportTwo;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->evaluationReportUri = $this->programParticipationUri . "/{$this->programParticipation->participant->id}/evaluation-reports";
        
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $formOne = new RecordOfForm('1');
        $formTwo = new RecordOfForm('2');
        $feedbackFormOne = new RecordOfFeedbackForm($firm, $formOne);
        $feedbackFormTwo = new RecordOfFeedbackForm($firm, $formTwo);
        
        $evaluationPlanOne = new RecordOfEvaluationPlan($program, $feedbackFormOne, '1');
        $evaluationPlanTwo = new RecordOfEvaluationPlan($program, $feedbackFormTwo, '2');
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $consultantOne = new RecordOfConsultant($program, $personnelOne, '1');
        $consultantTwo = new RecordOfConsultant($program, $personnelTwo, '2');
        
        $dedicatedMentorOne = new RecordOfDedicatedMentor($participant, $consultantOne, '1');
        $dedicatedMentorTwo = new RecordOfDedicatedMentor($participant, $consultantTwo, '2');
        
        $formRecordOne = new RecordOfFormRecord($formOne, '1');
        $formRecordTwo = new RecordOfFormRecord($formTwo, '2');
        $this->evaluationReportOne = new RecordOfMentorEvaluationReport(
                $dedicatedMentorOne, $evaluationPlanOne, $formRecordOne);
        $this->evaluationReportTwo = new RecordOfMentorEvaluationReport(
                $dedicatedMentorTwo, $evaluationPlanTwo, $formRecordTwo);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('EvaluationPlan')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('MentorEvaluationReport')->truncate();
    }
    
    protected function showAll()
    {
        $this->evaluationReportOne->dedicatedMentor->consultant->personnel->insert($this->connection);
        $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->insert($this->connection);
        
        $this->evaluationReportOne->dedicatedMentor->consultant->insert($this->connection);
        $this->evaluationReportTwo->dedicatedMentor->consultant->insert($this->connection);
        
        $this->evaluationReportOne->dedicatedMentor->insert($this->connection);
        $this->evaluationReportTwo->dedicatedMentor->insert($this->connection);
        
        $this->evaluationReportOne->evaluationPlan->insert($this->connection);
        $this->evaluationReportTwo->evaluationPlan->insert($this->connection);
        
        $this->evaluationReportOne->insert($this->connection);
        $this->evaluationReportTwo->insert($this->connection);
        
        $this->get($this->evaluationReportUri, $this->teamMember->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        $response = [
            'total' => 2,
            'list' => [
                [
                    'id' => $this->evaluationReportOne->formRecord->id,
                    'modifiedTime' => $this->evaluationReportOne->modifiedTime,
                    'evaluationPlan' => [
                        'id' => $this->evaluationReportOne->evaluationPlan->id,
                        'name' => $this->evaluationReportOne->evaluationPlan->name,
                    ],
                    'mentor' => [
                        'id' => $this->evaluationReportOne->dedicatedMentor->consultant->id,
                        'personnel' => [
                            'id' => $this->evaluationReportOne->dedicatedMentor->consultant->personnel->id,
                            'name' => $this->evaluationReportOne->dedicatedMentor->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
                [
                    'id' => $this->evaluationReportTwo->formRecord->id,
                    'modifiedTime' => $this->evaluationReportTwo->modifiedTime,
                    'evaluationPlan' => [
                        'id' => $this->evaluationReportTwo->evaluationPlan->id,
                        'name' => $this->evaluationReportTwo->evaluationPlan->name,
                    ],
                    'mentor' => [
                        'id' => $this->evaluationReportTwo->dedicatedMentor->consultant->id,
                        'personnel' => [
                            'id' => $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->id,
                            'name' => $this->evaluationReportTwo->dedicatedMentor->consultant->personnel->getFullName(),
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_containCancelledReport_excludeFromResult()
    {
        $this->evaluationReportOne->cancelled = true;
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $evaluationReportTwoResponse = [
            'id' => $this->evaluationReportTwo->formRecord->id
        ];
        $this->seeJsonContains($evaluationReportTwoResponse);
    }
    
    protected function show()
    {
        $this->evaluationReportOne->dedicatedMentor->consultant->personnel->insert($this->connection);
        
        $this->evaluationReportOne->dedicatedMentor->consultant->insert($this->connection);
        
        $this->evaluationReportOne->dedicatedMentor->insert($this->connection);
        
        $this->evaluationReportOne->evaluationPlan->insert($this->connection);
        
        $this->evaluationReportOne->insert($this->connection);
        
        $uri = $this->evaluationReportUri . "/{$this->evaluationReportOne->formRecord->id}";
        $this->get($uri, $this->teamMember->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->evaluationReportOne->formRecord->id,
            'modifiedTime' => $this->evaluationReportOne->modifiedTime,
            'evaluationPlan' => [
                'id' => $this->evaluationReportOne->evaluationPlan->id,
                'name' => $this->evaluationReportOne->evaluationPlan->name,
            ],
            'mentor' => [
                'id' => $this->evaluationReportOne->dedicatedMentor->consultant->id,
                'personnel' => [
                    'id' => $this->evaluationReportOne->dedicatedMentor->consultant->personnel->id,
                    'name' => $this->evaluationReportOne->dedicatedMentor->consultant->personnel->getFullName(),
                ],
            ],
            'stringFieldRecords' => [],
            'integerFieldRecords' => [],
            'textAreaFieldRecords' => [],
            'attachmentFieldRecords' => [],
            'singleSelectFieldRecords' => [],
            'multiSelectFieldRecords' => [],
        ];
        $this->seeJsonContains($response);
    }
}
