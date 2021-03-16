<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\RecordOfKeyResultProgressReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfKeyResult;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfObjectiveProgressReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\RecordOfObjective;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfOKRPeriod;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;


class ObjectiveProgressReportControllerTest extends AsProgramCoordinatorTestCase
{
    protected $objective;
    protected $objectiveProgressReportOne;
    protected $objectiveProgressReportTwo;
    
    protected $keyResultPR11_objPR1_kr1;
    protected $keyResultPR21_objPR1_kr2;
    protected $keyResultPR12_objPR2_kr1;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('OKRPeriod')->truncate();
        $this->connection->table('Objective')->truncate();
        $this->connection->table('ObjectiveProgressReport')->truncate();
        $this->connection->table('KeyResult')->truncate();
        $this->connection->table('KeyResultProgressReport')->truncate();
        
        $program = $this->coordinator->program;
        
        $participant = new RecordOfParticipant($program, 9);
        
        $okrPeriod = new RecordOfOKRPeriod($participant, 9);
        
        $this->objective = new RecordOfObjective($okrPeriod, 9);
        
        $this->objectiveProgressReportOne = new RecordOfObjectiveProgressReport($this->objective, 1);
        $this->objectiveProgressReportTwo = new RecordOfObjectiveProgressReport($this->objective, 2);
        
        $keyResultOne = new RecordOfKeyResult($this->objective, 1);
        $keyResultTwo = new RecordOfKeyResult($this->objective, 2);
        
