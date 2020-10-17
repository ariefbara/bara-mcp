<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\Participant\RecordOfConsultationRequest,
    Firm\Program\RecordOfConsultationSetup,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfTeam,
    Firm\Team\RecordOfMember,
    Firm\Team\RecordOfTeamProgramParticipation,
    RecordOfUser,
    Shared\RecordOfForm,
    User\RecordOfUserParticipant
};

class ConsultationRequestControllerTest extends ProgramConsultationTestCase
{

    protected $consultationRequestUri;
    protected $consultationRequest;
    protected $consultationRequest_concluded;
    protected $participant;
    protected $userParticipant;
    protected $clientParticipant;
    protected $teamParticipant;
    protected $teamMember;
    protected $teamMemberOne;
    protected $offerInput;

    protected function setUp(): void
    {
        parent::setUp();
        $this->consultationRequestUri = $this->programConsultationUri . "/consultation-requests";
        
        $this->connection->table('Client')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('T_Member')->truncate();
        
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        
        $this->connection->table('Form')->truncate();
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('ConsultationRequestActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
        $this->connection->table('ConsultantActivityLog')->truncate();
        
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
        $this->connection->table('CommentMail')->truncate();
        
        $this->connection->table('Notification')->truncate();
        $this->connection->table('ConsultationRequestNotification')->truncate();
        $this->connection->table('ConsultationSessionNotification')->truncate();
        $this->connection->table('ClientNotificationRecipient')->truncate();

        $program = $this->programConsultation->program;
        $firm = $program->firm;
        
        $user = new RecordOfUser(0);
        $user->email = "adi@barapraja.com";
        $this->connection->table('User')->insert($user->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $client->email = "purnama.adi@gmail.com";
        $clientOne = new RecordOfClient($firm, 1);
        $clientOne->email = "go.on.apur@gmail.com";
        $this->connection->table('Client')->insert($client->toArrayForDbEntry());
        $this->connection->table('Client')->insert($clientOne->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $client, 0);
        $this->connection->table('Team')->insert($team->toArrayForDbEntry());
        
        $this->teamMember = new RecordOfMember($team, $client, 0);
        $this->teamMemberOne = new RecordOfMember($team, $clientOne, 1);
        $this->connection->table('T_Member')->insert($this->teamMember->toArrayForDbEntry());
        $this->connection->table('T_Member')->insert($this->teamMemberOne->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table('Form')->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($this->programConsultation->program->firm, $form);
        $this->connection->table('FeedbackForm')->insert($feedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table('ConsultationSetup')->insert($consultationSetup->toArrayForDbEntry());
        
        
        $this->participant = new RecordOfParticipant($program, 0);
        $this->connection->table('Participant')->insert($this->participant->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $this->participant);
        $this->connection->table('ClientParticipant')->insert($this->clientParticipant->toArrayForDbEntry());
        
        $this->userParticipant = new RecordOfUserParticipant($user, $this->participant);
        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $this->participant);
        
        $this->consultationRequest = new RecordOfConsultationRequest(
                $consultationSetup, $this->participant, $this->programConsultation, 0);
        $this->consultationRequest->startDateTime = (new \DateTimeImmutable("+24 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequest->endDateTime = (new \DateTimeImmutable("+25 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequest_concluded = new RecordOfConsultationRequest(
                $consultationSetup, $this->participant, $this->programConsultation, 1);
        $this->consultationRequest_concluded->concluded = true;
        $this->consultationRequest_concluded->status = "rejected";
        $this->consultationRequest_concluded->startDateTime = (new \DateTimeImmutable("-24 hours"))->format("Y-m-d H:i:s");
        $this->consultationRequest_concluded->endDateTime = (new \DateTimeImmutable("-23 hours"))->format("Y-m-d H:i:s");
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest->toArrayForDbEntry());
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest_concluded->toArrayForDbEntry());
        
        $this->offerInput = [
            "startTime" => (new DateTime('+5 hours'))->format('Y-m-d H:i:s'),
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
        $this->connection->table('FeedbackForm')->truncate();
        $this->connection->table('ConsultationSetup')->truncate();
        $this->connection->table('ConsultationRequest')->truncate();
        $this->connection->table('ConsultationSession')->truncate();
        
        $this->connection->table('ActivityLog')->truncate();
        $this->connection->table('ConsultationRequestActivityLog')->truncate();
        $this->connection->table('ConsultationSessionActivityLog')->truncate();
        $this->connection->table('ConsultantActivityLog')->truncate();
        
        $this->connection->table('Mail')->truncate();
        $this->connection->table('MailRecipient')->truncate();
        $this->connection->table('CommentMail')->truncate();
        
        $this->connection->table('Notification')->truncate();
        $this->connection->table('ConsultationRequestNotification')->truncate();
         $this->connection->table('ConsultationSessionNotification')->truncate();
        $this->connection->table('ClientNotificationRecipient')->truncate();
    }
    protected function setAsTeamParticipant()
    {
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
    }
    protected function setAsUserParticipant()
    {
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());
    }
    
    public function test_reject()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/reject";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "concluded" => true,
            "status" => "rejected",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
    }
    public function test_reject_logActivity()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/reject";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "consultation request rejected",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultationRequestActivityLogEntry = [
            "ConsultationRequest_id" => $this->consultationRequest->id,
        ];
        $this->seeInDatabase("ConsultationRequestActivityLog", $consultationRequestActivityLogEntry);
        
        $consultantActivityLog = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantActivityLog", $consultantActivityLog);
    }
    public function test_reject_persistNotificationAndSendMail()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/reject";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $notificationEntry = [
            "message" => "consultation request rejected",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->clientParticipant->client->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Permintaan Konsultasi",
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
    public function test_reject_teamParticipant_notifyAllMembers()
    {
        $this->setAsTeamParticipant();
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/reject";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->teamMember->client->id,
            "readStatus" => false,
        ];
        $clientOneNotificationRecipientEntry = [
            "Client_id" => $this->teamMemberOne->client->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        $this->seeInDatabase("ClientNotificationRecipient", $clientOneNotificationRecipientEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->teamMember->client->email,
            "recipientName" => $this->teamMember->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $recipientOneEntry = [
            "recipientMailAddress" => $this->teamMemberOne->client->email,
            "recipientName" => $this->teamMemberOne->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
        $this->seeInDatabase("MailRecipient", $recipientOneEntry);
    }
    public function test_reject_userParticipant_notifyUser()
    {
        $this->setAsUserParticipant();
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/reject";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $userNotificationRecipientEntry = [
            "User_id" => $this->userParticipant->user->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("UserNotificationRecipient", $userNotificationRecipientEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->userParticipant->user->email,
            "recipientName" => $this->userParticipant->user->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
    }
    public function test_reject_consultationRequestAlreadyConcluded_error403()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest_concluded->id}/reject";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_offer()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "startTime" => $this->offerInput['startTime'],
            "endTime" => (new DateTime($this->offerInput['startTime']))->add(new \DateInterval("PT1H"))->format("Y-m-d H:i:s"),
            "status" => "offered",
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultation->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "startDateTime" => $this->offerInput['startTime'],
            "endDateTime" => (new DateTime($this->offerInput['startTime']))->add(new \DateInterval("PT1H"))->format("Y-m-d H:i:s"),
            "status" => "offered",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
    }
    public function test_offer_logActivity()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "consultation request new schedule offered",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultationRequestActivityLogEntry = [
            "ConsultationRequest_id" => $this->consultationRequest->id,
        ];
        $this->seeInDatabase("ConsultationRequestActivityLog", $consultationRequestActivityLogEntry);
        
        $consultantActivityLog = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantActivityLog", $consultantActivityLog);
    }
    public function test_offer_persistNotificationAndSendMail()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $notificationEntry = [
            "message" => "consultation request offered",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->clientParticipant->client->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Permintaan Konsultasi",
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
    public function test_offer_teamParticipant_notifyAllMembers()
    {
        $this->setAsTeamParticipant();
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->teamMember->client->id,
            "readStatus" => false,
        ];
        $clientOneNotificationRecipientEntry = [
            "Client_id" => $this->teamMemberOne->client->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        $this->seeInDatabase("ClientNotificationRecipient", $clientOneNotificationRecipientEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->teamMember->client->email,
            "recipientName" => $this->teamMember->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $recipientOneEntry = [
            "recipientMailAddress" => $this->teamMemberOne->client->email,
            "recipientName" => $this->teamMemberOne->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
        $this->seeInDatabase("MailRecipient", $recipientOneEntry);
    }
    public function test_offer_userParticipant_notifyUser()
    {
        $this->setAsUserParticipant();
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $userNotificationRecipientEntry = [
            "User_id" => $this->userParticipant->user->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("UserNotificationRecipient", $userNotificationRecipientEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->userParticipant->user->email,
            "recipientName" => $this->userParticipant->user->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
    }
    public function test_offer_consultationRequestAlreadyConcluded_error403()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest_concluded->id}/offer";
        $this->patch($uri, $this->offerInput, $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_accept()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "status" => "scheduled",
        ];
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200)
                ->seeJsonContains($response);
        
        $consultationRequestEntry = [
            "id" => $this->consultationRequest->id,
            "status" => "scheduled",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
    }
    public function test_accept_persistConsultationSession()
    {
        $this->connection->table('ConsultationSession')->truncate();
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        $consulattionSessionEntry = [
            "Participant_id" => $this->consultationRequest->participant->id,
            "Consultant_id" => $this->consultationRequest->consultant->id,
            "ConsultationSetup_id" => $this->consultationRequest->consultationSetup->id,
            "startDateTime" => $this->consultationRequest->startDateTime,
            "endDateTime" => $this->consultationRequest->endDateTime,
        ];
        $this->seeInDatabase("ConsultationSession", $consulattionSessionEntry);
        
    }
    public function test_accept_logActivity()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $activityLogEntry = [
            "message" => "Jadwal Konsultasi Disepakati",
        ];
        $this->seeInDatabase("ActivityLog", $activityLogEntry);
        
        $consultantActivityLog = [
            "Consultant_id" => $this->programConsultation->id,
        ];
        $this->seeInDatabase("ConsultantActivityLog", $consultantActivityLog);
    }
    public function test_accept_persistNotificationAndSendMail()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $notificationEntry = [
            "message" => "Jadwal Konsultasi Disepakati",
        ];
        $this->seeInDatabase("Notification", $notificationEntry);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->clientParticipant->client->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        
        $mailEntry = [
            "subject" => "Konsulta: Jadwal Konsultasi",
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
    public function test_accept_teamParticipant_notifyAllMembers()
    {
        $this->setAsTeamParticipant();
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $clientNotificationRecipientEntry = [
            "Client_id" => $this->teamMember->client->id,
            "readStatus" => false,
        ];
        $clientOneNotificationRecipientEntry = [
            "Client_id" => $this->teamMemberOne->client->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("ClientNotificationRecipient", $clientNotificationRecipientEntry);
        $this->seeInDatabase("ClientNotificationRecipient", $clientOneNotificationRecipientEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->teamMember->client->email,
            "recipientName" => $this->teamMember->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $recipientOneEntry = [
            "recipientMailAddress" => $this->teamMemberOne->client->email,
            "recipientName" => $this->teamMemberOne->client->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
        $this->seeInDatabase("MailRecipient", $recipientOneEntry);
    }
    public function test_accept_userParticipant_notifyUser()
    {
        $this->setAsUserParticipant();
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(200);
        
        $userNotificationRecipientEntry = [
            "User_id" => $this->userParticipant->user->id,
            "readStatus" => false,
        ];
        $this->seeInDatabase("UserNotificationRecipient", $userNotificationRecipientEntry);
        
        $recipientEntry = [
            "recipientMailAddress" => $this->userParticipant->user->email,
            "recipientName" => $this->userParticipant->user->getFullName(),
            "sent" => true,
            "attempt" => 1,
        ];
        $this->seeInDatabase("MailRecipient", $recipientEntry);
    }
    public function test_accept_statusNotProposed_error403()
    {
        $this->connection->table('ConsultationRequest')->truncate();
        $this->consultationRequest->status = 'offered';
        $this->connection->table('ConsultationRequest')->insert($this->consultationRequest->toArrayForDbEntry());
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}/accept";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_accept_consultationRequestAlreadyConcluded_error403()
    {
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest_concluded->id}/accept";
        $this->patch($uri, [], $this->programConsultation->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_show()
    {
        $response = [
            "id" => $this->consultationRequest->id,
            "startTime" => $this->consultationRequest->startDateTime,
            "endTime" => $this->consultationRequest->endDateTime,
            "concluded" => $this->consultationRequest->concluded,
            "status" => $this->consultationRequest->status,
            "consultationSetup" => [
                "id" => $this->consultationRequest->consultationSetup->id,
                "name" => $this->consultationRequest->consultationSetup->name,
            ],
            "participant" => [
                "id" => $this->consultationRequest->participant->id,
                "client" => [
                    "id" => $this->clientParticipant->client->id,
                    "name" => $this->clientParticipant->client->getFullName(),
                ],
                "user" => null,
                "team" => null,
            ],
        ];
        
        $uri = $this->consultationRequestUri . "/{$this->consultationRequest->id}";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll()
    {
        $response = [
            "total" => 2, 
            "list" => [
                [
                    "id" => $this->consultationRequest->id,
                    "startTime" => $this->consultationRequest->startDateTime,
                    "endTime" => $this->consultationRequest->endDateTime,
                    "concluded" => $this->consultationRequest->concluded,
                    "status" => $this->consultationRequest->status,
                    "consultationSetup" => [
                        "id" => $this->consultationRequest->consultationSetup->id,
                        "name" => $this->consultationRequest->consultationSetup->name,
                    ],
                    "participant" => [
                        "id" => $this->consultationRequest->participant->id,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "user" => null,
                        "team" => null,
                    ],
                ],
                [
                    "id" => $this->consultationRequest_concluded->id,
                    "startTime" => $this->consultationRequest_concluded->startDateTime,
                    "endTime" => $this->consultationRequest_concluded->endDateTime,
                    "concluded" => $this->consultationRequest_concluded->concluded,
                    "status" => $this->consultationRequest_concluded->status,
                    "consultationSetup" => [
                        "id" => $this->consultationRequest_concluded->consultationSetup->id,
                        "name" => $this->consultationRequest_concluded->consultationSetup->name,
                    ],
                    "participant" => [
                        "id" => $this->consultationRequest_concluded->participant->id,
                        "client" => [
                            "id" => $this->clientParticipant->client->id,
                            "name" => $this->clientParticipant->client->getFullName(),
                        ],
                        "user" => null,
                        "team" => null,
                    ],
                ],
            ],
        ];
        $this->get($this->consultationRequestUri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_minStartTimeFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->consultationRequest->id,
        ];
        $uri = $this->consultationRequestUri . "?minStartTime=" . (new \DateTimeImmutable())->format("Y-m-d H:i:s");
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_maxEndTimeFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->consultationRequest_concluded->id,
        ];
        
        $uri = $this->consultationRequestUri . "?maxEndTime=" . (new \DateTimeImmutable())->format("Y-m-d H:i:s");
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_concludedStatusFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->consultationRequest->id,
        ];
        $uri = $this->consultationRequestUri . "?concludedStatus=false";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }
    public function test_showAll_statusFilterSet_200()
    {
        $totalResponse = [
            "total" => 1,
        ];
        $objectReponse = [
            "id" => $this->consultationRequest_concluded->id,
        ];
        
        $uri = $this->consultationRequestUri . "?status[]=rejected";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($objectReponse)
                ->seeStatusCode(200);
    }
}
