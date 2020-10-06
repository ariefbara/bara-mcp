<?php

namespace Tests\Controllers\Client\TeamMembership\ProgramParticipation\Worksheet;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\ {
    Client\TeamMembership\ProgramParticipation\WorksheetTestCase,
    RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment,
    RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\RecordOfPersonnel
};

class CommentControllerTest extends WorksheetTestCase
{
    protected $commentUri;
    protected $comment, $commentOne, $consultantComment;
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
        $this->connection->table('CommentActivityLog')->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $this->comment = new RecordOfComment($this->worksheet, 0);
        $this->commentOne = new RecordOfComment($this->worksheet, 1);
        $this->commentOne->parent = $this->comment;
        $this->connection->table('Comment')->insert($this->comment->toArrayForDbEntry());
        $this->connection->table('Comment')->insert($this->commentOne->toArrayForDbEntry());

        $personnel = new RecordOfPersonnel($firm, 0, 'purnama.adi@gmail.com', 'password123');
        $this->connection->table('Personnel')->insert($personnel->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table('Consultant')->insert($consultant->toArrayForDbEntry());
        
        $this->consultantComment = new RecordOfConsultantComment($consultant, $this->commentOne);
        $this->connection->table('ConsultantComment')->insert($this->consultantComment->toArrayForDbEntry());
        
        $this->commentInput = [
            "message" => "new comment message",
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('CommentActivityLog')->truncate();
    }
    
    public function test_submitNew_201()
    {
        $this->connection->table('Comment')->truncate();
        
        $response = [
            "message" => $this->commentInput['message'],
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "parent" => null,
        ];
        
        $this->post($this->commentUri, $this->commentInput, $this->teamMembership->client->token)
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
    }
    public function test_submitNew_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->post($this->commentUri, $this->commentInput, $this->teamMembership->client->token)
                ->seeStatusCode(403);
    }
    public function test_submitNew_logActivity()
    {
        $this->post($this->commentUri, $this->commentInput, $this->teamMembership->client->token)
                ->seeStatusCode(201);
        
        $activityLogEntry = [
            "message" => "team member submitted comment",
            "occuredTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $teamMemberActivityLogEntry = [
            "TeamMember_id" => $this->teamMembership->id,
        ];
        $this->seeInDatabase("TeamMemberActivityLog", $teamMemberActivityLogEntry);
//see CommentActivityLog table to check log persisted
    }
    
    public function test_submitReply_201()
    {
        $response = [
            "message" => $this->commentInput['message'],
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "consultantComment" => null,
            "parent" => [
                "id" => $this->consultantComment->comment->id,
                "submitTime" => $this->consultantComment->comment->submitTime,
                "message" => $this->consultantComment->comment->message,
                "removed" => $this->consultantComment->comment->removed,
                "consultantComment" => [
                    'id' => $this->consultantComment->id,
                    'consultant' => [
                        'id' => $this->consultantComment->consultant->id,
                        'personnel' => [
                            'id' => $this->consultantComment->consultant->personnel->id,
                            'name' => $this->consultantComment->consultant->personnel->getFullName(),
                        ],

                    ],
                ],
            ],
        ];
        $uri = $this->commentUri . "/{$this->consultantComment->comment->id}";
        $this->post($uri, $this->commentInput, $this->teamMembership->client->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);
        
        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->commentInput['message'],
            "removed" => false,
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "parent_id" => $this->consultantComment->comment->id,
        ];
        $this->seeInDatabase("Comment", $commentEntry);
    }
    public function test_submitReply_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->commentUri . "/{$this->consultantComment->comment->id}";
        $this->post($uri, $this->commentInput, $this->teamMembership->client->token)
                ->seeStatusCode(403);
    }
    public function test_submitReply_logActivity()
    {
        $uri = $this->commentUri . "/{$this->consultantComment->comment->id}";
        $this->post($uri, $this->commentInput, $this->teamMembership->client->token)
                ->seeStatusCode(201);
        
        $activityLogEntry = [
            "message" => "team member submitted comment",
            "occuredTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $teamMemberActivityLogEntry = [
            "TeamMember_id" => $this->teamMembership->id,
        ];
        $this->seeInDatabase("TeamMemberActivityLog", $teamMemberActivityLogEntry);
//see CommentActivityLog table to check log persisted
    }
    
    public function test_show_200()
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
                'consultantComment' => null,
            ],
            "consultantComment" => [
                'id' => $this->consultantComment->id,
                'consultant' => [
                    'id' => $this->consultantComment->consultant->id,
                    'personnel' => [
                        'id' => $this->consultantComment->consultant->personnel->id,
                        'name' => $this->consultantComment->consultant->personnel->getFullName(),
                    ],
                    
                ],
            ],
        ];
        
        $uri = $this->commentUri . "/{$this->commentOne->id}";
        $this->get($uri, $this->teamMembership->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->commentUri . "/{$this->commentOne->id}";
        $this->get($uri, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
    
    public function test_showAll_200()
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
                    "consultantComment" => null,
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
                        'consultantComment' => null,
                    ],
                    "consultantComment" => [
                        'id' => $this->consultantComment->id,
                        'consultant' => [
                            'id' => $this->consultantComment->consultant->id,
                            'personnel' => [
                                'id' => $this->consultantComment->consultant->personnel->id,
                                'name' => $this->consultantComment->consultant->personnel->getFullName(),
                            ],

                        ],
                    ],
                ],
            ],
        ];
        $this->get($this->commentUri, $this->teamMembership->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->get($this->commentUri, $this->teamMembership->client->token)
            ->seeStatusCode(403);
    }
}