        $this->keyResultPR11_objPR1_kr1 = new RecordOfKeyResultProgressReport($this->objectiveProgressReportOne, $keyResultOne, 11);
        $this->keyResultPR21_objPR1_kr2 = new RecordOfKeyResultProgressReport($this->objectiveProgressReportOne, $keyResultTwo, 21);
        $this->keyResultPR12_objPR2_kr1 = new RecordOfKeyResultProgressReport($this->objectiveProgressReportTwo, $keyResultOne, 12);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('OKRPeriod')->truncate();
        $this->connection->table('Objective')->truncate();
        $this->connection->table('ObjectiveProgressReport')->truncate();
        $this->connection->table('KeyResult')->truncate();
        $this->connection->table('KeyResultProgressReport')->truncate();
    }
    
    protected function executeApprove()
    {
        $this->objectiveProgressReportOne->objective->okrPeriod->participant->insert($this->connection);
        $this->objectiveProgressReportOne->objective->okrPeriod->insert($this->connection);
        $this->objectiveProgressReportOne->objective->insert($this->connection);
        $this->objectiveProgressReportOne->insert($this->connection);
        
        $uri = $this->asProgramCoordinatorUri . "/objective-progress-reports/{$this->objectiveProgressReportOne->id}/approve";
        $this->patch($uri, [], $this->coordinator->personnel->token);
    }
    public function test_approve_200()
    {
        $this->executeApprove();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->objectiveProgressReportOne->id,
            'approvalStatus' => OKRPeriodApprovalStatus::APPROVED,
        ];
        $this->seeJsonContains($response);
        
        $entry = [
            'id' => $this->objectiveProgressReportOne->id,
            'status' => OKRPeriodApprovalStatus::APPROVED,
        ];
        $this->seeInDatabase('ObjectiveProgressReport', $entry);
    }
    public function test_approve_alreadyCancelled_403()
    {
        $this->objectiveProgressReportOne->cancelled = true;
        $this->executeApprove();
        $this->seeStatusCode(403);
    }
    public function test_approve_alreadyConcluded_403()
    {
        $this->objectiveProgressReportOne->status = OKRPeriodApprovalStatus::REJECTED;
        $this->executeApprove();
        $this->seeStatusCode(403);
    }
    
    protected function executeReject()
    {
        $this->objectiveProgressReportOne->objective->okrPeriod->participant->insert($this->connection);
        $this->objectiveProgressReportOne->objective->okrPeriod->insert($this->connection);
        $this->objectiveProgressReportOne->objective->insert($this->connection);
        $this->objectiveProgressReportOne->insert($this->connection);
        
        $uri = $this->asProgramCoordinatorUri . "/objective-progress-reports/{$this->objectiveProgressReportOne->id}/reject";
        $this->patch($uri, [], $this->coordinator->personnel->token);
    }
    public function test_reject_200()
    {
        $this->executeReject();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->objectiveProgressReportOne->id,
            'approvalStatus' => OKRPeriodApprovalStatus::REJECTED,
        ];
        $this->seeJsonContains($response);
        
        $entry = [
            'id' => $this->objectiveProgressReportOne->id,
            'status' => OKRPeriodApprovalStatus::REJECTED,
        ];
        $this->seeInDatabase('ObjectiveProgressReport', $entry);
    }
    public function test_reject_alreadyCancelled_403()
    {
        $this->objectiveProgressReportOne->cancelled = true;
        $this->executeReject();
        $this->seeStatusCode(403);
    }
    public function test_reject_alreadyConcluded_403()
    {
        $this->objectiveProgressReportOne->status = OKRPeriodApprovalStatus::REJECTED;
        $this->executeReject();
        $this->seeStatusCode(403);
    }
    
    protected function executeShow()
    {
        $this->objectiveProgressReportOne->objective->okrPeriod->participant->insert($this->connection);
        $this->objectiveProgressReportOne->objective->okrPeriod->insert($this->connection);
        $this->objectiveProgressReportOne->objective->insert($this->connection);
        $this->objectiveProgressReportOne->insert($this->connection);
        
        $this->keyResultPR11_objPR1_kr1->keyResult->insert($this->connection);
        $this->keyResultPR11_objPR1_kr1->insert($this->connection);
        $this->keyResultPR21_objPR1_kr2->keyResult->insert($this->connection);
        $this->keyResultPR21_objPR1_kr2->insert($this->connection);
        
        $uri = $this->asProgramCoordinatorUri . "/objective-progress-reports/{$this->objectiveProgressReportOne->id}";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
        $response = [
           'id' => $this->objectiveProgressReportOne->id,
           'reportDate' => $this->objectiveProgressReportOne->reportDate,
           'submitTime' => $this->objectiveProgressReportOne->submitTime,
           'approvalStatus' => $this->objectiveProgressReportOne->status,
           'cancelled' => $this->objectiveProgressReportOne->cancelled,
           'keyResultProgressReports' => [
               [
                   'id' => $this->keyResultPR11_objPR1_kr1->id,
                   'value' => $this->keyResultPR11_objPR1_kr1->value,
                   'disabled' => $this->keyResultPR11_objPR1_kr1->disabled,
                   'keyResult' => [
                       'id' => $this->keyResultPR11_objPR1_kr1->keyResult->id,
                       'name' => $this->keyResultPR11_objPR1_kr1->keyResult->name,
                       'target' => $this->keyResultPR11_objPR1_kr1->keyResult->target,
                       'weight' => $this->keyResultPR11_objPR1_kr1->keyResult->weight,
                   ],
               ],
               [
                   'id' => $this->keyResultPR21_objPR1_kr2->id,
                   'value' => $this->keyResultPR21_objPR1_kr2->value,
                   'disabled' => $this->keyResultPR21_objPR1_kr2->disabled,
                   'keyResult' => [
                       'id' => $this->keyResultPR21_objPR1_kr2->keyResult->id,
                       'name' => $this->keyResultPR21_objPR1_kr2->keyResult->name,
                       'target' => $this->keyResultPR21_objPR1_kr2->keyResult->target,
                       'weight' => $this->keyResultPR21_objPR1_kr2->keyResult->weight,
                   ],
               ],
           ],
       ];
       $this->seeJsonContains($response);
    }
    
    protected function executeShowAll()
    {
        $this->objectiveProgressReportOne->objective->okrPeriod->participant->insert($this->connection);
        $this->objectiveProgressReportOne->objective->okrPeriod->insert($this->connection);
        $this->objectiveProgressReportOne->objective->insert($this->connection);
        $this->objectiveProgressReportOne->insert($this->connection);
        $this->objectiveProgressReportTwo->insert($this->connection);
        
        $uri = $this->asProgramCoordinatorUri . "/objectives/{$this->objective->id}/objective-progress-reports";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        
        $objPROneReponse = [
            'id' => $this->objectiveProgressReportOne->id,
            'reportDate' => $this->objectiveProgressReportOne->reportDate,
            'submitTime' => $this->objectiveProgressReportOne->submitTime,
            'approvalStatus' => $this->objectiveProgressReportOne->status,
            'cancelled' => $this->objectiveProgressReportOne->cancelled,
        ];
        $this->seeJsonContains($objPROneReponse);
        
        $objPRTwoReponse = [
            'id' => $this->objectiveProgressReportTwo->id,
            'reportDate' => $this->objectiveProgressReportTwo->reportDate,
            'submitTime' => $this->objectiveProgressReportTwo->submitTime,
            'approvalStatus' => $this->objectiveProgressReportTwo->status,
            'cancelled' => $this->objectiveProgressReportTwo->cancelled,
        ];
        $this->seeJsonContains($objPRTwoReponse);
    }
}
