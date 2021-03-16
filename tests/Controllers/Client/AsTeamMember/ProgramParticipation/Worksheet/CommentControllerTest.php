<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation\Worksheet;

use DateTime;
use DateTimeImmutable;
use Tests\Controllers\Client\AsTeamMember\ProgramParticipation\WorksheetTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantComment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfComment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\Team\Member\RecordOfMemberComment;

class CommentControllerTest extends WorksheetTestCase
{
    protected $commentUri;
    protected $comment;
    protected $commentOne;
    protected $consultantComment;
    protected $memberComment;
    protected $commentInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commentUri = $this->worksheetUri . "/{$this->worksheet->id}/comments";
        
        $this->connection->table('Comment')->truncate();
        $this->connection->table('MemberComment')->truncate();
        $this->connection->table('ParticipantComment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('CommentActivityLog')->truncate();
        
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
        $this->connection->table('CommentMail')->truncate();
        
        $this->connection->table('Notification')->truncate();
        $this->connection->table('CommentNotification')->truncate();
        $this->connection->table('PersonnelNotificationRecipient')->truncate();
        $this->connection->table('ClientNotificationRecipient')->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $this->comment = new RecordOfComment($this->worksheet, 0);
        $this->commentOne = new RecordOfComment($this->worksheet, 1);
        $this->commentOne->parent = $this->comment;
        $this->connection->table('Comment')->insert($this->comment->toArrayForDbEntry());
        $this->connection->table('Comment')->insert($this->commentOne->toArrayForDbEntry());
        
        $this->memberComment = new RecordOfMemberComment($this->teamMemberTwo_notAdmin, $this->comment);
        $this->connection->table('MemberComment')->insert($this->memberComment->toArrayForDbEntry());

        $personnel = new RecordOfPersonnel($firm, 0);
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
        $this->connection->table('MemberComment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Personnel')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('CommentActivityLog')->truncate();
        
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
        $this->connection->table('CommentMail')->truncate();
        
        $this->connection->table('Notification')->truncate();
        $this->connection->table('CommentNotification')->truncate();
        $this->connection->table('PersonnelNotificationRecipient')->truncate();
        $this->connection->table('ClientNotificationRecipient')->truncate();
    }
    
    public function test_submitNew_201()
    {
        $this->connection->table('Comment')->truncate();
        $this->connection->table('MemberComment')->truncate();
        
        $response = [
            "message" => $this->commentInput['message'],
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "parent" => null,
            "member" => [
                'id' => $this->teamMember->id,
                'client' => [
                    'id' => $this->teamMember->client->id,
                    'name' => $this->teamMember->client->getFullName(),
                ],
            ],
            'consultantComment' => null,
        ];
        
        $this->post($this->commentUri, $this->commentInput, $this->teamMember->client->token)
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
        
        $memberCommentEntry = [
            'Member_id' => $this->teamMember->id,
        ];
        $this->seeInDatabase('MemberComment', $memberCommentEntry);
    }
    public function test_submitNew_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->post($this->commentUri, $this->commentInput, $this->teamMember->client->token)
                ->seeStatusCode(403);
    }
    public function test_submitNew_logActivity()
    {
        $this->post($this->commentUri, $this->commentInput, $this->teamMember->client->token)
                ->seeStatusCode(201);
        
        $activityLogEntry = [
            "message" => "team member submitted comment",
            "occuredTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $teamMemberActivityLogEntry = [
            "TeamMember_id" => $this->teamMember->id,
        ];
        $this->seeInDatabase("TeamMemberActivityLog", $teamMemberActivityLogEntry);
//see CommentActivityLog table to check log persisted
    }
    
    public function test_submitReply_201()
    {
        $response = [
            "message" => $this->commentInput['message'],
            "consultantComment" => null,
            'member' => [
                'id' => $this->teamMember->id,
                'client' => [
                    'id' => $this->teamMember->client->id,
                    'name' => $this->teamMember->client->getFullName(),
                ],
            ],
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
        $this->post($uri, $this->commentInput, $this->teamMember->client->token)
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
    public function test_submitReply_aggregateMailNotificaitonForConsultant()
    {
        $uri = $this->commentUri . "/{$this->consultantComment->comment->id}";
        $this->post($uri, $this->commentInput, $this->teamMember->client->token)
                ->seeStatusCode(201);
        
        $mailEntry = [
            "subject" => "Comment Replied",
            "SenderMailAddress" => $this->programParticipation->participant->program->firm->mailSenderAddress,
            "SenderName" => $this->programParticipation->participant->program->firm->mailSenderName,
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $consultantMailRecipientEntry = [
            "recipientMailAddress" => $this->consultantComment->consultant->personnel->email,
            "recipientName" => $this->consultantComment->consultant->personnel->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $consultantMailRecipientEntry);
    }
    public function test_submitReply_aggregateNotificationForOtherTeamMemberAndConsultant()
    {
        $uri = $this->commentUri . "/{$this->consultantComment->comment->id}";
        $this->post($uri, $this->commentInput, $this->teamMember->client->token)
                ->seeStatusCode(201);
        
        $personnelNotificationRecipientEntry = [
            "readStatus" => false,
            "Personnel_id" => $this->consultantComment->consultant->personnel->id,
        ];
        $this->seeInDatabase("PersonnelNotificationRecipient", $personnelNotificationRecipientEntry);
    }
    public function test_submitReply_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->commentUri . "/{$this->consultantComment->comment->id}";
        $this->post($uri, $this->commentInput, $this->teamMember->client->token)
                ->seeStatusCode(403);
    }
    public function test_submitReply_logActivity()
    {
        $uri = $this->commentUri . "/{$this->consultantComment->comment->id}";
        $this->post($uri, $this->commentInput, $this->teamMember->client->token)
                ->seeStatusCode(201);
        
        $activityLogEntry = [
            "message" => "team member submitted comment",
//            "occuredTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $teamMemberActivityLogEntry = [
            "TeamMember_id" => $this->teamMember->id,
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
            'member' => null,
        ];
        
        $uri = $this->commentUri . "/{$this->commentOne->id}";
        $this->get($uri, $this->teamMember->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_show_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $uri = $this->commentUri . "/{$this->commentOne->id}";
        $this->get($uri, $this->teamMember->client->token)
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
                    'member' => [
                        'id' => $this->teamMemberTwo_notAdmin->id,
                        'client' => [
                            'id' => $this->teamMemberTwo_notAdmin->client->id,
                            'name' => $this->teamMemberTwo_notAdmin->client->getFullName(),
                        ],
                    ],
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
                    'member' => null,
                ],
            ],
        ];
        $this->get($this->commentUri, $this->teamMember->client->token)
            ->seeStatusCode(200)
            ->seeJsonContains($response);
    }
    public function test_showAll_inactiveMember_403()
    {
        $this->setTeamMembershipInactive();
        $this->get($this->commentUri, $this->teamMember->client->token)
            ->seeStatusCode(403);
    }
}
