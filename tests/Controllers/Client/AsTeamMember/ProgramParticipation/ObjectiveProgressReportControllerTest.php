<?php

namespace Tests\Controllers\Team\AsTeamMember\ProgramParticipation;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\Controllers\Client\AsTeamMember\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\RecordOfKeyResultProgressReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfKeyResult;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfObjectiveProgressReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\RecordOfObjective;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfOKRPeriod;

class ObjectiveProgressReportControllerTest extends ProgramParticipationTestCase
{
    protected $objectiveProgressReportUri;
    protected $objectivePR1;
    protected $objectivePR2;
    protected $keyResultPR1_ObjPR1_KR1_11;
    protected $keyResultPR2_ObjPR1_KR2_12;
    protected $keyResultPR1_ObjPR2_KR1_21;

    protected $objective;
    protected $keyResultOne;
    protected $keyResultTwo;
    protected $objectiveProgressReportRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('OKRPeriod')->truncate();
        $this->connection->table('Objective')->truncate();
        $this->connection->table('ObjectiveProgressReport')->truncate();
        $this->connection->table('KeyResult')->truncate();
        $this->connection->table('KeyResultProgressReport')->truncate();
        
        $participant = $this->programParticipation->participant;
        
        $this->objectiveProgressReportUri = $this->programParticipationUri . "/{$participant->id}/objective-progress-reports";
        
        $okrPeriod = new RecordOfOKRPeriod($participant, 9);
        $okrPeriod->status = OKRPeriodApprovalStatus::APPROVED;
        
        $this->objective = new RecordOfObjective($okrPeriod, 9);
        
        $this->keyResultOne = new RecordOfKeyResult($this->objective, 1);
        $this->keyResultTwo = new RecordOfKeyResult($this->objective, 2);
        
        $this->objectivePR1 = new RecordOfObjectiveProgressReport($this->objective, 1);
        $this->objectivePR2 = new RecordOfObjectiveProgressReport($this->objective, 2);
        
        $this->keyResultPR1_ObjPR1_KR1_11 = new RecordOfKeyResultProgressReport($this->objectivePR1, $this->keyResultOne, 11);
        $this->keyResultPR2_ObjPR1_KR2_12 = new RecordOfKeyResultProgressReport($this->objectivePR1, $this->keyResultTwo, 12);
        $this->keyResultPR1_ObjPR2_KR1_21 = new RecordOfKeyResultProgressReport($this->objectivePR2, $this->keyResultOne, 21);
        
        $this->objectiveProgressReportRequest = [
            'reportDate' => (new DateTimeImmutable('- 5 days'))->format('Y-m-d'),
            'keyResultProgressReports' => [
                [
                    'keyResultId' => $this->keyResultOne->id,
                    'value' => 33,
                ],
                [
                    'keyResultId' => $this->keyResultTwo->id,
                    'value' => 66,
                ],
            ],
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
//        $this->connection->table('OKRPeriod')->truncate();
//        $this->connection->table('Objective')->truncate();
//        $this->connection->table('ObjectiveProgressReport')->truncate();
//        $this->connection->table('KeyResult')->truncate();
//        $this->connection->table('KeyResultProgressReport')->truncate();
    }
    
