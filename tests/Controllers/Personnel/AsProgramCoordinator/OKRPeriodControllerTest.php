<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\RecordOfKeyResultProgressReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfKeyResult;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfObjectiveProgressReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\RecordOfObjective;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfOKRPeriod;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;

class OKRPeriodControllerTest extends AsProgramCoordinatorTestCase
{
    protected $participant;
    protected $okrPeriodOne;
    protected $okrPeriodTwo;
    
    protected $objective1_okrPeriod1_11;
    protected $objective2_okrPeriod1_12;
    protected $objective1_okrPeriod2_21;
    
    protected $keyResult1_objective11_111;
    protected $keyResult2_objective11_112;
    protected $keyResult1_objective12_121;
    protected $keyResult1_objective21_211;
    
    protected $objectivePR1_objective11_111_approved;
    protected $objectivePR2_objective11_112;
    
    protected $keyResultPR1_kr111_objPR111_1111;
    protected $keyResultPR2_kr112_objPR111_1112;
    protected $keyResultPR1_kr111_objPR112_1121;
    protected $keyResultPR2_kr112_objPR112_1122;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('OKRPeriod')->truncate();
        $this->connection->table('Objective')->truncate();
        $this->connection->table('KeyResult')->truncate();
        $this->connection->table('ObjectiveProgressReport')->truncate();
        $this->connection->table('KeyResultProgressReport')->truncate();
        
        $program = $this->coordinator->program;
        
        $this->participant = new RecordOfParticipant($program, 9);
        
        $this->okrPeriodOne = new RecordOfOKRPeriod($this->participant, 1);
        $this->okrPeriodTwo = new RecordOfOKRPeriod($this->participant, 2);
        
        $this->objective1_okrPeriod1_11 = new RecordOfObjective($this->okrPeriodOne, 11);
        $this->objective2_okrPeriod1_12 = new RecordOfObjective($this->okrPeriodOne, 12);
        $this->objective1_okrPeriod2_21 = new RecordOfObjective($this->okrPeriodTwo, 21);
        
        $this->keyResult1_objective11_111 = new RecordOfKeyResult($this->objective1_okrPeriod1_11, 111);
        $this->keyResult2_objective11_112 = new RecordOfKeyResult($this->objective1_okrPeriod1_11, 112);
        $this->keyResult1_objective12_121 = new RecordOfKeyResult($this->objective2_okrPeriod1_12, 121);
        $this->keyResult1_objective21_211 = new RecordOfKeyResult($this->objective1_okrPeriod2_21, 211);
        
        $this->objectivePR1_objective11_111_approved = new RecordOfObjectiveProgressReport($this->objective1_okrPeriod1_11, 111);
        $this->objectivePR1_objective11_111_approved->status = OKRPeriodApprovalStatus::APPROVED;
        $this->objectivePR2_objective11_112 = new RecordOfObjectiveProgressReport($this->objective1_okrPeriod1_11, 112);
        
