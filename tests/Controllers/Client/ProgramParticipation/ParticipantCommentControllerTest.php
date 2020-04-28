<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ {
    RecordOfParticipantComment,
    Worksheet\RecordOfComment
};

class ParticipantCommentControllerTest extends WorksheetTestCase
{
    protected $participantCommentUri;
    protected $participantComment;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantCommentUri = $this->programParticipationUri . "/{$this->programParticipation->id}/participant-comments";
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ParticipantComment')->truncate();
        
        $comment = new RecordOfComment($this->worksheet, 0);
        $this->connection->table('Comment')->insert($comment->toArrayForDbEntry());
        
        $this->participantComment = new RecordOfParticipantComment($this->programParticipation, $comment);
        $this->connection->table('ParticipantComment')->insert($this->participantComment->toArrayForDbEntry());
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ParticipantComment')->truncate();
    }
    
    public function test_remove()
    {
$this->disableExceptionHandling();
        $uri = $this->participantCommentUri . "/{$this->participantComment->id}";
        $this->delete($uri, [], $this->client->token)
                ->seeStatusCode(200);
        
        $commentEntry = [
            "id" => $this->participantComment->comment->id,
            "removed" => true,
        ];
        $this->seeInDatabase("Comment", $commentEntry);
        
    }
}
