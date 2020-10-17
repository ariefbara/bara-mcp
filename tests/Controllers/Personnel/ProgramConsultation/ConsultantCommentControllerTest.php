<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\Consultant\RecordOfConsultantComment,
    Firm\Program\Participant\RecordOfWorksheet,
    Firm\Program\Participant\Worksheet\RecordOfComment,
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfTeam,
    Firm\RecordOfWorksheetForm,
    Firm\Team\RecordOfMember,
    Firm\Team\RecordOfTeamProgramParticipation,
    RecordOfUser,
    Shared\RecordOfForm,
    Shared\RecordOfFormRecord,
    User\RecordOfUserParticipant
};

class ConsultantCommentControllerTest extends ProgramConsultationTestCase
{

    protected $consultantCommentUri;
    protected $client;
    protected $clientOne;
    protected $participant;
    protected $clientParticipant;
    protected $userParticipant;
    protected $teamParticipant;
    protected $worksheet;
    protected $consultantComment;
    protected $commentOne;
    protected $submitNewRequest;
    protected $submitReplyRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultantCommentUri = $this->programConsultationUri . "/consultant-comments";

        $this->connection->table('Client')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('CommentActivityLog')->truncate();
        $this->connection->table('ConsultantActivityLog')->truncate();
        
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
        $this->connection->table('CommentMail')->truncate();
        
        $this->connection->table('Notification')->truncate();
        $this->connection->table('CommentNotification')->truncate();
        $this->connection->table('ClientNotificationRecipient')->truncate();
        
        $program = $this->programConsultation->program;
        $firm = $program->firm;
        
        $this->participant = new RecordOfParticipant($program, 0);
        $this->connection->table('Participant')->insert($this->participant->toArrayForDbEntry());
        
        $this->client = new RecordOfClient($firm, 0);
        $this->client->email = "purnama.adi@gmail.com";
        $this->clientOne = new RecordOfClient($firm, 1);
        $this->clientOne->email = "go.on.apur@gmail.com";
        $this->connection->table('Client')->insert($this->client->toArrayForDbEntry());
        $this->connection->table('Client')->insert($this->clientOne->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($this->client, $this->participant);
        $this->clientParticipant = new RecordOfClientParticipant($this->clientOne, $this->participant);
        $this->connection->table('ClientParticipant')->insert($this->clientParticipant->toArrayForDbEntry());

        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());

        $formRecord = new RecordOfFormRecord($form, 0);
        $this->connection->table('FormRecord')->insert($formRecord->toArrayForDbEntry());

        $worksheetForm = new RecordOfWorksheetForm($this->programConsultation->program->firm, $form);
        $this->connection->table('WorksheetForm')->insert($worksheetForm->toArrayForDbEntry());

        $mission = new RecordOfMission($this->programConsultation->program, $worksheetForm, 0, null);
        $this->connection->table('Mission')->insert($mission->toArrayForDbEntry());

        $this->worksheet = new RecordOfWorksheet($this->participant, $formRecord, $mission, 0);
        $this->connection->table('Worksheet')->insert($this->worksheet->toArrayForDbEntry());

        $comment = new RecordOfComment($this->worksheet, 0);
        $this->commentOne = new RecordOfComment($this->worksheet, 1);
        $this->connection->table('Comment')->insert($comment->toArrayForDbEntry());
        $this->connection->table('Comment')->insert($this->commentOne->toArrayForDbEntry());

        $this->consultantComment = new RecordOfConsultantComment($this->programConsultation, $comment);
        $this->connection->table('ConsultantComment')->insert($this->consultantComment->toArrayForDbEntry());

        $this->submitNewRequest = [
            "participantId" => $this->participant->id,
            "worksheetId" => $this->worksheet->id,
            "message" => 'new message',
        ];
        $this->submitReplyRequest = [
            "commentId" => $this->commentOne->id,
            "participantId" => $this->participant->id,
            "worksheetId" => $this->worksheet->id,
            "message" => 'new message',
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        
        $this->connection->table('Form')->truncate();
        $this->connection->table('FormRecord')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Worksheet')->truncate();
        $this->connection->table('Comment')->truncate();
        $this->connection->table('ConsultantComment')->truncate();
        
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('CommentActivityLog')->truncate();
        $this->connection->table('ConsultantActivityLog')->truncate();
        
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
        $this->connection->table('CommentMail')->truncate();
        
        $this->connection->table('Notification')->truncate();
        $this->connection->table('CommentNotification')->truncate();
        $this->connection->table('ClientNotificationRecipient')->truncate();
    }
    
