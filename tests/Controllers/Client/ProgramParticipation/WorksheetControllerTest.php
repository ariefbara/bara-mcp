<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\Participant\Worksheet\RecordOfCompletedMission,
    Firm\Program\RecordOfMission,
    Shared\RecordOfFormRecord
};

class WorksheetControllerTest extends WorksheetTestCase
{
    protected $worksheetOne;
    protected $branchMission;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table("CompletedMission")->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        
        $this->branchMission = new RecordOfMission($program, $this->worksheetForm, 1, $this->mission);
        $this->connection->table('Mission')->insert($this->branchMission->toArrayForDbEntry());
        
        $formRecord = new RecordOfFormRecord($this->form, 1);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());
        
        $this->worksheetOne = new RecordOfWorksheet($participant, $formRecord, $this->branchMission, 1);
        $this->worksheetOne->parent = $this->worksheet;
        $this->connection->table('Worksheet')->insert($this->worksheetOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("CompletedMission")->truncate();
    }
    
    public function test_submitRoot()
    {
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('FormRecord')->truncate();
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($this->worksheetResponse);
        
        $worksheetEntry = [
            "parent_id" => null,
            "name" => $this->worksheetInput['name'],
            "Participant_id" => $this->programParticipation->id,
            "removed" => false,
        ];
        $this->seeInDatabase('Worksheet', $worksheetEntry);
        
        $formRecordEntry = [
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
            "Form_id" => $this->form->id,
        ];
        $this->seeInDatabase('FormRecord', $formRecordEntry);
    }
    public function test_submitRoot_missionNotRootMission_403()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(403);
    }
    public function test_submitRoot_emptyName_error400()
    {
        $this->worksheetInput['name'] = "";
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_submitRoot_logActivity()
    {
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201);
        
        $activityLogEntry = [
            "message" => "participant submitted worksheet",
            "occuredTime" => (new DateTimeImmutable)->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
//see database manually to check WorksheetActivityLog recorded successfully
    }
    public function test_submitRoot_addMissionToCompletedMissionList()
    {
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201);
        
        $completedMissionEntry = [
            "Participant_id" => $this->programParticipation->participant->id,
            "Mission_id" => $this->mission->id,
            "completedTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("CompletedMission", $completedMissionEntry);
    }
    public function test_submitMission_missionAlreadyCompleted_preventAddNewRecord()
    {
        $completedMission = new RecordOfCompletedMission($this->programParticipation->participant, $this->mission, 0);
        $completedMission->completedTime = (new DateTimeImmutable("-1 days"))->format("Y-m-d H:i:s");
        $this->connection->table("CompletedMission")->insert($completedMission->toArrayForDbEntry());
        
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201);
        
        $completedMissionEntry = [
            "Mission_id" => $this->mission->id,
            "completedTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->notSeeInDatabase("CompletedMission", $completedMissionEntry);
    }
    public function test_submitRoot_inactiveParticipant_403()
    {
        $this->setParticipantInactive();
        $this->post($this->worksheetUri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_submitBranch()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        $this->worksheetResponse['parent'] = [
            "id" => $this->worksheet->id,
            "name" => $this->worksheet->name,
            "parent" => null,
        ];
        $this->worksheetResponse['mission'] = [
            "id" => $this->branchMission->id,
            "name" => $this->branchMission->name,
            "worksheetForm" => [
                "id" => $this->branchMission->worksheetForm->id,
                "name" => $this->branchMission->worksheetForm->form->name,
                "description" => $this->branchMission->worksheetForm->form->description,
            ],
        ];
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($this->worksheetResponse);
        
        $worksheetEntry = [
            "parent_id" => $this->worksheet->id,
            "name" => $this->worksheetInput['name'],
            "Participant_id" => $this->programParticipation->id,
            "removed" => false,
        ];
        $this->seeInDatabase('Worksheet', $worksheetEntry);
        
        $formRecordEntry = [
            "submitTime" => (new DateTime())->format("Y-m-d H:i:s"),
            "Form_id" => $this->form->id,
        ];
        $this->seeInDatabase('FormRecord', $formRecordEntry);
    }
    public function test_submitBranch_emptyName_error400()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        $this->worksheetInput['name'] = "";
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_submitBranch_missionNotBranchOfParentWorksheetMission_error403()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        $mission = new RecordOfMission($this->programParticipation->participant->program, $this->worksheetForm, 2, null);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());
        
        $this->worksheetInput["missionId"] = $mission->id;
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(403);
    }
    public function test_submitBranch_logActivity()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201);
        
        $activityLogEntry = [
            "message" => "participant submitted worksheet",
            "occuredTime" => (new DateTimeImmutable)->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
//see WorksheetActivityLog column manually to check record persisted
    }
    public function test_submitBranch_addCompletedMission()
    {
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201);
        
        $completedMissionEntry = [
            "Participant_id" => $this->programParticipation->participant->id,
            "Mission_id" => $this->branchMission->id,
            "completedTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("CompletedMission", $completedMissionEntry);
    }
    public function test_submitBranch_alreadyCompletedSameMission_dontAddNewCompletedMission()
    {
        $completedMission = new RecordOfCompletedMission($this->programParticipation->participant, $this->branchMission, 0);
        $completedMission->completedTime = (new DateTimeImmutable("-2 days"))->format("Y-m-d H:i:s");
        $this->connection->table("CompletedMission")->insert($completedMission->toArrayForDbEntry());
        
        $this->worksheetInput['missionId'] = $this->branchMission->id;
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(201);
        
        $completedMissionEntry = [
            "Mission_id" => $this->branchMission->id,
            "completedTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->notSeeInDatabase("CompletedMission", $completedMissionEntry);
        
    }
    public function test_submitBranch_inactiveParticipant_403()
    {
        $this->setParticipantInactive();
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->post($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_update()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->worksheetResponse);
        
        $worksheetEntry = [
            "id" => $this->worksheet->id,
            "parent_id" => null,
            "name" => $this->worksheetInput['name'],
            "Participant_id" => $this->programParticipation->id,
            "removed" => false,
        ];
        $this->seeInDatabase('Worksheet', $worksheetEntry);
    }
    public function test_update_emptyName_error400()
    {
        $this->worksheetInput['name'] = "";
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(400);
    }
    public function test_update_logActivity()
    {
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(200);
                
        $activityLogEntry = [
            "message" => "participant updated worksheet",
            "occuredTime" => (new DateTimeImmutable)->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $worksheetActivityLogEntry = [
            "Worksheet_id" => $this->worksheet->id,
        ];
        $this->seeInDatabase("WorksheetActivityLog", $worksheetActivityLogEntry);
    }
    public function test_update_inactiveParticipant_403()
    {
        $this->setParticipantInactive();
        $uri = $this->worksheetUri . "/{$this->worksheet->id}";
        $this->patch($uri, $this->worksheetInput, $this->client->token)
                ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $formRecord = new RecordOfFormRecord($this->worksheet->formRecord->form, 2);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());
        $worksheetTwo = new RecordOfWorksheet($this->programParticipation->participant, $formRecord, $this->mission, 2);
        $worksheetTwo->parent = $this->worksheetOne;
        $this->connection->table('Worksheet')->insert($worksheetTwo->toArrayForDbEntry());
        
        $this->worksheetResponse['id'] = $this->worksheet->id;
        $this->worksheetResponse['name'] = $this->worksheet->name;
        $this->worksheetResponse['parent'] = [
            'id' => $worksheetTwo->parent->id,
            'name' => $worksheetTwo->parent->name,
            'parent' => [
                'id' => $worksheetTwo->parent->parent->id,
                'name' => $worksheetTwo->parent->parent->name,
                'parent' => null,
            ],
        ];
        
        $uri = $this->worksheetUri . "/{$worksheetTwo->id}";
        $this->get($uri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($this->worksheetResponse);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->worksheet->id,
                    "name" => $this->worksheet->name,
                    "parent" => null,
                    "mission" => [
                        "id" => $this->worksheet->mission->id,
                        "name" => $this->worksheet->mission->name,
                    ],
                ],
                [
                    "id" => $this->worksheetOne->id,
                    "name" => $this->worksheetOne->name,
                    "parent" => [
                        "id" => $this->worksheetOne->parent->id,
                        "name" => $this->worksheetOne->parent->name,
                    ],
                    "mission" => [
                        "id" => $this->worksheetOne->mission->id,
                        "name" => $this->worksheetOne->mission->name,
                    ],
                ],
            ],
        ];
        $this->get($this->worksheetUri, $this->client->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_showAll_hasParentFilterSet()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->worksheet->id,
        ];
        $uri = $this->worksheetUri . "?hasParent=false";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_missionIdFilterSet()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->worksheetOne->id,
        ];
        $uri = $this->worksheetUri . "?missionId={$this->worksheetOne->mission->id}";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_parentIdFilterSet()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->worksheetOne->id,
        ];
        $uri = $this->worksheetUri . "?parentId={$this->worksheetOne->parent->id}";
        $this->get($uri, $this->client->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }
}
