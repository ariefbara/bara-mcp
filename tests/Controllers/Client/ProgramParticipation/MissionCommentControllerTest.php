<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfMissionComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;

class MissionCommentControllerTest extends ExtendedClientParticipantTestCase
{
    protected $showAllUri;
    
    protected $missionOne;
    protected $missionTwo;
    
    protected $missionCommentOne_m1;
    protected $missionCommentTwo_m2;
    protected $missionCommentThree_m1;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('MissionComment')->truncate();
        
        $this->showAllUri = $this->clientParticipantUri . "/mission-comments";
        
        $program = $this->clientParticipant->participant->program;
        
        $this->missionOne = new RecordOfMission($program, null, '1', null);
        $this->missionTwo = new RecordOfMission($program, null, '2', null);
        
        $this->missionCommentOne_m1 = new RecordOfMissionComment($this->missionOne, null, '1');
        $this->missionCommentTwo_m2 = new RecordOfMissionComment($this->missionTwo, null, '2');
        $this->missionCommentThree_m1 = new RecordOfMissionComment($this->missionOne, null, '3');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('MissionComment')->truncate();
    }
    
    protected function showAll()
    {
        $this->insertClientParticipantRecord();
        
        $this->missionOne->insert($this->connection);
        $this->missionTwo->insert($this->connection);
        
        $this->missionCommentOne_m1->insert($this->connection);
        $this->missionCommentTwo_m2->insert($this->connection);
        $this->missionCommentThree_m1->insert($this->connection);
        
        $this->get($this->showAllUri, $this->client->token);
    }
    public function test_showAll_200()
    {
        $this->showAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => 3,
            'list' => [
                [
                    'id' => $this->missionCommentOne_m1->id,
                    'message' => $this->missionCommentOne_m1->message,
                    'rolePaths' => $this->missionCommentOne_m1->rolePaths,
                    'userName' => $this->missionCommentOne_m1->userName,
                    'modifiedTime' => $this->missionCommentOne_m1->modifiedTime,
                    'repliedMessage' => null,
                    'mission' => [
                        'id' => $this->missionCommentOne_m1->mission->id,
                        'name' => $this->missionCommentOne_m1->mission->name,
                    ],
                ],
                [
                    'id' => $this->missionCommentTwo_m2->id,
                    'message' => $this->missionCommentTwo_m2->message,
                    'rolePaths' => $this->missionCommentTwo_m2->rolePaths,
                    'userName' => $this->missionCommentTwo_m2->userName,
                    'modifiedTime' => $this->missionCommentTwo_m2->modifiedTime,
                    'repliedMessage' => null,
                    'mission' => [
                        'id' => $this->missionCommentTwo_m2->mission->id,
                        'name' => $this->missionCommentTwo_m2->mission->name,
                    ],
                ],
                [
                    'id' => $this->missionCommentThree_m1->id,
                    'message' => $this->missionCommentThree_m1->message,
                    'rolePaths' => $this->missionCommentThree_m1->rolePaths,
                    'userName' => $this->missionCommentThree_m1->userName,
                    'modifiedTime' => $this->missionCommentThree_m1->modifiedTime,
                    'repliedMessage' => null,
                    'mission' => [
                        'id' => $this->missionCommentThree_m1->mission->id,
                        'name' => $this->missionCommentThree_m1->mission->name,
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_showAll_containReply_includeParentCommentInResponse()
    {
        $this->missionCommentThree_m1->repliedComment = $this->missionCommentOne_m1;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $missionCommentOneResponse = ['id' => $this->missionCommentOne_m1->id];
        $this->seeJsonContains($missionCommentOneResponse);
        
        $missionCommentTwoResponse = ['id' => $this->missionCommentTwo_m2->id];
        $this->seeJsonContains($missionCommentTwoResponse);
        
        $missionCommentThreeResponse = [
            'id' => $this->missionCommentThree_m1->id,
            'repliedMessage' => [
                'id' => $this->missionCommentThree_m1->repliedComment->id,
                'message' => $this->missionCommentThree_m1->repliedComment->message,
                'rolePaths' => $this->missionCommentThree_m1->repliedComment->rolePaths,
                'userName' => $this->missionCommentThree_m1->repliedComment->userName,
                'modifiedTime' => $this->missionCommentThree_m1->repliedComment->modifiedTime,
            ],
        ];
        $this->seeJsonContains($missionCommentThreeResponse);
    }
    public function test_showAll_containMissionCommentFromOtherProgam_exclude()
    {
        $firm = $this->clientParticipant->participant->program->firm;
        $otherProgram = new RecordOfProgram($firm, 'other');
        $otherProgram->insert($this->connection);
        
        $this->missionCommentTwo_m2->mission->program = $otherProgram;
        
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $missionCommentOneResponse = ['id' => $this->missionCommentOne_m1->id];
        $this->seeJsonContains($missionCommentOneResponse);
        
        $missionCommentTwoResponse = ['id' => $this->missionCommentTwo_m2->id];
        $this->seeJsonDoesntContains($missionCommentTwoResponse);
        
        $missionCommentThreeResponse = ['id' => $this->missionCommentThree_m1->id];
        $this->seeJsonContains($missionCommentThreeResponse);
    }
    public function test_showAll_paginationApplied()
    {
        $this->missionCommentOne_m1->modifiedTime = (new \DateTimeImmutable('-48 hours'));
        $this->missionCommentTwo_m2->modifiedTime = (new \DateTimeImmutable('-24 hours'));
        $this->missionCommentThree_m1->modifiedTime = (new \DateTimeImmutable('-72 hours'));
        
        $this->showAllUri .= "?page=1&pageSize=1";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $missionCommentOneResponse = ['id' => $this->missionCommentOne_m1->id];
        $this->seeJsonDoesntContains($missionCommentOneResponse);
        
        $missionCommentTwoResponse = ['id' => $this->missionCommentTwo_m2->id];
        $this->seeJsonContains($missionCommentTwoResponse);
        
        $missionCommentThreeResponse = ['id' => $this->missionCommentThree_m1->id];
        $this->seeJsonDoesntContains($missionCommentThreeResponse);
    }
    public function test_showAll_orderApplied()
    {
        $this->missionCommentOne_m1->modifiedTime = (new \DateTimeImmutable('-48 hours'));
        $this->missionCommentTwo_m2->modifiedTime = (new \DateTimeImmutable('-24 hours'));
        $this->missionCommentThree_m1->modifiedTime = (new \DateTimeImmutable('-72 hours'));
        
        $this->showAllUri .= "?page=1&pageSize=1&order=ASC";
        $this->showAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 3];
        $this->seeJsonContains($totalResponse);
        
        $missionCommentOneResponse = ['id' => $this->missionCommentOne_m1->id];
        $this->seeJsonDoesntContains($missionCommentOneResponse);
        
        $missionCommentTwoResponse = ['id' => $this->missionCommentTwo_m2->id];
        $this->seeJsonDoesntContains($missionCommentTwoResponse);
        
        $missionCommentThreeResponse = ['id' => $this->missionCommentThree_m1->id];
        $this->seeJsonContains($missionCommentThreeResponse);
    }
    public function test_showAll_inactiveParticipant_forbidden()
    {
        $this->clientParticipant->participant->active = false;
        $this->showAll();
        $this->seeStatusCode(403);
    }
    
    protected function show()
    {
        $this->insertClientParticipantRecord();
        $this->missionCommentOne_m1->mission->insert($this->connection);
        $this->missionCommentOne_m1->insert($this->connection);
        
        $uri = $this->showAllUri . "/{$this->missionCommentOne_m1->id}";
        $this->get($uri, $this->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $response = [
            'id' => $this->missionCommentOne_m1->id,
            'message' => $this->missionCommentOne_m1->message,
            'rolePaths' => $this->missionCommentOne_m1->rolePaths,
            'userName' => $this->missionCommentOne_m1->userName,
            'modifiedTime' => $this->missionCommentOne_m1->modifiedTime,
            'repliedMessage' => null,
            'missions' => [
                'id' => $this->missionCommentOne_m1->mission->id,
                'name' => $this->missionCommentOne_m1->mission->name,
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_show_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->show();
        $this->seeStatusCode(403);
    }
}