        $this->keyResultPR1_kr111_objPR111_1111 = new RecordOfKeyResultProgressReport($this->objectivePR1_objective11_111_approved, $this->keyResult1_objective11_111, 1111);
        $this->keyResultPR2_kr112_objPR111_1112 = new RecordOfKeyResultProgressReport($this->objectivePR1_objective11_111_approved, $this->keyResult2_objective11_112, 1112);
        $this->keyResultPR1_kr111_objPR112_1121 = new RecordOfKeyResultProgressReport($this->objectivePR2_objective11_112, $this->keyResult1_objective11_111, 1121);
        $this->keyResultPR2_kr112_objPR112_1122 = new RecordOfKeyResultProgressReport($this->objectivePR2_objective11_112, $this->keyResult2_objective11_112, 1122);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('OKRPeriod')->truncate();
        $this->connection->table('Objective')->truncate();
        $this->connection->table('KeyResult')->truncate();
        $this->connection->table('ObjectiveProgressReport')->truncate();
        $this->connection->table('KeyResultProgressReport')->truncate();
    }
    
    protected function executeApprove()
    {
        $this->participant->insert($this->connection);
        $this->okrPeriodOne->insert($this->connection);
        $uri = $this->asProgramCoordinatorUri . "/okr-periods/{$this->okrPeriodOne->id}/approve";
        $this->patch($uri, [], $this->coordinator->personnel->token);
    }
    public function test_approve_200()
    {
        $this->executeApprove();
        $this->seeStatusCode(200);
        $response = [
            'id' => $this->okrPeriodOne->id,
            'approvalStatus' => OKRPeriodApprovalStatus::APPROVED,
        ];
        $this->seeJsonContains($response);
        
        $entry = [
            'id' => $this->okrPeriodOne->id,
            'status' => OKRPeriodApprovalStatus::APPROVED,
        ];
        $this->seeInDatabase('OKRPeriod', $entry);
    }
    public function test_approve_alreadyCancelled_403()
    {
        $this->okrPeriodOne->cancelled = true;
        $this->executeApprove();
        $this->seeStatusCode(403);
    }
    public function test_approve_alreadyConcluded_403()
    {
        $this->okrPeriodOne->status = OKRPeriodApprovalStatus::APPROVED;
        $this->executeApprove();
        $this->seeStatusCode(403);
    }
    
    protected function executeReject()
    {
        $this->participant->insert($this->connection);
        $this->okrPeriodOne->insert($this->connection);
        $uri = $this->asProgramCoordinatorUri . "/okr-periods/{$this->okrPeriodOne->id}/reject";
        $this->patch($uri, [], $this->coordinator->personnel->token);
    }
    public function test_Reject_200()
    {
        $this->executeReject();
        $this->seeStatusCode(200);
        $response = [
            'id' => $this->okrPeriodOne->id,
            'approvalStatus' => OKRPeriodApprovalStatus::REJECTED,
        ];
        $this->seeJsonContains($response);
        
        $entry = [
            'id' => $this->okrPeriodOne->id,
            'status' => OKRPeriodApprovalStatus::REJECTED,
        ];
        $this->seeInDatabase('OKRPeriod', $entry);
    }
    public function test_Reject_alreadyCancelled_403()
    {
        $this->okrPeriodOne->cancelled = true;
        $this->executeReject();
        $this->seeStatusCode(403);
    }
    public function test_Reject_alreadyConcluded_403()
    {
        $this->okrPeriodOne->status = OKRPeriodApprovalStatus::REJECTED;
        $this->executeReject();
        $this->seeStatusCode(403);
    }
    
    protected function executeShow()
    {
        $this->participant->insert($this->connection);
        $this->okrPeriodOne->insert($this->connection);
        
        $this->objective1_okrPeriod1_11->insert($this->connection);
        $this->objective2_okrPeriod1_12->insert($this->connection);
        
        $this->keyResult1_objective11_111->insert($this->connection);
        $this->keyResult2_objective11_112->insert($this->connection);
        $this->keyResult1_objective12_121->insert($this->connection);
        
        $this->objectivePR1_objective11_111_approved->insert($this->connection);
        $this->objectivePR2_objective11_112->insert($this->connection);
        
        $this->keyResultPR1_kr111_objPR111_1111->insert($this->connection);
        $this->keyResultPR2_kr112_objPR111_1112->insert($this->connection);
        $this->keyResultPR1_kr111_objPR112_1121->insert($this->connection);
        $this->keyResultPR2_kr112_objPR112_1122->insert($this->connection);
        
        $uri = $this->asProgramCoordinatorUri . "/okr-periods/{$this->okrPeriodOne->id}";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
                
        $response = [
            'id' => $this->okrPeriodOne->id,
            'name' => $this->okrPeriodOne->name,
            'description' => $this->okrPeriodOne->description,
            'startDate' => $this->okrPeriodOne->startDate,
            'endDate' => $this->okrPeriodOne->endDate,
            'approvalStatus' => $this->okrPeriodOne->status,
            'cancelled' => $this->okrPeriodOne->cancelled,
            'objectives' => [
                [
                    'id' => $this->objective1_okrPeriod1_11->id,
                    'name' => $this->objective1_okrPeriod1_11->name,
                    'description' => $this->objective1_okrPeriod1_11->description,
                    'weight' => $this->objective1_okrPeriod1_11->weight,
                    'disabled' => $this->objective1_okrPeriod1_11->disabled,
                    'lastApprovedProgressReport' => [
                        'id' => $this->objectivePR1_objective11_111_approved->id,
                        'reportDate' => $this->objectivePR1_objective11_111_approved->reportDate,
                        'submitTime' => $this->objectivePR1_objective11_111_approved->submitTime,
                        'approvalStatus' => $this->objectivePR1_objective11_111_approved->status,
                        'cancelled' => $this->objectivePR1_objective11_111_approved->cancelled,
                        'keyResultProgressReports' => [
                            [
                                'id' => $this->keyResultPR1_kr111_objPR111_1111->id,
                                'value' => $this->keyResultPR1_kr111_objPR111_1111->value,
                                'disabled' => $this->keyResultPR1_kr111_objPR111_1111->disabled,
                                'keyResult' => [
                                    'id' => $this->keyResultPR1_kr111_objPR111_1111->keyResult->id,
                                    'name' => $this->keyResultPR1_kr111_objPR111_1111->keyResult->name,
                                    'target' => $this->keyResultPR1_kr111_objPR111_1111->keyResult->target,
                                    'weight' => $this->keyResultPR1_kr111_objPR111_1111->keyResult->weight,
                                ],
                            ],
                            [
                                'id' => $this->keyResultPR2_kr112_objPR111_1112->id,
                                'value' => $this->keyResultPR2_kr112_objPR111_1112->value,
                                'disabled' => $this->keyResultPR2_kr112_objPR111_1112->disabled,
                                'keyResult' => [
                                    'id' => $this->keyResultPR2_kr112_objPR111_1112->keyResult->id,
                                    'name' => $this->keyResultPR2_kr112_objPR111_1112->keyResult->name,
                                    'target' => $this->keyResultPR2_kr112_objPR111_1112->keyResult->target,
                                    'weight' => $this->keyResultPR2_kr112_objPR111_1112->keyResult->weight,
                                ],
                            ],
                        ],
                    ],
                    'keyResults' => [
                        [
                            'id' => $this->keyResult1_objective11_111->id,
                            'name' => $this->keyResult1_objective11_111->name,
                            'description' => $this->keyResult1_objective11_111->description,
                            'target' => $this->keyResult1_objective11_111->target,
                            'weight' => $this->keyResult1_objective11_111->weight,
                            'disabled' => $this->keyResult1_objective11_111->disabled,
                        ],
                        [
                            'id' => $this->keyResult2_objective11_112->id,
                            'name' => $this->keyResult2_objective11_112->name,
                            'description' => $this->keyResult2_objective11_112->description,
                            'target' => $this->keyResult2_objective11_112->target,
                            'weight' => $this->keyResult2_objective11_112->weight,
                            'disabled' => $this->keyResult2_objective11_112->disabled,
                        ],
                    ],
                ],
                [
                    'id' => $this->objective2_okrPeriod1_12->id,
                    'name' => $this->objective2_okrPeriod1_12->name,
                    'description' => $this->objective2_okrPeriod1_12->description,
                    'weight' => $this->objective2_okrPeriod1_12->weight,
                    'disabled' => $this->objective2_okrPeriod1_12->disabled,
                    'lastApprovedProgressReport' => null,
                    'keyResults' => [
                        [
                            'id' => $this->keyResult1_objective12_121->id,
                            'name' => $this->keyResult1_objective12_121->name,
                            'description' => $this->keyResult1_objective12_121->description,
                            'target' => $this->keyResult1_objective12_121->target,
                            'weight' => $this->keyResult1_objective12_121->weight,
                            'disabled' => $this->keyResult1_objective12_121->disabled,
                        ],
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeShowAll()
    {
        $this->participant->insert($this->connection);
        $this->okrPeriodOne->insert($this->connection);
        $this->okrPeriodTwo->insert($this->connection);
        $uri = $this->asProgramCoordinatorUri . "/participants/{$this->participant->id}/okr-periods";
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_showAll_200()
    {
$this->disableExceptionHandling();
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $okrOneReponse = [
            'id' => $this->okrPeriodOne->id,
            'name' => $this->okrPeriodOne->name,
            'startDate' => $this->okrPeriodOne->startDate,
            'endDate' => $this->okrPeriodOne->endDate,
            'approvalStatus' => $this->okrPeriodOne->status,
            'cancelled' => $this->okrPeriodOne->cancelled,
        ];
        $this->seeJsonContains($okrOneReponse);
        
        $okrTwoReponse = [
            'id' => $this->okrPeriodTwo->id,
            'name' => $this->okrPeriodTwo->name,
            'startDate' => $this->okrPeriodTwo->startDate,
            'endDate' => $this->okrPeriodTwo->endDate,
            'approvalStatus' => $this->okrPeriodTwo->status,
            'cancelled' => $this->okrPeriodTwo->cancelled,
        ];
        $this->seeJsonContains($okrTwoReponse);
    }
    
}
