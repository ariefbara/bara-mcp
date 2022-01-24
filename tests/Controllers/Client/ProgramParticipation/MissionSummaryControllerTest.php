<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\Client\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;

class MissionSummaryControllerTest extends ProgramParticipationTestCase
{
    protected $missionOne;
    protected $missionTwo;
    protected $missionThree;
    
    protected $completedMissionOne_m1;
    protected $completedMissionTwo_m2;
    protected $completedMissionThree_m3_otherParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $otherParticipant = new RecordOfParticipant($program, 'other');
        
        $this->missionOne = new RecordOfMission($program, null, '1', null);
        $this->missionOne->published = true;
        $this->missionOne->position = 1;
        $this->missionTwo = new RecordOfMission($program, null, '2', null);
        $this->missionTwo->published = true;
        $this->missionTwo->position = 2;
        $this->missionThree = new RecordOfMission($program, null, '3', null);
        $this->missionThree->published = true;
        $this->missionThree->position = 3;
        
        $this->completedMissionOne_m1 = new RecordOfCompletedMission($participant, $this->missionOne, '1');
        $this->completedMissionOne_m1->completedTime = (new \DateTimeImmutable('-48 hours'))->format('Y-m-d H:i:s');
        $this->completedMissionTwo_m2 = new RecordOfCompletedMission($participant, $this->missionTwo, '2');
        $this->completedMissionTwo_m2->completedTime = (new \DateTimeImmutable('-24 hours'))->format('Y-m-d H:i:s');
        $this->completedMissionThree_m3_otherParticipant = new RecordOfCompletedMission($otherParticipant, $this->missionThree, '3');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('CompletedMission')->truncate();
    }
    
    protected function show()
    {
        $this->missionOne->insert($this->connection);
        $this->missionTwo->insert($this->connection);
        $this->missionThree->insert($this->connection);
        
        $this->completedMissionOne_m1->insert($this->connection);
        $this->completedMissionTwo_m2->insert($this->connection);
        
        $uri = $this->programParticipationUri . "/{$this->programParticipation->participant->id}/mission-summary";
echo $uri;
        $this->get($uri, $this->programParticipation->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'totalCompletedMission' => '2',
            'totalMission' => '3',
            'lastCompletedTime' => $this->completedMissionTwo_m2->completedTime,
            'lastCompletedMissionId' => $this->completedMissionTwo_m2->mission->id,
            'lastCompletedMissionName' => $this->completedMissionTwo_m2->mission->name,
            'nextMissionId' => $this->missionThree->id,
            'nextMissionName' => $this->missionThree->name,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_containUnpublishedMission_excludeFromResult()
    {
        $this->missionTwo->published = false;
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'totalCompletedMission' => '1',
            'totalMission' => '2',
            'lastCompletedTime' => $this->completedMissionOne_m1->completedTime,
            'lastCompletedMissionId' => $this->completedMissionOne_m1->mission->id,
            'lastCompletedMissionName' => $this->completedMissionOne_m1->mission->name,
            'nextMissionId' => $this->missionThree->id,
            'nextMissionName' => $this->missionThree->name,
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_hasCompletedMissionFromOtherParticipant_ignore()
    {
        $this->completedMissionThree_m3_otherParticipant->participant->insert($this->connection);
        $this->completedMissionThree_m3_otherParticipant->insert($this->connection);
        
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'totalCompletedMission' => '3',
            'totalMission' => '3',
            'lastCompletedTime' => $this->completedMissionTwo_m2->completedTime,
            'lastCompletedMissionId' => $this->completedMissionTwo_m2->mission->id,
            'lastCompletedMissionName' => $this->completedMissionTwo_m2->mission->name,
            'nextMissionId' => $this->missionThree->id,
            'nextMissionName' => $this->missionThree->name,
        ];
        $this->seeJsonContains($response);
    }
}
