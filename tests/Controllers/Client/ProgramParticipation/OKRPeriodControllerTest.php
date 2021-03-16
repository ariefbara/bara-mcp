<?php

namespace Tests\Controllers\Client\AsProgramParticipant;

use DateTime;
use SharedContext\Domain\ValueObject\OKRPeriodApprovalStatus;
use Tests\Controllers\Client\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\ObjectiveProgressReport\RecordOfKeyResultProgressReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfKeyResult;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\Objective\RecordOfObjectiveProgressReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\OKRPeriod\RecordOfObjective;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfOKRPeriod;

class OKRPeriodControllerTest extends ProgramParticipationTestCase
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
    
    protected $createRequest;
    protected $updateRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('OKRPeriod')->truncate();
        $this->connection->table('Objective')->truncate();
        $this->connection->table('KeyResult')->truncate();
        $this->connection->table('ObjectiveProgressReport')->truncate();
        $this->connection->table('KeyResultProgressReport')->truncate();
        
        $this->participant = $this->programParticipation->participant;
        
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
        
        $this->createRequest = [
            'name' => 'new okr period name',
            'description' => 'new okr period description',
            'startDate' => (new DateTime('+2 months'))->format('Y-m-d'),
            'endDate' => (new DateTime('+3 months'))->format('Y-m-d'),
            'objectives' => [
                [
                    'name' => 'objective one name',
                    'description' => 'objective one description',
                    'weight' => 70,
                    'keyResults' => [
                        [
                            'name' => 'key result one objective one name',
                            'description' => 'key result one objective one description',
                            'target' => 111111,
                            'weight' => 50,
                        ],
                        [
                            'name' => 'key result two objective one name',
                            'description' => 'key result twoobjective one description',
                            'target' => 222222,
                            'weight' => 50,
                        ],
                    ],
                ],
                [
                    'name' => 'objective two name',
                    'description' => 'objective two description',
                    'weight' => 30,
                    'keyResults' => [
                        [
                            'name' => 'key result one objective two name',
                            'description' => 'key result one objective two description',
                            'target' => 121212,
                            'weight' => 100,
                        ],
                    ],
                ],
            ],
        ];
        
        $this->updateRequest = [
            'name' => 'new okr period name',
            'description' => 'new okr period description',
            'startDate' => (new DateTime('+2 months'))->format('Y-m-d'),
            'endDate' => (new DateTime('+3 months'))->format('Y-m-d'),
            'objectives' => [
                [
                    'id' => $this->objective1_okrPeriod1_11->id,
                    'name' => 'objective one name',
                    'description' => 'objective one description',
                    'weight' => 70,
                    'keyResults' => [
                        [
                            'id' => $this->keyResult1_objective11_111->id,
                            'name' => 'key result one objective one name',
                            'description' => 'key result one objective one description',
                            'target' => 111111,
                            'weight' => 50,
                        ],
                    ],
                ],
            ],
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('OKRPeriod')->truncate();
        $this->connection->table('Objective')->truncate();
        $this->connection->table('KeyResult')->truncate();
        $this->connection->table('ObjectiveProgressReport')->truncate();
        $this->connection->table('KeyResultProgressReport')->truncate();
    }
    
    protected function executeCreate()
    {
        $uri = $this->programParticipationUri . "/{$this->participant->id}/okr-periods";
        $this->post($uri, $this->createRequest, $this->client->token);
    }
    public function test_create_201()
    {
        $this->executeCreate();
        $this->seeStatusCode(201);
        
        $response = [
            'name' => $this->createRequest['name'],
            'description' => $this->createRequest['description'],
            'startDate' => $this->createRequest['startDate'],
            'endDate' => $this->createRequest['endDate'],
            'approvalStatus' => 0,
            'cancelled' => false,
        ];
        $this->seeJsonContains($response);
        
        $okrPeriodEntry = [
            'name' => $this->createRequest['name'],
            'description' => $this->createRequest['description'],
            'startDate' => $this->createRequest['startDate'],
            'endDate' => $this->createRequest['endDate'],
            'status' => 0,
            'cancelled' => false,
        ];
        $this->seeInDatabase('OKRPeriod', $okrPeriodEntry);
    }
    public function test_create_aggregateObjectives()
    {
        $this->executeCreate();
        $this->seeStatusCode(201);
        
        $objectiveOneResponse = [
            'name' => $this->createRequest['objectives'][0]['name'],
            'description' => $this->createRequest['objectives'][0]['description'],
            'weight' => $this->createRequest['objectives'][0]['weight'],
            'disabled' => false,
        ];
        $this->seeJsonContains($objectiveOneResponse);
        
        $objectiveTwoResponse = [
            'name' => $this->createRequest['objectives'][1]['name'],
            'description' => $this->createRequest['objectives'][1]['description'],
            'weight' => $this->createRequest['objectives'][1]['weight'],
            'disabled' => false,
        ];
        $this->seeJsonContains($objectiveTwoResponse);
        
        $objectiveOneEntry = [
            'name' => $this->createRequest['objectives'][0]['name'],
            'description' => $this->createRequest['objectives'][0]['description'],
            'weight' => $this->createRequest['objectives'][0]['weight'],
            'disabled' => false,
        ];
        $this->seeInDatabase('Objective', $objectiveOneEntry);
        
        $objectiveTwoEntry = [
            'name' => $this->createRequest['objectives'][1]['name'],
            'description' => $this->createRequest['objectives'][1]['description'],
            'weight' => $this->createRequest['objectives'][1]['weight'],
            'disabled' => false,
        ];
        $this->seeInDatabase('Objective', $objectiveTwoEntry);
    }
    public function test_create_aggregateKeyResults()
    {
        $this->executeCreate();
        $this->seeStatusCode(201);
        
        $keyResultOneObjectiveOneResponse = [
            'name' => $this->createRequest['objectives'][0]['keyResults'][0]['name'],
            'description' => $this->createRequest['objectives'][0]['keyResults'][0]['description'],
            'target' => $this->createRequest['objectives'][0]['keyResults'][0]['target'],
            'weight' => $this->createRequest['objectives'][0]['keyResults'][0]['weight'],
            'disabled' => false
        ];
        $this->seeJsonContains($keyResultOneObjectiveOneResponse);
        $keyResultTwoObjectiveOneResponse = [
            'name' => $this->createRequest['objectives'][0]['keyResults'][1]['name'],
            'description' => $this->createRequest['objectives'][0]['keyResults'][1]['description'],
            'target' => $this->createRequest['objectives'][0]['keyResults'][1]['target'],
            'weight' => $this->createRequest['objectives'][0]['keyResults'][1]['weight'],
            'disabled' => false
        ];
        $this->seeJsonContains($keyResultTwoObjectiveOneResponse);
        $keyResultOneObjectiveTwoResponse = [
            'name' => $this->createRequest['objectives'][1]['keyResults'][0]['name'],
            'description' => $this->createRequest['objectives'][1]['keyResults'][0]['description'],
            'target' => $this->createRequest['objectives'][1]['keyResults'][0]['target'],
            'weight' => $this->createRequest['objectives'][1]['keyResults'][0]['weight'],
            'disabled' => false
        ];
        $this->seeJsonContains($keyResultOneObjectiveTwoResponse);
        
        $keyResultOneObjectiveOneEntry = [
            'name' => $this->createRequest['objectives'][0]['keyResults'][0]['name'],
            'description' => $this->createRequest['objectives'][0]['keyResults'][0]['description'],
            'target' => $this->createRequest['objectives'][0]['keyResults'][0]['target'],
            'weight' => $this->createRequest['objectives'][0]['keyResults'][0]['weight'],
            'disabled' => false
        ];
        $this->seeInDatabase('KeyResult', $keyResultOneObjectiveOneEntry);
        $keyResultTwoObjectiveOneEntry = [
            'name' => $this->createRequest['objectives'][0]['keyResults'][1]['name'],
            'description' => $this->createRequest['objectives'][0]['keyResults'][1]['description'],
            'target' => $this->createRequest['objectives'][0]['keyResults'][1]['target'],
            'weight' => $this->createRequest['objectives'][0]['keyResults'][1]['weight'],
            'disabled' => false
        ];
        $this->seeInDatabase('KeyResult', $keyResultTwoObjectiveOneEntry);
        $keyResultOneObjectiveTwoEntry = [
            'name' => $this->createRequest['objectives'][1]['keyResults'][0]['name'],
            'description' => $this->createRequest['objectives'][1]['keyResults'][0]['description'],
            'target' => $this->createRequest['objectives'][1]['keyResults'][0]['target'],
            'weight' => $this->createRequest['objectives'][1]['keyResults'][0]['weight'],
            'disabled' => false
        ];
        $this->seeInDatabase('KeyResult', $keyResultOneObjectiveTwoEntry);
    }
    public function test_create_periodConflictWithExistingOKRPeriod_409()
    {
        $this->okrPeriodOne->insert($this->connection);
        $this->createRequest['startDate'] = $this->okrPeriodOne->startDate;
        $this->createRequest['endDate'] = $this->okrPeriodOne->endDate;
        
        $this->executeCreate();
        $this->seeStatusCode(409);
    }
    public function test_create_conflictedPeriodAlreadyCancelled_201()
    {
        $this->okrPeriodOne->cancelled = true;
        $this->okrPeriodOne->insert($this->connection);
        $this->createRequest['startDate'] = $this->okrPeriodOne->startDate;
        $this->createRequest['endDate'] = $this->okrPeriodOne->endDate;
        
        $this->executeCreate();
        $this->seeStatusCode(201);
    }
    public function test_create_conflictedPeriodAlreadyRejected_201()
    {
        $this->okrPeriodOne->status = OKRPeriodApprovalStatus::REJECTED;
        $this->okrPeriodOne->insert($this->connection);
        $this->createRequest['startDate'] = $this->okrPeriodOne->startDate;
        $this->createRequest['endDate'] = $this->okrPeriodOne->endDate;
        
        $this->executeCreate();
        $this->seeStatusCode(201);
    }
    public function test_create_emptyOKRPeriodName_400()
    {
        $this->createRequest['name'] = '';
        $this->executeCreate();
        $this->seeStatusCode(400);
    }
    public function test_create_emptyObjective_403()
    {
        $this->createRequest['objectives'] = [];
        $this->executeCreate();
        $this->seeStatusCode(403);
    }
    public function test_create_emptyObjectiveName_400()
    {
        $this->createRequest['objectives'][0]['name'] = '';
        $this->executeCreate();
        $this->seeStatusCode(400);
    }
    public function test_create_emptyObjectiveWeight_400()
    {
        $this->createRequest['objectives'][0]['weight'] = 0;
        $this->executeCreate();
        $this->seeStatusCode(400);
    }
    public function test_create_emptyKeyResult_403()
    {
        $this->createRequest['objectives'][0]['keyResults'] = [];
        $this->executeCreate();
        $this->seeStatusCode(403);
    }
    public function test_create_emptyKeyResultName_400()
    {
        $this->createRequest['objectives'][0]['keyResults'][0]['name'] = '';
        $this->executeCreate();
        $this->seeStatusCode(400);
    }
    public function test_create_emptyKeyResultTarget_400()
    {
        $this->createRequest['objectives'][0]['keyResults'][0]['target'] = 0;
        $this->executeCreate();
        $this->seeStatusCode(400);
    }
    public function test_create_emptyKeyResultWeight_400()
    {
        $this->createRequest['objectives'][0]['keyResults'][0]['weight'] = 0;
        $this->executeCreate();
        $this->seeStatusCode(400);
    }
    
    protected function executeUpdate()
    {
        $this->okrPeriodOne->insert($this->connection);
        $this->objective1_okrPeriod1_11->insert($this->connection);
        $this->objective2_okrPeriod1_12->insert($this->connection);
        $this->keyResult1_objective11_111->insert($this->connection);
        $this->keyResult2_objective11_112->insert($this->connection);
        $this->keyResult1_objective12_121->insert($this->connection);
        
        $uri = $this->programParticipationUri . "/{$this->participant->id}/okr-periods/{$this->okrPeriodOne->id}";
        $this->patch($uri, $this->updateRequest, $this->client->token);
    }
    public function test_update_200()
    {
        $this->executeUpdate();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->okrPeriodOne->id,
            'name' => $this->updateRequest['name'],
            'description' => $this->updateRequest['description'],
            'startDate' => $this->updateRequest['startDate'],
            'endDate' => $this->updateRequest['endDate'],
        ];
        $this->seeJsonContains($response);
        
        $okrPeriodEntry = [
            'id' => $this->okrPeriodOne->id,
            'name' => $this->updateRequest['name'],
            'description' => $this->updateRequest['description'],
            'startDate' => $this->updateRequest['startDate'],
            'endDate' => $this->updateRequest['endDate'],
        ];
        $this->seeInDatabase('OKRPeriod', $okrPeriodEntry);
    }
    public function test_update_updateObjective()
    {
        $this->executeUpdate();
        $this->seeStatusCode(200);
        
        $objectiveResponse = [
            'id' => $this->objective1_okrPeriod1_11->id,
            'name' => $this->updateRequest['objectives'][0]['name'],
            'description' => $this->updateRequest['objectives'][0]['description'],
            'weight' => $this->updateRequest['objectives'][0]['weight'],
        ];
        $this->seeJsonContains($objectiveResponse);
        
        $objectiveEntry = [
            'id' => $this->objective1_okrPeriod1_11->id,
            'name' => $this->updateRequest['objectives'][0]['name'],
            'description' => $this->updateRequest['objectives'][0]['description'],
            'weight' => $this->updateRequest['objectives'][0]['weight'],
        ];
        $this->seeInDatabase('Objective', $objectiveEntry);
    }
    public function test_update_updateObjectiveAlreadyDisable_enableThisObjective()
    {
        $this->objective1_okrPeriod1_11->disabled = true;
        $this->executeUpdate();
        $objectiveEntry = [
            'id' => $this->objective1_okrPeriod1_11->id,
            'disabled' => false,
        ];
        $this->seeInDatabase('Objective', $objectiveEntry);
    }
    public function test_update_requestHasNewObjective_aggregateNewObjective()
    {
        $this->updateRequest['objectives'][0]['id'] = null;
        $this->executeUpdate();
        $this->seeStatusCode(200);
        
        $objectiveEntry = [
            'OKRPeriod_id' => $this->okrPeriodOne->id,
            'name' => $this->updateRequest['objectives'][0]['name'],
            'description' => $this->updateRequest['objectives'][0]['description'],
            'weight' => $this->updateRequest['objectives'][0]['weight'],
        ];
        $this->seeInDatabase('Objective', $objectiveEntry);
    }
    public function test_update_disableExistingObjectiveNotExistInUpdateRequest()
    {
        $this->executeUpdate();
        $objectiveEntry = [
            'id' => $this->objective2_okrPeriod1_12->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('Objective', $objectiveEntry);
    }
    public function test_update_updateExistingKeyResult()
    {
        $this->executeUpdate();
        $keyResultEntry = [
            'id' => $this->keyResult1_objective11_111->id,
            'name' => $this->updateRequest['objectives'][0]['keyResults'][0]['name'],
            'description' => $this->updateRequest['objectives'][0]['keyResults'][0]['description'],
            'target' => $this->updateRequest['objectives'][0]['keyResults'][0]['target'],
            'weight' => $this->updateRequest['objectives'][0]['keyResults'][0]['weight'],
        ];
        $this->seeInDatabase('KeyResult', $keyResultEntry);
    }
    public function test_update_keyResultAlreadyDisabled_enableThisKeyResult()
    {
        $this->keyResult1_objective11_111->disabled = true;
        $this->executeUpdate();
        $keyResultEntry = [
            'id' => $this->keyResult1_objective11_111->id,
            'disabled' => false
        ];
        $this->seeInDatabase('KeyResult', $keyResultEntry);
    }
    public function test_update_requestContainNewKeyResult_aggregatenewKeyResult()
    {
        $this->updateRequest['objectives'][0]['keyResults'][0]['id'] = null;
        $this->executeUpdate();
        $keyResultEntry = [
            'Objective_id' => $this->objective1_okrPeriod1_11->id,
            'name' => $this->updateRequest['objectives'][0]['keyResults'][0]['name'],
            'description' => $this->updateRequest['objectives'][0]['keyResults'][0]['description'],
            'target' => $this->updateRequest['objectives'][0]['keyResults'][0]['target'],
            'weight' => $this->updateRequest['objectives'][0]['keyResults'][0]['weight'],
        ];
        $this->seeInDatabase('KeyResult', $keyResultEntry);
    }
    public function test_update_existingKeyResultNotExistInRequest_disable()
    {
        $this->executeUpdate();
        $keyResultEntry = [
            'id' => $this->keyResult2_objective11_112->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('KeyResult', $keyResultEntry);
    }
    public function test_update_disableExsitingObjectiveNotExistInRequest_disableAllKeyResultsOfThisObjective()
    {
        $this->executeUpdate();
        $keyResultEntry = [
            'id' => $this->keyResult2_objective11_112->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('KeyResult', $keyResultEntry);
    }
    public function test_update_okrPeriodAlreayCancelled_403()
    {
        $this->okrPeriodOne->cancelled = true;
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    public function test_update_okrPeriodAlreadyConcluded_403()
    {
        $this->okrPeriodOne->status = OKRPeriodApprovalStatus::APPROVED;
        $this->executeUpdate();
        $this->seeStatusCode(403);
    }
    public function test_update_conflictWithOtherOKRPeriod_409()
    {
        $this->okrPeriodTwo->insert($this->connection);
        $this->updateRequest['startDate'] = $this->okrPeriodTwo->startDate;
        $this->updateRequest['endDate'] = $this->okrPeriodTwo->endDate;
        $this->executeUpdate();
        $this->seeStatusCode(409);
    }
    public function test_update_conflictWithSelf_200()
    {
        $this->updateRequest['startDate'] = $this->okrPeriodOne->startDate;
        $this->updateRequest['endDate'] = $this->okrPeriodOne->endDate;
        $this->executeUpdate();
        $this->seeStatusCode(200);
    }
    public function test_update_conflictedOKRPeriodAlreadyCancelled_200()
    {
        $this->okrPeriodTwo->cancelled = true;
        $this->okrPeriodTwo->insert($this->connection);
        $this->updateRequest['startDate'] = $this->okrPeriodTwo->startDate;
        $this->updateRequest['endDate'] = $this->okrPeriodTwo->endDate;
        $this->executeUpdate();
        $this->seeStatusCode(200);
    }
    public function test_update_conflictedOKRPeriodAlreadyRejected_200()
    {
        $this->okrPeriodTwo->status = OKRPeriodApprovalStatus::REJECTED;
        $this->okrPeriodTwo->insert($this->connection);
        $this->updateRequest['startDate'] = $this->okrPeriodTwo->startDate;
        $this->updateRequest['endDate'] = $this->okrPeriodTwo->endDate;
        $this->executeUpdate();
        $this->seeStatusCode(200);
    }
    
    protected function executeCancel()
    {
        $this->okrPeriodOne->insert($this->connection);
        $this->objective1_okrPeriod1_11->insert($this->connection);
        $this->keyResult1_objective11_111->insert($this->connection);
        
        $uri = $this->programParticipationUri . "/{$this->participant->id}/okr-periods/{$this->okrPeriodOne->id}";
        $this->delete($uri, [], $this->client->token);
    }
    public function test_cancel_201()
    {
        $this->executeCancel();
        $okrPeriodEntry = [
            'id' => $this->okrPeriodOne->id,
            'cancelled' => true,
        ];
        $this->seeInDatabase('OKRPeriod', $okrPeriodEntry);
    }
    public function test_cancel_disableObjectives()
    {
        $this->executeCancel();
        $objectiveEntry = [
            'id' => $this->objective1_okrPeriod1_11->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('Objective', $objectiveEntry);
    }
    public function test_cancel_disableKeyResults()
    {
        $this->executeCancel();
        $keyResultEntry = [
            'id' => $this->keyResult1_objective11_111->id,
            'disabled' => true,
        ];
        $this->seeInDatabase('KeyResult', $keyResultEntry);
    }
    public function test_cancel_alreadyCancelled_403()
    {
        $this->okrPeriodOne->cancelled = true;
        $this->executeCancel();
        $this->seeStatusCode(403);
    }
    public function test_cancel_alreadyConcluded_403()
    {
        $this->okrPeriodOne->status = OKRPeriodApprovalStatus::APPROVED;
        $this->executeCancel();
        $this->seeStatusCode(403);
    }
    
    protected function executeShow()
    {
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
        
        $uri = $this->programParticipationUri . "/{$this->participant->id}/okr-periods/{$this->okrPeriodOne->id}";
        $this->get($uri, $this->client->token);
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
        $this->okrPeriodOne->insert($this->connection);
        $this->okrPeriodTwo->insert($this->connection);
        $uri = $this->programParticipationUri . "/{$this->participant->id}/okr-periods";
        $this->get($uri, $this->client->token);
    }
    public function test_showAll_200()
    {
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
