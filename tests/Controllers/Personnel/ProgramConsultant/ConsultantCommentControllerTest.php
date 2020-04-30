<?php

namespace Tests\Controllers\Personnel\ProgramConsultant;

use Tests\Controllers\RecordPreparation\{
    Firm\Program\Consultant\RecordOfConsultantComment,
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfWorksheetForm,
    RecordOfClient,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord
};

class ConsultantCommentControllerTest extends ProgramConsultantTestCase
{

    protected $consultantCommentUri;
    protected $client;
    protected $worksheet;
    protected $consultantComment;
    protected $commentOne;
    protected $submitNewRequest;
    protected $submitReplyRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantCommentUri = $this->programConsultantUri . "/consultant-comments";

        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();

        $this->connection->table('Notification')->truncate();
        $this->connection->table('ClientNotification')->truncate();
        $this->connection->table('CommentNotification')->truncate();

        $this->client = new RecordOfClient(0, 'client@email.org', 'password123');
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());

        $participant = new RecordOfParticipant($this->programConsultant->program, $this->client, 0);
        $this->connection->table('Participant')->insert($participant->toArrayForDbEntry());

        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($form, 0);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());

        $worksheetForm = new RecordOfWorksheetForm($this->programConsultant->program->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());

        $mission = new RecordOfMission($this->programConsultant->program, $worksheetForm, 0, null);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());

        $this->worksheet = new RecordOfWorksheet($participant, $formRecord, $mission);
        $this->connection->table('Worksheet')->insert($this->worksheet->toArrayForDbEntry());

        $comment = new RecordOfComment($this->worksheet, 0);
        $this->commentOne = new RecordOfComment($this->worksheet, 1);
        $this->connection->table('Comment')->insert($comment->toArrayForDbEntry());
        $this->connection->table('Comment')->insert($this->commentOne->toArrayForDbEntry());

        $this->consultantComment = new RecordOfConsultantComment($this->programConsultant, $comment);
        $this->connection->table('ConsultantComment')->insert($this->consultantComment->toArrayForDbEntry());

        $this->submitNewRequest = [
            "participantId" => $participant->id,
            "worksheetId" => $this->worksheet->id,
            "message" => 'new message',
        ];
        $this->submitReplyRequest = [
            "commentId" => $this->commentOne->id,
            "participantId" => $participant->id,
            "worksheetId" => $this->worksheet->id,
            "message" => 'new message',
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();

        $this->connection->table('Notification')->truncate();
        $this->connection->table('ClientNotification')->truncate();
        $this->connection->table('CommentNotification')->truncate();
    }

    public function test_submitNew()
    {
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Comment')->truncate();

        $response = [
            "submitTime" => (new \DateTime())->format('Y-m-d H:i:s'),
            "message" => $this->submitNewRequest['message'],
            "parent" => null,
        ];

        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultant->personnel->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->submitNewRequest['message'],
            "submitTime" => (new \DateTime())->format("Y-m-d H:i:s"),
            "parentComment_id" => null,
            "removed" => false,
        ];
        $this->seeInDatabase("Comment", $commentEntry);

        $consultantCommentEntry = [
            "Consultant_id" => $this->programConsultant->id,
        ];
        $this->seeInDatabase("ConsultantComment", $consultantCommentEntry);
    }

    public function test_submitNew_notifyClient()
    {
        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultant->personnel->token)
                ->seeStatusCode(201);

        $notificationEntry = [
            "message" => "consultant {$this->programConsultant->personnel->name} has commented on your worksheet",
            "notifiedTime" => (new \DateTime())->format('Y-m-d H:i:s'),
            "isRead" => false,
        ];
        $this->seeInDatabase("Notification", $notificationEntry);

        $clientNotificationEntry = [
            "Client_id" => $this->client->id,
        ];
        $this->seeInDatabase('ClientNotification', $clientNotificationEntry);
    }
    
    public function test_submitReply()
    {
        $response = [
            "submitTime" => (new \DateTime())->format('Y-m-d H:i:s'),
            "message" => $this->submitReplyRequest['message'],
            "parent" => [
                "id" => $this->commentOne->id,
                "message" => $this->commentOne->message,
            ],
        ];

        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultant->personnel->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->submitReplyRequest['message'],
            "submitTime" => (new \DateTime())->format("Y-m-d H:i:s"),
            "parentComment_id" => $this->commentOne->id,
            "removed" => false,
        ];
        $this->seeInDatabase("Comment", $commentEntry);

        $consultantCommentEntry = [
            "Consultant_id" => $this->programConsultant->id,
        ];
        $this->seeInDatabase("ConsultantComment", $consultantCommentEntry);
    }
    
    public function test_submitReply_notifyClient()
    {
        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultant->personnel->token)
                ->seeStatusCode(201);
        
        $notificationEntry = [
            "message" => "consultant {$this->programConsultant->personnel->name} has commented on your worksheet",
            "notifiedTime" => (new \DateTime())->format('Y-m-d H:i:s'),
            "isRead" => false,
        ];
        $this->seeInDatabase("Notification", $notificationEntry);

        $clientNotificationEntry = [
            "Client_id" => $this->client->id,
        ];
        $this->seeInDatabase('ClientNotification', $clientNotificationEntry);
    }
    
    public function test_remove()
    {
        $uri = $this->consultantCommentUri . "/{$this->consultantComment->id}";
        $this->delete($uri, [], $this->programConsultant->personnel->token)
                ->seeStatusCode(200);
        
        $commentEntry = [
            "id" => $this->consultantComment->comment->id,
            "removed" => true,
        ];
        $this->seeInDatabase("Comment", $commentEntry);
    }

}
