<?php

namespace Tests\Controllers\Personnel\AsProgramConsultant\Participant\Worksheet;

use Tests\Controllers\Personnel\AsProgramConsultant\Participant\WorksheetTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\Team\Member\RecordOfMemberComment;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;

class CommentControllerTest extends WorksheetTestCase
{
    protected $commentUri;
    protected $comment;
    protected $commentOne;
    protected $memberCommentOne;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->commentUri = $this->worksheetUri . "/{$this->worksheet->id}/comments";
        $this->connection->table('Comment')->truncate();
        $this->connection->table('MemberComment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('T_Member')->truncate();
        
        $firm = $this->consultant->program->firm;
        
        $this->comment = new RecordOfComment($this->worksheet, 0);
        $this->commentOne = new RecordOfComment($this->worksheet, 1);
        $this->commentOne->parent = $this->comment;
        $this->connection->table('Comment')->insert($this->comment->toArrayForDbEntry());
        $this->connection->table('Comment')->insert($this->commentOne->toArrayForDbEntry());
        
        $consultantComment = new RecordOfConsultantComment($this->consultant, $this->comment);
        $this->connection->table('ConsultantComment')->insert($consultantComment->toArrayForDbEntry());
        
        
        $client = new RecordOfClient($firm, 99);
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());
        
        $member = new RecordOfMember(null, $client, 99);
        $this->connection->table('T_Member')->insert($member->toArrayForDbEntry());
        
        $this->memberCommentOne = new RecordOfMemberComment($member, $this->commentOne);
        $this->connection->table('MemberComment')->insert($this->memberCommentOne->toArrayForDbEntry());
        
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('MemberComment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Client')->truncate();
        $this->connection->table('T_Member')->truncate();
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->commentOne->id,
            "message" => $this->commentOne->message,
            "submitTime" => $this->commentOne->submitTime,
            "removed" => $this->commentOne->removed,
            "consultant" => null,
            "member" => [
                'id' => $this->memberCommentOne->member->id,
                'client' => [
                    'id' => $this->memberCommentOne->member->client->id,
                    'name' => $this->memberCommentOne->member->client->getFullName(),
                ],
            ],
            "parent" => [
                "id" => $this->commentOne->parent->id,
                "message" => $this->commentOne->parent->message,
                "submitTime" => $this->commentOne->parent->submitTime,
                "removed" => $this->commentOne->parent->removed,
                "consultant" => [
                    "id" => $this->consultant->id,
                    "personnel" => [
                        "id" => $this->consultant->personnel->id,
                        "name" => $this->consultant->personnel->getFullName(),
                    ],
                ],
                'member' => null,
            ],
        ];
        $uri = $this->commentUri . "/{$this->commentOne->id}";
        $this->get($uri, $this->consultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
    }
    public function test_show_personnelNotProgramConsultant_error401()
    {
        $uri = $this->commentUri . "/{$this->commentOne->id}";
        $this->get($uri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
    }
    
    public function test_showAll()
    {
        $reponse = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->comment->id,
                    "message" => $this->comment->message,
                    "submitTime" => $this->comment->submitTime,
                    "removed" => $this->comment->removed,
                    "consultant" => [
                        "id" => $this->consultant->id,
                        "personnel" => [
                            "id" => $this->consultant->personnel->id,
                            "name" => $this->consultant->personnel->getFullName(),
                        ],
                    ],
                    "parent" => null,
                    "member" => null,
                ],
                [
                    "id" => $this->commentOne->id,
                    "message" => $this->commentOne->message,
                    "submitTime" => $this->commentOne->submitTime,
                    "removed" => $this->commentOne->removed,
                    "consultant" => null,
                    "member" => [
                        'id' => $this->memberCommentOne->member->id,
                        'client' => [
                            'id' => $this->memberCommentOne->member->client->id,
                            'name' => $this->memberCommentOne->member->client->getFullName(),
                        ],
                    ],
                    "parent" => [
                        "id" => $this->commentOne->parent->id,
                        "message" => $this->commentOne->parent->message,
                        "submitTime" => $this->commentOne->parent->submitTime,
                        "removed" => $this->commentOne->parent->removed,
                        "consultant" => [
                            "id" => $this->consultant->id,
                            "personnel" => [
                                "id" => $this->consultant->personnel->id,
                                "name" => $this->consultant->personnel->getFullName(),
                            ],
                        ],
                        'member' => null,
                    ],
                ],
            ],
        ];
        $this->get($this->commentUri, $this->consultant->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($reponse);
    }
    public function test_showAll_personnelNotProgramConsultant_error401()
    {
        $this->get($this->commentUri, $this->removedConsultant->personnel->token)
                ->seeStatusCode(401);
        
    }
}
