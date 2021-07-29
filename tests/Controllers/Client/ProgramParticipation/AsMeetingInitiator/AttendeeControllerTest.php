<?php

namespace Tests\Controllers\Client\ProgramParticipation\AsMeetingInitiator;

use Tests\Controllers\MailChecker;
use Tests\Controllers\NotificationChecker;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class AttendeeControllerTest extends AsMeetingInitiatorTestCase
{
    protected $attendeeUri;
    protected $showAttendeeUri;
    
    protected $managerOne;
    protected $coordinatorOne;
    protected $consultantOne;
    protected $clientParticipantOne;
    protected $userParticipantOne;
    protected $teamParticipantOne;
    protected $activityParticipantOne;
    protected $coordinatorInviteeOne;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendeeUri = $this->meetingInitiatorUri . "/attendees";
        $this->showAttendeeUri = $this->asMeetingInitiatorUri . "/attendees";
        
        $this->connection->table("Mail")->truncate();
        $this->connection->table("MeetingMail")->truncate();
        $this->connection->table("MailRecipient")->truncate();
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("MeetingAttendeeNotification")->truncate();
        
        $this->connection->table("Manager")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("TeamParticipant")->truncate();

        $activityType = $this->meeting->activityType;
        $program = $activityType->program;
        $firm = $program->firm;

        $this->managerOne = new RecordOfManager($firm, '1');
        
        $personnelOne = new RecordOfPersonnel($firm, '1');
        $personnelTwo = new RecordOfPersonnel($firm, '2');
        
        $this->coordinatorOne = new RecordOfCoordinator($program, $personnelOne, '1');
        $this->consultantOne = new RecordOfConsultant($program, $personnelTwo, '1');
        
        $userOne = new RecordOfUser('1');
        
        $participantOne = new RecordOfParticipant($program, '1');
        $participantTwo = new RecordOfParticipant($program, '2');
        $participantThree = new RecordOfParticipant($program, '3');
        
        $this->userParticipantOne = new RecordOfUserParticipant($userOne, $participantOne);
        
        $teamOne = new RecordOfTeam($firm, null, '1');
        
        $this->teamParticipantOne = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);

        $clientOne = new RecordOfClient($firm, '1');
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantThree);
        
        $this->activityParticipantOne = new RecordOfActivityParticipant($this->meeting->activityType, null, '1');
        
        $inviteeOne = new RecordOfInvitee($this->meeting, $this->activityParticipantOne, '1');
        
        $this->coordinatorInviteeOne = new RecordOfCoordinatorInvitee($this->coordinatorOne, $inviteeOne);
        
        $this->inviteManagerInput = [
            "managerId" => $this->managerOne->id,
        ];
        $this->inviteCoordinatorInput = [
            "coordinatorId" => $this->coordinatorOne->id,
        ];
        $this->inviteConsultantInput = [
            "consultantId" => $this->consultantOne->id,
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Mail")->truncate();
        $this->connection->table("MeetingMail")->truncate();
        $this->connection->table("MailRecipient")->truncate();
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("MeetingAttendeeNotification")->truncate();
        
        $this->connection->table("Manager")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
    }
    
    protected function executeShowAll()
    {
        $this->coordinatorInviteeOne->coordinator->personnel->insert($this->connection);
        $this->coordinatorInviteeOne->coordinator->insert($this->connection);
        $this->coordinatorInviteeOne->insert($this->connection);
        
        $this->get($this->showAttendeeUri, $this->client->token);
    }
    public function test_showAll_200()
    {
        $this->executeShowAll();
        $this->seeStatusCode(200);
        
        $totalResponse = ['total' => 1];
        $this->seeJsonContains($totalResponse);
        
        $coordinatorInviteeOneResponse = [
            "id" => $this->coordinatorInviteeOne->invitee->id,
            "willAttend" => $this->coordinatorInviteeOne->invitee->willAttend,
            "attended" => $this->coordinatorInviteeOne->invitee->attended,
            "manager" => null,
            "coordinator" => [
                 "id" => $this->coordinatorInviteeOne->coordinator->id,
                "personnel" => [
                     "id" => $this->coordinatorInviteeOne->coordinator->personnel->id,
                     "name" => $this->coordinatorInviteeOne->coordinator->personnel->getFullName(),
                ],
            ],
            "consultant" => null,
            "participant" => null,
        ];
        $this->seeJsonContains($coordinatorInviteeOneResponse);
    }
    
    protected function executeShow()
    {
        $this->coordinatorInviteeOne->coordinator->personnel->insert($this->connection);
        $this->coordinatorInviteeOne->coordinator->insert($this->connection);
        $this->coordinatorInviteeOne->insert($this->connection);
        
        $this->get($this->showAttendeeUri . "/{$this->coordinatorInviteeOne->invitee->id}", $this->client->token);
    }
    public function test_show_200()
    {
        $this->executeShow();
        $this->seeStatusCode(200);
        
        $response = [
            "id" => $this->coordinatorInviteeOne->invitee->id,
            "willAttend" => $this->coordinatorInviteeOne->invitee->willAttend,
            "attended" => $this->coordinatorInviteeOne->invitee->attended,
            "manager" => null,
            "coordinator" => [
                 "id" => $this->coordinatorInviteeOne->coordinator->id,
                "personnel" => [
                     "id" => $this->coordinatorInviteeOne->coordinator->personnel->id,
                     "name" => $this->coordinatorInviteeOne->coordinator->personnel->getFullName(),
                ],
            ],
            "consultant" => null,
            "participant" => null,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function executeInviteManager()
    {
        $this->activityParticipantOne->participantType = 'manager';
        $this->activityParticipantOne->insert($this->connection);
        
        $this->managerOne->insert($this->connection);
        
        $uri = $this->attendeeUri . "/invite-manager";
        $this->put($uri, ['managerId' => $this->managerOne->id], $this->programParticipation->client->token);
    }
    public function test_inviteManager_200()
    {
        $this->executeInviteManager();
        $this->seeStatusCode(200);
        
        $managerInviteeEntry = [
            "Manager_id" => $this->managerOne->id,
        ];
        $this->seeInDatabase("ManagerInvitee", $managerInviteeEntry);
    }
    public function test_inviteManager_sendMailAndNotfication_200()
    {
        $this->executeInviteManager();
        (new MailChecker())->checkMailExist($subject = "Meeting Invitation", $this->managerOne->email);
        (new NotificationChecker())
                ->checkNotificationExist($message = "meeting invitation received")
                ->checkManagerNotificationExist($this->managerOne->id);
    }
    
    protected function executeInviteCoordinator()
    {
        $this->activityParticipantOne->participantType = 'coordinator';
        $this->activityParticipantOne->insert($this->connection);
        
        $this->coordinatorOne->personnel->insert($this->connection);
        $this->coordinatorOne->insert($this->connection);
        
        $uri = $this->attendeeUri . "/invite-coordinator";
        $this->put($uri, ['coordinatorId' => $this->coordinatorOne->id], $this->programParticipation->client->token);
    }
    public function test_inviteCoordinator_200()
    {
        $this->executeInviteCoordinator();
        $this->seeStatusCode(200);
        
        $coordinatorInviteeEntry = [
            "Coordinator_id" => $this->coordinatorOne->id,
        ];
        $this->seeInDatabase("CoordinatorInvitee", $coordinatorInviteeEntry);
    }
    public function test_inviteCoordinator_sendMailAndNotification()
    {
        $this->executeInviteCoordinator();
        
        (new MailChecker())->checkMailExist($subject = "Meeting Invitation", $this->coordinatorOne->personnel->email);
        (new NotificationChecker())
                ->checkNotificationExist($message = "meeting invitation received")
                ->checkPersonnelNotificationExist($this->coordinatorOne->personnel->id);
    }
    
    protected function executeInviteConslutant()
    {
        $this->activityParticipantOne->participantType = 'consultant';
        $this->activityParticipantOne->insert($this->connection);
        
        $this->consultantOne->personnel->insert($this->connection);
        $this->consultantOne->insert($this->connection);
        
        $uri = $this->attendeeUri . "/invite-consultant";
        $this->put($uri, ['consultantId' => $this->consultantOne->id], $this->programParticipation->client->token);
    }
    public function test_inviteConsultant_200()
    {
        $this->executeInviteConslutant();
        $this->seeStatusCode(200);
        
        $consultantInviteeEntry = [
            "Consultant_id" => $this->consultantOne->id,
        ];
        $this->seeInDatabase("ConsultantInvitee", $consultantInviteeEntry);
    }
    public function test_inviteConsultant_sendMailAndNotification()
    {
        $this->executeInviteConslutant();
        $this->seeStatusCode(200);
        
        (new MailChecker())->checkMailExist($subject = "Meeting Invitation", $this->consultantOne->personnel->email);
        (new NotificationChecker())
                ->checkNotificationExist($message = "meeting invitation received")
                ->checkPersonnelNotificationExist($this->consultantOne->personnel->id);
        
    }
    
    protected function executeInviteParticipant()
    {
        $this->activityParticipantOne->participantType = 'participant';
        $this->activityParticipantOne->insert($this->connection);
        
        $this->userParticipantOne->user->insert($this->connection);
        $this->userParticipantOne->insert($this->connection);
        
        $uri = $this->attendeeUri . "/invite-participant";
        $this->put($uri, ['participantId' => $this->userParticipantOne->participant->id], $this->programParticipation->client->token);
    }
    public function test_inviteParticipant_200()
    {
        $this->executeInviteParticipant();
        $this->seeStatusCode(200);
        
        $participantInviteeEntry = [
            "Participant_id" => $this->userParticipantOne->participant->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInviteeEntry);
    }
    public function test_inviteParticipant_sendEmailAndNotification()
    {
        $this->executeInviteParticipant();
        
        (new MailChecker())->checkMailExist($subject = "Meeting Invitation", $recipientEmail = $this->userParticipantOne->user->email);
        (new NotificationChecker())
                ->checkNotificationExist($message = "meeting invitation received")
                ->checkUserNotificationExist($this->userParticipantOne->user->id);
    }
    
    protected function executeCancelInvitation()
    {
        $this->coordinatorInviteeOne->coordinator->personnel->insert($this->connection);
        $this->coordinatorInviteeOne->coordinator->insert($this->connection);
        $this->coordinatorInviteeOne->insert($this->connection);

        $uri = $this->attendeeUri . "/cancel-invitation/{$this->coordinatorInviteeOne->invitee->id}";
        $this->patch($uri, [], $this->programParticipation->client->token);
    }
    public function test_cancelInvitation_200()
    {
        $this->executeCancelInvitation();
        $this->seeStatusCode(200);
        
        $inviteeEntry = [
            "id" => $this->coordinatorInviteeOne->invitee->id,
            "cancelled" => true,
        ];
        $this->seeInDatabase("Invitee", $inviteeEntry);
    }
    public function test_cancelInvitation_sendEmailAndNotification()
    {
        $this->executeCancelInvitation();
        
        (new MailChecker())->checkMailExist(
                $subject = "Meeting Invitation Cancelled", $recipientEmail = $this->coordinatorInviteeOne->coordinator->personnel->email);
        (new NotificationChecker())
                ->checkNotificationExist($message = "meeting invitation cancelled")
                ->checkPersonnelNotificationExist($this->coordinatorInviteeOne->coordinator->personnel->id);
    }

}