    protected function executeSubmit()
    {
        $this->objective->okrPeriod->insert($this->connection);
        $this->objective->insert($this->connection);
        $this->keyResultOne->insert($this->connection);
        $this->keyResultTwo->insert($this->connection);
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->participant->id}/objectives/{$this->objective->id}/objective-progress-reports";
        $this->post($uri, $this->objectiveProgressReportRequest, $this->teamMember->client->token);
    }
    public function test_submit_201()
    {
        $this->executeSubmit();
        $this->seeStatusCode(201);
        
        $response = [
            'reportDate' => $this->objectiveProgressReportRequest['reportDate'],
        ];
        $this->seeJsonContains($response);
        
        $objectiveProgressReportEntry = [
            'Objective_id' => $this->objective->id,
            'reportDate' => $this->objectiveProgressReportRequest['reportDate'],
        ];
        $this->seeInDatabase('ObjectiveProgressReport', $objectiveProgressReportEntry);
    }
    public function test_submit_aggregateKeyResultProgressReport()
    {
        $this->executeSubmit();
        $keyResultProgressReportOneResponse = [
            'value' => $this->objectiveProgressReportRequest['keyResultProgressReports'][0]['value'],
            'keyResult' => [
                'id' => $this->objectiveProgressReportRequest['keyResultProgressReports'][0]['keyResultId'],
                'name' => $this->keyResultOne->name,
                'target' => $this->keyResultOne->target,
                'weight' => $this->keyResultOne->weight,
            ],
        ];
        $this->seeJsonContains($keyResultProgressReportOneResponse);
        $keyResultProgressReportTwoResponse = [
            'value' => $this->objectiveProgressReportRequest['keyResultProgressReports'][0]['value'],
            'keyResult' => [
                'id' => $this->objectiveProgressReportRequest['keyResultProgressReports'][1]['keyResultId'],
                'name' => $this->keyResultTwo->name,
                'target' => $this->keyResultTwo->target,
                'weight' => $this->keyResultTwo->weight,
            ],
        ];
        $this->seeJsonContains($keyResultProgressReportTwoResponse);
        
        $keyResultProgressReportOneEntry = [
            'value' => $this->objectiveProgressReportRequest['keyResultProgressReports'][0]['value'],
            'KeyResult_id' => $this->objectiveProgressReportRequest['keyResultProgressReports'][0]['keyResultId'],
        ];
        $this->seeInDatabase('KeyResultProgressReport', $keyResultProgressReportOneEntry);
        $keyResultProgressReportTwoEntry = [
            'value' => $this->objectiveProgressReportRequest['keyResultProgressReports'][1]['value'],
            'KeyResult_id' => $this->objectiveProgressReportRequest['keyResultProgressReports'][1]['keyResultId'],
        ];
        $this->seeInDatabase('KeyResultProgressReport', $keyResultProgressReportTwoEntry);
    }
    public function test_submit_reportDateinFuture_forbidden()
    {
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime('+1 days'))->format('Y-m-d');
        $this->executeSubmit();
        $this->seeStatusCode(403);
    }
    public function test_submit_reportDateLessThanOKRPeriod_403()
    {
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objective->okrPeriod->startDate))->sub(new DateInterval('P1D'))->format('Y-m-d');
        $this->executeSubmit();
        $this->seeStatusCode(403);
    }
    public function test_submit_reportDateBiggerThanOKRPeriod_403()
    {
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objective->okrPeriod->endDate))->add(new DateInterval('P1D'))->format('Y-m-d');
        $this->executeSubmit();
        $this->seeStatusCode(403);
    }
    public function test_submit_reportDateConflictWithOtherReportProgress_409()
    {
        $this->objectivePR1->insert($this->connection);
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objectivePR1->reportDate))->format('Y-m-d');
        $this->executeSubmit();
        $this->seeStatusCode(409);
    }
    public function test_submit_reportDateConflictWithOtherReportProgressAlreadyCancelled_201()
    {
        $this->objectivePR1->cancelled = true;
        $this->objectivePR1->insert($this->connection);
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objectivePR1->reportDate))->format('Y-m-d');
        $this->executeSubmit();
        $this->seeStatusCode(201);
    }
    public function test_submit_reportDateConflictWithOtherReportProgressAlreadyRejected_201()
    {
        $this->objectivePR1->status = OKRPeriodApprovalStatus::REJECTED;
        $this->objectivePR1->insert($this->connection);
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objectivePR1->reportDate))->format('Y-m-d');
        $this->executeSubmit();
        $this->seeStatusCode(201);
    }
    public function test_submit_missingKeyResultProgressReportInProgress_skipAggregatingThisKeyResultProgressReport()
    {
        unset($this->objectiveProgressReportRequest['keyResultProgressReports'][1]);
        $this->executeSubmit();
        $this->notSeeInDatabase('KeyResultProgressReport', ['KeyResult_id' => $this->keyResultTwo->id]);
    }
    public function test_submit_disabledObjective_forbidden()
    {
        $this->objective->disabled = true;
        $this->executeSubmit();
        $this->seeStatusCode(403);
    }
    public function test_submit_cancelledOKRPeriod_forbidden()
    {
        $this->objective->okrPeriod->cancelled = true;
        $this->executeSubmit();
        $this->seeStatusCode(403);
    }
    public function test_submit_notApprovedOKRPeriod_forbidden()
    {
        $this->objective->okrPeriod->status = OKRPeriodApprovalStatus::UNCONCLUDED;
        $this->executeSubmit();
        $this->seeStatusCode(403);
    }
    
    protected function executeUpdate()
    {
        $this->objective->okrPeriod->insert($this->connection);
        $this->objective->insert($this->connection);
        $this->keyResultOne->insert($this->connection);
        $this->keyResultTwo->insert($this->connection);
        
        $this->objectivePR1->insert($this->connection);
        $this->keyResultPR1_ObjPR1_KR1_11->insert($this->connection);
        
        $uri = $this->objectiveProgressReportUri . "/{$this->objectivePR1->id}";
        $this->patch($uri, $this->objectiveProgressReportRequest, $this->teamMember->client->token);
    }
    public function test_update_200()
    {
        $this->executeUpdate();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->objectivePR1->id,
            'reportDate' => $this->objectiveProgressReportRequest['reportDate'],
        ];
        $this->seeJsonContains($response);
        
        $objectivePREntry = [
            'id' => $this->objectivePR1->id,
            'reportDate' => $this->objectiveProgressReportRequest['reportDate'],
        ];
        $this->seeInDatabase('ObjectiveProgressReport', $objectivePREntry);
    }
    public function test_update_updateKeyResultProgressReport()
    {
        $this->executeUpdate();
        $response = [
            'id' => $this->keyResultPR1_ObjPR1_KR1_11->id,
            'value' => $this->objectiveProgressReportRequest['keyResultProgressReports'][0]['value'],
        ];
        $this->seeJsonContains($response);
        
        $keyResultPREntry = [
            'KeyResult_id' => $this->keyResultOne->id,
            'id' => $this->keyResultPR1_ObjPR1_KR1_11->id,
            'value' => $this->objectiveProgressReportRequest['keyResultProgressReports'][0]['value'],
        ];
        $this->seeInDatabase('KeyResultProgressReport', $keyResultPREntry);
    }
    public function test_update_aggregateNewKeyResultProgressReport()
    {
        $this->executeUpdate();
        $response = [
            'value' => $this->objectiveProgressReportRequest['keyResultProgressReports'][1]['value'],
            'keyResult' => [
                'id' => $this->keyResultTwo->id,
                'name' => $this->keyResultTwo->name,
                'target' => $this->keyResultTwo->target,
                'weight' => $this->keyResultTwo->weight,
            ],
        ];
        $this->seeJsonContains($response);
        
        $keyResultPREntry = [
            'ObjectiveProgressReport_id' => $this->objectivePR1->id,
            'KeyResult_id' => $this->keyResultTwo->id,
            'value' => $this->objectiveProgressReportRequest['keyResultProgressReports'][1]['value'],
        ];
        $this->seeInDatabase('KeyResultProgressReport', $keyResultPREntry);
    }
    public function test_update_disableProgressReportCorrespondWithDisableKeyResult()
    {
        $this->keyResultOne->disabled = true;
        $this->executeUpdate();
        $keyResultPREntry = [
            'id' => $this->keyResultPR1_ObjPR1_KR1_11->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('KeyResultProgressReport', $keyResultPREntry);
    }
    public function test_update_updatedKeyResultProgressDataNotExist_doNothing()
    {
        unset($this->objectiveProgressReportRequest['keyResultProgressReports'][0]);
        $this->executeUpdate();
        $keyResultPREntry = [
            'id' => $this->keyResultPR1_ObjPR1_KR1_11->id,
            'value' => $this->keyResultPR1_ObjPR1_KR1_11->value,
            'disabled' => false,
        ];
        $this->seeInDatabase('KeyResultProgressReport', $keyResultPREntry);
    }
    public function test_update_reportDateinFuture_forbidden()
    {
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime('+1 days'))->format('Y-m-d');
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    public function test_update_reportDateLessThanOKRPeriod_403()
    {
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objective->okrPeriod->startDate))->sub(new DateInterval('P1D'))->format('Y-m-d');
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    public function test_update_reportDateBiggerThanOKRPeriod_403()
    {
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objective->okrPeriod->endDate))->add(new DateInterval('P1D'))->format('Y-m-d');
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    public function test_update_reportDateConflictWithOtherReportProgress_409()
    {
        $this->objectivePR2->insert($this->connection);
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objectivePR2->reportDate))->format('Y-m-d');
        $this->executeUpdate();
        $this->seeStatusCode(409);
    }
    public function test_update_reportDateConflictWithOtherReportProgressAlreadyCancelled_201()
    {
        $this->objectivePR2->cancelled = true;
        $this->objectivePR2->insert($this->connection);
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objectivePR2->reportDate))->format('Y-m-d');
        $this->executeUpdate();
        $this->seeStatusCode(200);
    }
    public function test_update_reportDateConflictWithOtherReportProgressAlreadyRejected_201()
    {
        $this->objectivePR2->status = OKRPeriodApprovalStatus::REJECTED;
        $this->objectivePR2->insert($this->connection);
        $this->objectiveProgressReportRequest['reportDate'] = (new DateTime($this->objectivePR2->reportDate))->format('Y-m-d');
        $this->executeUpdate();
        $this->seeStatusCode(200);
    }
    public function test_update_disabledObjective_forbidden()
    {
        $this->objective->disabled = true;
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    public function test_update_cancelledOKRPeriod_forbidden()
    {
        $this->objective->okrPeriod->cancelled = true;
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    public function test_update_notApprovedOKRPeriod_forbidden()
    {
        $this->objective->okrPeriod->status = OKRPeriodApprovalStatus::UNCONCLUDED;
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    
    protected function executeCancel()
    {
        $this->objective->okrPeriod->insert($this->connection);
        $this->objective->insert($this->connection);
        
        $this->objectivePR1->insert($this->connection);
        
        $uri = $this->objectiveProgressReportUri . "/{$this->objectivePR1->id}";
        $this->delete($uri, [], $this->teamMember->client->token);
    }
    public function test_cancel_200()
    {
        $this->executeCancel();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->objectivePR1->id,
            'cancelled' => true,
        ];
        $this->seeJsonContains($response);
        
        $entry = [
            'id' => $this->objectivePR1->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('ObjectiveProgressReport', $entry);
   }
   public function test_cancel_alreadyCancelled_403()
   {
       $this->objectivePR1->cancelled = true;
       $this->executeCancel();
       $this->seeStatusCode(403);
   }
   public function test_cancel_alreadyConcluded_403()
   {
       $this->objectivePR1->status = OKRPeriodApprovalStatus::REJECTED;
       $this->executeCancel();
       $this->seeStatusCode(403);
   }
   
   protected function executeShow()
   {
       $this->objective->okrPeriod->insert($this->connection);
       $this->objective->insert($this->connection);
       $this->keyResultOne->insert($this->connection);
       $this->keyResultTwo->insert($this->connection);
       
       $this->objectivePR1->insert($this->connection);
       $this->keyResultPR1_ObjPR1_KR1_11->insert($this->connection);
       $this->keyResultPR2_ObjPR1_KR2_12->insert($this->connection);
       
       $uri = $this->objectiveProgressReportUri . "/{$this->objectivePR1->id}";
       $this->get($uri, $this->teamMember->client->token);       
   }
   public function test_show_200()
   {
       $this->executeShow();
       $response = [
           'id' => $this->objectivePR1->id,
           'reportDate' => $this->objectivePR1->reportDate,
           'submitTime' => $this->objectivePR1->submitTime,
           'approvalStatus' => $this->objectivePR1->status,
           'cancelled' => $this->objectivePR1->cancelled,
           'keyResultProgressReports' => [
               [
                   'id' => $this->keyResultPR1_ObjPR1_KR1_11->id,
                   'value' => $this->keyResultPR1_ObjPR1_KR1_11->value,
                   'disabled' => $this->keyResultPR1_ObjPR1_KR1_11->disabled,
                   'keyResult' => [
                       'id' => $this->keyResultPR1_ObjPR1_KR1_11->keyResult->id,
                       'name' => $this->keyResultPR1_ObjPR1_KR1_11->keyResult->name,
                       'target' => $this->keyResultPR1_ObjPR1_KR1_11->keyResult->target,
                       'weight' => $this->keyResultPR1_ObjPR1_KR1_11->keyResult->weight,
                   ],
               ],
               [
                   'id' => $this->keyResultPR2_ObjPR1_KR2_12->id,
                   'value' => $this->keyResultPR2_ObjPR1_KR2_12->value,
                   'disabled' => $this->keyResultPR2_ObjPR1_KR2_12->disabled,
                   'keyResult' => [
                       'id' => $this->keyResultPR2_ObjPR1_KR2_12->keyResult->id,
                       'name' => $this->keyResultPR2_ObjPR1_KR2_12->keyResult->name,
                       'target' => $this->keyResultPR2_ObjPR1_KR2_12->keyResult->target,
                       'weight' => $this->keyResultPR2_ObjPR1_KR2_12->keyResult->weight,
                   ],
               ],
           ],
       ];
       $this->seeJsonContains($response);
   }
   
   protected function executeShowAll()
   {
       $this->objective->okrPeriod->insert($this->connection);
       $this->objective->insert($this->connection);
       $this->keyResultOne->insert($this->connection);
       $this->keyResultTwo->insert($this->connection);
       
       $this->objectivePR1->insert($this->connection);
       $this->objectivePR2->insert($this->connection);
       $this->keyResultPR1_ObjPR1_KR1_11->insert($this->connection);
       $this->keyResultPR2_ObjPR1_KR2_12->insert($this->connection);
       $this->keyResultPR1_ObjPR2_KR1_21->insert($this->connection);
       
       $uri = $this->programParticipationUri . "/{$this->programParticipation->participant->id}/objectives/{$this->objective->id}/objective-progress-reports";
       $this->get($uri, $this->teamMember->client->token);
   }
   public function test_showAll_200()
   {
       $this->executeShowAll();
       $this->seeStatusCode(200);
       
       $totalResponse = ['total' => 2];
       $this->seeJsonContains($totalResponse);
       $objPROneResponse = [
           'id' => $this->objectivePR1->id,
           'reportDate' => $this->objectivePR1->reportDate,
           'submitTime' => $this->objectivePR1->submitTime,
           'approvalStatus' => $this->objectivePR1->status,
           'cancelled' => $this->objectivePR1->cancelled,
           'keyResultProgressReports' => [
               [
                   'id' => $this->keyResultPR1_ObjPR1_KR1_11->id,
                   'value' => $this->keyResultPR1_ObjPR1_KR1_11->value,
                   'disabled' => $this->keyResultPR1_ObjPR1_KR1_11->disabled,
                   'keyResult' => [
                       'id' => $this->keyResultPR1_ObjPR1_KR1_11->keyResult->id,
                       'name' => $this->keyResultPR1_ObjPR1_KR1_11->keyResult->name,
                       'target' => $this->keyResultPR1_ObjPR1_KR1_11->keyResult->target,
                       'weight' => $this->keyResultPR1_ObjPR1_KR1_11->keyResult->weight,
                   ],
               ],
               [
                   'id' => $this->keyResultPR2_ObjPR1_KR2_12->id,
                   'value' => $this->keyResultPR2_ObjPR1_KR2_12->value,
                   'disabled' => $this->keyResultPR2_ObjPR1_KR2_12->disabled,
                   'keyResult' => [
                       'id' => $this->keyResultPR2_ObjPR1_KR2_12->keyResult->id,
                       'name' => $this->keyResultPR2_ObjPR1_KR2_12->keyResult->name,
                       'target' => $this->keyResultPR2_ObjPR1_KR2_12->keyResult->target,
                       'weight' => $this->keyResultPR2_ObjPR1_KR2_12->keyResult->weight,
                   ],
               ],
           ],
       ];
       $this->seeJsonContains($objPROneResponse);
       $objPRTwoResponse = [
           'id' => $this->objectivePR2->id,
           'reportDate' => $this->objectivePR2->reportDate,
           'submitTime' => $this->objectivePR2->submitTime,
           'approvalStatus' => $this->objectivePR2->status,
           'cancelled' => $this->objectivePR2->cancelled,
           'keyResultProgressReports' => [
               [
                   'id' => $this->keyResultPR1_ObjPR2_KR1_21->id,
                   'value' => $this->keyResultPR1_ObjPR2_KR1_21->value,
                   'disabled' => $this->keyResultPR1_ObjPR2_KR1_21->disabled,
                   'keyResult' => [
                       'id' => $this->keyResultPR1_ObjPR2_KR1_21->keyResult->id,
                       'name' => $this->keyResultPR1_ObjPR2_KR1_21->keyResult->name,
                       'target' => $this->keyResultPR1_ObjPR2_KR1_21->keyResult->target,
                       'weight' => $this->keyResultPR1_ObjPR2_KR1_21->keyResult->weight,
                   ],
               ],
           ],
       ];
       $this->seeJsonContains($objPRTwoResponse);
   }
}
