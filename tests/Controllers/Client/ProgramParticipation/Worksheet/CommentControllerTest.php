<?php

namespace Tests\Controllers\Client\ProgramParticipation\Worksheet;

use DateTime;
use Tests\Controllers\ {
    Client\ProgramParticipation\WorksheetTestCase,
    RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment,
    RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfConsultantComment,
    RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfParticipantComment,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\RecordOfPersonnel
};

class CommentControllerTest extends WorksheetTestCase
{
    protected $commentUri;
    protected $participantComment, $consultantComment;
    protected $commentInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commentUri = $this->worksheetUri . "/{$this->worksheet->id}/comments";
        
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ParticipantComment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        
        $comment = new RecordOfComment(0);
        $commentOne = new RecordOfComment(1);
        $this->connection->table('Comment')->insert($comment->toArrayForDbEntry());
        $this->connection->table('Comment')->insert($commentOne->toArrayForDbEntry());

        $personnel = new RecordOfPersonnel($this->programParticipation->program->firm, 0, 'purnama.adi@gmail.com', 'password123');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($this->programParticipation->program, $personnel, 0);
        $this->connection->table('Consultant')->insert($consultant->toArrayForDbEntry());
        
        $this->participantComment = new RecordOfParticipantComment($this->worksheet, $comment);
        $this->connection->table('ParticipantComment')->insert($this->participantComment->toArrayForDbEntry());
        
        $this->consultantComment = new RecordOfConsultantComment($this->worksheet, $consultant, $commentOne);
        $this->connection->table('ConsultantComment')->insert($this->consultantComment->toArrayForDbEntry());
        
        $this->commentInput = [
            "message" => "new comment message",
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ParticipantComment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
    }
    
    public function test_submitNew()
    {
        
$this->disableExceptionHandling();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ParticipantComment')->truncate();
        
        $response = [
            "message" => $this->commentInput['message'],
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "parent" => null,
        ];
        
        $this->post($this->commentUri, $this->commentInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);
        
        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->commentInput['message'],
            "removed" => false,
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "parent_id" => null,
        ];
        $this->seeInDatabase("Comment", $commentEntry);
        
        $participantCommentEntry = [
            "Participant_id" => $this->programParticipation->id,
        ];
        $this->seeInDatabase("ParticipantComment", $participantCommentEntry);
    }
/*
    public function test_submitReply()
    {
        $response = [
            "message" => $this->commentInput['message'],
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "parent" => [
                "id" => $this->comment->id,
                "submitTime" => $this->comment->submitTime,
                "message" => $this->comment->message,
                "removed" => $this->comment->removed,
            ],
        ];
        $uri = $this->commentUri . "/{$this->comment->id}";
        $this->post($uri, $this->commentInput, $this->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);
        
        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->commentInput['message'],
            "removed" => false,
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "parent_id" => $this->comment->id,
        ];
        $this->seeInDatabase("Comment", $commentEntry);
    }
    
    
    public function test_show()
    {
        $response = [
            "id" => $this->commentOne->id,
            "message" => $this->commentOne->message,
            "submitTime" => $this->commentOne->submitTime,
            "removed" => $this->commentOne->removed,
            "parent" => [
                "id" => $this->comment->id,
                "submitTime" => $this->comment->submitTime,
                "message" => $this->comment->message,
                "removed" => $this->comment->removed,
            ],
            "participant" => [
                "id" => $this->programParticipation->id,
                "client" => [
                    "id" => $this->programParticipation->client->id,
                    "name" => $this->programParticipation->client->name,
                ],
            ],
            "consultant" => null,
        ];
        
        $uri = $this->commentUri . "/{$this->commentOne->id}";
        $this->get($uri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->comment->id,
                    "message" => $this->comment->message,
                    "submitTime" => $this->comment->submitTime,
                    "removed" => $this->comment->removed,
                    "parent" => null,
                    "participant" => [
                        "id" => $this->programParticipation->id,
                        "client" => [
                            "id" => $this->programParticipation->client->id,
                            "name" => $this->programParticipation->client->name,
                        ],
                    ],
                    "consultant" => null,
                ],
                [
                    "id" => $this->commentOne->id,
                    "message" => $this->commentOne->message,
                    "submitTime" => $this->commentOne->submitTime,
                    "removed" => $this->commentOne->removed,
                    "parent" => [
                        "id" => $this->comment->id,
                        "submitTime" => $this->comment->submitTime,
                        "message" => $this->comment->message,
                        "removed" => $this->comment->removed,
                    ],
                    "participant" => [
                        "id" => $this->programParticipation->id,
                        "client" => [
                            "id" => $this->programParticipation->client->id,
                            "name" => $this->programParticipation->client->name,
                        ],
                    ],
                    "consultant" => null,
                ],
            ],
        ];
        $this->get($this->commentUri, $this->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
 * 
 */
}
