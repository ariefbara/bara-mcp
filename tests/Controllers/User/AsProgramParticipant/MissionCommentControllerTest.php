<?php

namespace Tests\Controllers\User\AsProgramParticipant;

use Tests\Controllers\RecordPreparation\Firm\Program\Mission\RecordOfMissionComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;

class MissionCommentControllerTest extends AsProgramParticipantTestCase
{
    protected $missionOne;
    protected $missionCommentOne;
    protected $missionCommentTwo;
    protected $submitRequest = [
        'message' => 'new mission comment message',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('MissionComment')->truncate();
        
        $program = $this->programParticipation->participant->program;
        $this->missionOne = new RecordOfMission($program, null, '1', null);
        $this->missionCommentOne = new RecordOfMissionComment($this->missionOne, null, '1');
        $this->missionCommentTwo = new RecordOfMissionComment($this->missionOne, $this->missionCommentOne, '2');
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('MissionComment')->truncate();
    }
    
    protected function executeSubmit()
    {
        $this->missionOne->insert($this->connection);
        $uri = $this->asProgramParticipantUri . "/missions/{$this->missionOne->id}/mission-comments";
        $this->post($uri, $this->submitRequest, $this->programParticipation->user->token);
    }
    public function test_submit_201()
    {
        $modifiedTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->executeSubmit();
        $this->seeStatusCode(201);
        
        $response = [
            'message' => $this->submitRequest['message'],
            'rolePaths' => json_encode([
                'participant' => $this->programParticipation->participant->id
            ]),
            'userName' => $this->programParticipation->user->getFullName(),
            'modifiedTime' => $modifiedTime,
            'repliedMessage' => null,
        ];
        $this->seeJsonContains($response);
        
        $missionCommentEntry = [
            'Mission_id' => $this->missionOne->id,
            'message' => $this->submitRequest['message'],
            'userName' => $this->programParticipation->user->getFullName(),
            'userId' => $this->programParticipation->user->id,
            'modifiedTime' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'MissionComment_idToReply' => null,
        ];
        $this->seeInDatabase('MissionComment', $missionCommentEntry);
    }
    
    protected function executeReply()
    {
        $this->missionOne->insert($this->connection);
        $this->missionCommentOne->insert($this->connection);
        $uri = $this->asProgramParticipantUri . "/mission-comments/{$this->missionCommentOne->id}";
        $this->post($uri, $this->submitRequest, $this->programParticipation->user->token);
    }
    public function test_reply_201()
    {
        $modifiedTime = (new \DateTimeImmutable())->format('Y-m-d H:i:s');
        $this->executeReply();
        $this->seeStatusCode(201);
        
        $response = [
            'message' => $this->submitRequest['message'],
            'rolePaths' => json_encode([
                'participant' => $this->programParticipation->participant->id
            ]),
            'userName' => $this->programParticipation->user->getFullName(),
            'modifiedTime' => $modifiedTime,
            'repliedMessage' => [
                'id' => $this->missionCommentOne->id,
                'message' => $this->missionCommentOne->message,
                'rolePaths' => $this->missionCommentOne->rolePaths,
                'userName' => $this->missionCommentOne->userName,
                'modifiedTime' => $this->missionCommentOne->modifiedTime,
            ],
        ];
        $this->seeJsonContains($response);
        
        $missionCommentEntry = [
            'Mission_id' => $this->missionOne->id,
            'message' => $this->submitRequest['message'],
            'userName' => $this->programParticipation->user->getFullName(),
            'userId' => $this->programParticipation->user->id,
            'modifiedTime' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
            'MissionComment_idToReply' => $this->missionCommentOne->id,
        ];
        $this->seeInDatabase('MissionComment', $missionCommentEntry);
    }
    
    protected function executeShow()
    {
        $this->missionOne->insert($this->connection);
        $this->missionCommentOne->insert($this->connection);
        $uri = $this->asProgramParticipantUri . "/mission-comments/{$this->missionCommentOne->id}";
        $this->get($uri, $this->programParticipation->user->token);
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
        $response = [
            'id' => $this->missionCommentOne->id,
            'message' => $this->missionCommentOne->message,
            'rolePaths' => $this->missionCommentOne->rolePaths,
            'userName' => $this->missionCommentOne->userName,
            'modifiedTime' => $this->missionCommentOne->modifiedTime,
            'repliedMessage' => null,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeShowAll()
    {
        $this->missionOne->insert($this->connection);
        $this->missionCommentOne->insert($this->connection);
        $this->missionCommentTwo->insert($this->connection);
        $uri = $this->asProgramParticipantUri . "/missions/{$this->missionOne->id}/mission-comments";
        $this->get($uri, $this->programParticipation->user->token);
    }
    public function test_showAll_200()
    {
        $this->executeShowAll();
        $this->seeStatusCode(200);
        $totalResponse = ['total' => 2];
        $this->seeJsonContains($totalResponse);
        
        $commentOneResponse = [
            'id' => $this->missionCommentOne->id,
            'message' => $this->missionCommentOne->message,
            'rolePaths' => $this->missionCommentOne->rolePaths,
            'userName' => $this->missionCommentOne->userName,
            'modifiedTime' => $this->missionCommentOne->modifiedTime,
            'repliedMessage' => null,
        ];
        $this->seeJsonContains($commentOneResponse);
        
        $commentTwoResponse = [
            'id' => $this->missionCommentTwo->id,
            'message' => $this->missionCommentTwo->message,
            'rolePaths' => $this->missionCommentTwo->rolePaths,
            'userName' => $this->missionCommentTwo->userName,
            'modifiedTime' => $this->missionCommentTwo->modifiedTime,
            'repliedMessage' => [
                'id' => $this->missionCommentOne->id,
                'message' => $this->missionCommentOne->message,
                'rolePaths' => $this->missionCommentOne->rolePaths,
                'userName' => $this->missionCommentOne->userName,
                'modifiedTime' => $this->missionCommentOne->modifiedTime,
            ],
        ];
        $this->seeJsonContains($commentTwoResponse);
    }
}