    protected function setAsUserParticipant()
    {
        $this->connection->table("ClientParticipant")->truncate();
        
        $user = new RecordOfUser(0);
        $user->email = "adi@barapraja.com";
        $this->connection->table("User")->insert($user->toArrayForDbEntry());
        
        $this->userParticipant = new RecordOfUserParticipant($user, $this->participant);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());
    }
    protected function setAsTeamParticipant()
    {
        $this->connection->table("ClientParticipant")->truncate();
        
        $firm = $this->programConsultation->program->firm;
        $team = new RecordOfTeam($firm, $this->client, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $member = new RecordOfMember($team, $this->client, 0);
        $memberOne = new RecordOfMember($team, $this->clientOne, 1);
        $this->connection->table("T_Member")->insert($member->toArrayForDbEntry());
        $this->connection->table("T_Member")->insert($memberOne->toArrayForDbEntry());
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $this->participant);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
        
    }

    public function test_submitNew_201()
    {
        $this->connection->table('ConsultantComment')->truncate();
        $this->connection->table('Comment')->truncate();

        $response = [
            "message" => $this->submitNewRequest['message'],
            "removed" => false,
            "participant" => null,
        ];

        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->submitNewRequest['message'],
            "parent_id" => null,
            "removed" => false,
        ];
        $this->seeInDatabase("Comment", $commentEntry);

        $consultantCommentEntry = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantComment", $consultantCommentEntry);
    }
    public function test_submitNew_logActivity()
    {
        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201);
        
        $activityLogEntry = [
            "message" => "comment submitted",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultantActivityLog = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantActivityLog", $consultantActivityLog);
    }
    public function test_submitNew_persistNotificationAndSendMail()
    {
        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201);
        
        $notificationEntry = [
            "message" => "comment submitted",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->clientParticipant->client->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Komentar Worksheet",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->clientParticipant->client->email,
            "recipientName" => $this->clientParticipant->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
        
    }
    public function test_submitNew_teamParticipant_notifyAllMembers()
    {
        $this->setAsTeamParticipant();
        
        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201);
        
        $notificationEntry = [
            "message" => "comment submitted",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->client->id,
            "readStatus" => false,
        ];
        $clientOneNotificationRecipientEntry = [
            "Client_id" => $this->clientOne->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        $this->seeInDatabase("ClientNotificationRecipient", $clientOneNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Komentar Worksheet",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->client->email,
            "recipientName" => $this->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $recipientOneEntry = [
            "recipientMailAddress" => $this->clientOne->email,
            "recipientName" => $this->clientOne->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
        $this->seeInDatabase("MailRecipient", $recipientOneEntry);
    }
    public function test_submitNew_userParticipant_notifyUser()
    {
        $this->setAsUserParticipant();
        
        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201);
        
        $notificationEntry = [
            "message" => "comment submitted",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $userNotificationRecipientEntry = [
            "User_id" => $this->userParticipant->user->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("UserNotificationRecipient", $userNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Komentar Worksheet",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->userParticipant->user->email,
            "recipientName" => $this->userParticipant->user->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
    }
    public function test_submitNew_inactiveConsultant_403()
    {
        $this->removeProgramConsultation();
        $uri = $this->consultantCommentUri . "/new";
        $this->post($uri, $this->submitNewRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_submitReply()
    {
        $response = [
            "submitTime" => (new DateTime())->format('Y-m-d H:i:s'),
            "message" => $this->submitReplyRequest['message'],
            "removed" => false,
        ];

        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201)
                ->seeJsonContains($response);

        $commentEntry = [
            "Worksheet_id" => $this->worksheet->id,
            "message" => $this->submitReplyRequest['message'],
            "parent_id" => $this->commentOne->id,
            "removed" => false,
        ];
        $this->seeInDatabase("Comment", $commentEntry);

        $consultantCommentEntry = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantComment", $consultantCommentEntry);
    }
    public function test_submitReply_logActivity()
    {
        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201);
        
        $activityLogEntry = [
            "message" => "comment submitted",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultantActivityLog = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantActivityLog", $consultantActivityLog);
    }
    public function test_submitReply_persistNotificationAndSendMail()
    {
        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201);
        
        $notificationEntry = [
            "message" => "comment submitted",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->clientParticipant->client->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Komentar Worksheet",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->clientParticipant->client->email,
            "recipientName" => $this->clientParticipant->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
        
    }
    public function test_submitReply_teamParticipant_notifyAllMembers()
    {
        $this->setAsTeamParticipant();
        
        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201);
        
        $notificationEntry = [
            "message" => "comment submitted",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->client->id,
            "readStatus" => false,
        ];
        $clientOneNotificationRecipientEntry = [
            "Client_id" => $this->clientOne->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        $this->seeInDatabase("ClientNotificationRecipient", $clientOneNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Komentar Worksheet",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->client->email,
            "recipientName" => $this->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $recipientOneEntry = [
            "recipientMailAddress" => $this->clientOne->email,
            "recipientName" => $this->clientOne->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
        $this->seeInDatabase("MailRecipient", $recipientOneEntry);
    }
    public function test_submitReply_userParticipant_notifyUser()
    {
        $this->setAsUserParticipant();
        
        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(201);
        
        $notificationEntry = [
            "message" => "comment submitted",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $userNotificationRecipientEntry = [
            "User_id" => $this->userParticipant->user->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("UserNotificationRecipient", $userNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Komentar Worksheet",
        ];
        $this->seeInDatabase("Mail", $mailEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->userParticipant->user->email,
            "recipientName" => $this->userParticipant->user->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
    }
    public function test_submitReply_inactiveConsultant_403()
    {
        $this->removeProgramConsultation();
        
        $uri = $this->consultantCommentUri . "/reply";
        $this->post($uri, $this->submitReplyRequest, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
    }
    
}
