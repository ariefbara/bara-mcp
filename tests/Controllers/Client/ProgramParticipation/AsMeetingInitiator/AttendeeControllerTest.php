<?php

namespace Tests\Controllers\Client\ProgramParticipation\AsMeetingInitiator;

use Tests\Controllers\MailChecker;
use Tests\Controllers\NotificationChecker;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\RecordOfManager;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfMember;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class AttendeeControllerTest extends AsMeetingInitiatorTestCase
{
    protected $attendeeUri;
    
    /** @var RecordOfManager */
    protected $managerOne;
    /** @var RecordOfCoordinator */
    protected $coordinatorOne;
    /** @var RecordOfConsultant */
    protected $consultantOne;
    /** @var RecordOfTeamProgramParticipation */
    protected $teamParticipantOne;
    /** @var RecordOfMember */
    protected $teamMemberOne;
    /** @var RecordOfActivityInvitation */
    protected $coordinatorAttendee;

    protected function setUp(): void
    {
        parent::setUp();
        $this->attendeeUri = $this->asMeetingInitiatorUri . "/attendees";
        $this->connection->table("Mail")->truncate();
        $this->connection->table("MeetingMail")->truncate();
        $this->connection->table("MailRecipient")->truncate();
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("MeetingAttendeeNotification")->truncate();
/*
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("ManagerInvitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();

        $activityType = $this->meeting->activityType;
        $program = $activityType->program;
        $firm = $program->firm;

        $personnel = new RecordOfPersonnel($firm, 0);
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        $this->connection->table("Personnel")->insert($personnelOne->toArrayForDbEntry());

        $client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());

        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());

        $team = new RecordOfTeam($firm, $client, 0);
        $teamOne = new RecordOfTeam($firm, $client, 1);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        $this->connection->table("Team")->insert($teamOne->toArrayForDbEntry());

        $manager = new RecordOfManager($firm, 0, "manager@email.org", "Password123");
        $this->managerOne = new RecordOfManager($firm, 1, "managerOne@email.org", "Password123");
        $this->connection->table("Manager")->insert($manager->toArrayForDbEntry());
        $this->connection->table("Manager")->insert($this->managerOne->toArrayForDbEntry());

        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->coordinatorOne = new RecordOfCoordinator($program, $personnelOne, 1);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());
        $this->connection->table("Coordinator")->insert($this->coordinatorOne->toArrayForDbEntry());

        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        $this->connection->table("Consultant")->insert($this->consultantOne->toArrayForDbEntry());

        $participant = new RecordOfParticipant($program, 0);
        $participantOne = new RecordOfParticipant($program, 1);
        $participantTwo = new RecordOfParticipant($program, 2);
        $this->participantThree = new RecordOfParticipant($program, 3);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantOne->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($participantTwo->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantThree->toArrayForDbEntry());

        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());

        $this->userParticipant = new RecordOfUserParticipant($user, $participantOne);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());

        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $participantTwo);
        $this->teamParticipantThree = new RecordOfTeamProgramParticipation($teamOne, $this->participantThree);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
        $this->connection->table("TeamParticipant")->insert($this->teamParticipantThree->toArrayForDbEntry());

        $activityParticipant_coordinator = $this->meetingAttendace->invitee->activityParticipant;
        $activityParticipant_consultant = new RecordOfActivityParticipant($activityType, null, 0);
        $activityParticipant_consultant->participantType = "consultant";
        $activityParticipant_manager = new RecordOfActivityParticipant($activityType, null, 1);
        $activityParticipant_manager->participantType = "manager";
        $activityParticipant_coordinator = new RecordOfActivityParticipant($activityType, null, 2);
        $activityParticipant_coordinator->participantType = "coordinator";
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_consultant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_manager->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipant_coordinator->toArrayForDbEntry());

        $attendee_manager = new RecordOfInvitee($this->meeting, $activityParticipant_manager, 0);
        $attendee_coordinator = new RecordOfInvitee($this->meeting, $activityParticipant_coordinator, 1);
        $attendee_consultant = new RecordOfInvitee($this->meeting, $activityParticipant_consultant, 2);
        $attendee_clientParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_coordinator, 3);
        $attendee_userParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_coordinator, 4);
        $attendee_teamParticipant = new RecordOfInvitee($this->meeting, $activityParticipant_coordinator, 5);
        $this->connection->table("Invitee")->insert($attendee_manager->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_coordinator->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_consultant->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_clientParticipant->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_userParticipant->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($attendee_teamParticipant->toArrayForDbEntry());


        $this->managerAttendee = new RecordOfActivityInvitation($manager, $attendee_manager);
        $this->connection->table("ManagerInvitee")->insert($this->managerAttendee->toArrayForDbEntry());

        $this->coordinatorAttendee = new RecordOfActivityInvitation2($coordinator, $attendee_coordinator);
        $this->connection->table("CoordinatorInvitee")->insert($this->coordinatorAttendee->toArrayForDbEntry());

        $this->consultantAttendee = new RecordOfActivityInvitation3($consultant, $attendee_consultant);
        $this->connection->table("ConsultantInvitee")->insert($this->consultantAttendee->toArrayForDbEntry());

        $this->clientParticipantAttendee = new RecordOfActivityInvitation4($participant, $attendee_clientParticipant);
        $this->userParticipantAttendee = new RecordOfActivityInvitation4($participantOne, $attendee_userParticipant);
        $this->teamParticipantAttendee = new RecordOfActivityInvitation4($participantTwo, $attendee_teamParticipant);
        $this->connection->table("ParticipantInvitee")->insert($this->clientParticipantAttendee->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->userParticipantAttendee->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->teamParticipantAttendee->toArrayForDbEntry());
        
        $this->inviteManagerInput = [
            "managerId" => $this->managerOne->id,
        ];
        $this->inviteCoordinatorInput = [
            "coordinatorId" => $this->coordinatorOne->id,
        ];
        $this->inviteConsultantInput = [
            "consultantId" => $this->consultantOne->id,
        ];
        $this->inviteParticipantInput = [
            "participantId" => $this->participantThree->id,
        ];
 * 
 */
    }
    protected function setManagerUp()
    {
        RecordOfManager::truncateTable($this->connection);
        $firm = $this->programParticipation->participant->program->firm;
        $this->managerOne = new RecordOfManager($firm, 999);
        $this->managerOne->email = "purnama.adi+manager@gmail.com";
        $this->managerOne->persistSelf($this->connection);
        
        $this->inviteManagerInput = [
            "managerId" => $this->managerOne->id,
        ];
    }
    protected function tearManagerDown()
    {
        RecordOfManager::truncateTable($this->connection);
    }
    protected function setCoordinatorUp()
    {
        RecordOfCoordinator::truncateTable($this->connection);
        $this->connection->table("Personnel")->truncate();
        $program = $this->programParticipation->participant->program;
        $this->coordinatorOne = new RecordOfCoordinator($program, null, 1);
        $this->coordinatorOne->personnel->email = "purnama.adi+coordinator@gmail.com";
        
        $this->coordinatorOne->persistSelf($this->connection);
        $this->coordinatorOne->personnel->persistSelf($this->connection);
        
        $this->inviteCoordinatorInput = [
            "coordinatorId" => $this->coordinatorOne->id,
        ];
    }
    protected function tearCoordinatorDown()
    {
        RecordOfCoordinator::truncateTable($this->connection);
        RecordOfPersonnel::truncateTable($this->connection);
    }
    protected function clearConslutantDependency()
    {
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Consultant")->truncate();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Mail")->truncate();
        $this->connection->table("MeetingMail")->truncate();
        $this->connection->table("MailRecipient")->truncate();
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("MeetingAttendeeNotification")->truncate();
    }
    protected function setCoordinatorInviteeUp()
    {
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("PersonnelNotificationRecipient")->truncate();
        
        $this->coordinatorAttendee = new RecordOfActivityInvitation(null, null, 1);
        $this->coordinatorAttendee->invitee->activity = $this->meeting;
        $this->coordinatorAttendee->coordinator->personnel->email = "purnama.adi+coordinator@gmail.com";
        
        
        $this->coordinatorAttendee->persistSelf($this->connection);
        $this->coordinatorAttendee->invitee->persistSelf($this->connection);
        $this->coordinatorAttendee->coordinator->persistSelf($this->connection);
        $this->coordinatorAttendee->coordinator->personnel->persistSelf($this->connection);
    }
    
    public function test_showAll_200()
    {
        $this->setCoordinatorInviteeUp();
        
        $response = [
            "total" => 1,
            "list" => [
                [
                    "id" => $this->coordinatorAttendee->invitee->id,
                    "willAttend" => $this->coordinatorAttendee->invitee->willAttend,
                    "attended" => $this->coordinatorAttendee->invitee->attended,
                    "manager" => null,
                    "coordinator" => [
                         "id" => $this->coordinatorAttendee->coordinator->id,
                        "personnel" => [
                             "id" => $this->coordinatorAttendee->coordinator->personnel->id,
                             "name" => $this->coordinatorAttendee->coordinator->personnel->getFullName(),
                        ],
                    ],
                    "consultant" => null,
                    "participant" => null,
                ],
            ],
        ];
        
        $this->get($this->attendeeUri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_show_200()
    {
        $this->setCoordinatorInviteeUp();
        $response = [
            "id" => $this->coordinatorAttendee->invitee->id,
            "willAttend" => $this->coordinatorAttendee->invitee->willAttend,
            "attended" => $this->coordinatorAttendee->invitee->attended,
            "manager" => null,
            "coordinator" => [
                 "id" => $this->coordinatorAttendee->coordinator->id,
                "personnel" => [
                     "id" => $this->coordinatorAttendee->coordinator->personnel->id,
                     "name" => $this->coordinatorAttendee->coordinator->personnel->getFullName(),
                ],
            ],
            "consultant" => null,
            "participant" => null,
        ];
        $uri = $this->attendeeUri . "/{$this->coordinatorAttendee->invitee->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    protected function executeInviteManager()
    {
        $this->setManagerCanBeInvited();
        $this->setManagerUp();
        $mailChecker = new MailChecker();
        $notificationChecker = new NotificationChecker();
        
        $uri = $this->attendeeUri . "/invite-manager";
        $this->put($uri, $this->inviteManagerInput, $this->programParticipation->client->token);
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
        $this->setCoordinatorCanBeInvited();
        $this->setCoordinatorUp();
        $mailChecker = new MailChecker();
        $notificationChecker = new NotificationChecker();
        
        $uri = $this->attendeeUri . "/invite-coordinator";
        $this->put($uri, $this->inviteCoordinatorInput, $this->programParticipation->client->token);
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
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Personnel")->truncate();
        
        $this->connection->table("Mail")->truncate();
        $this->connection->table("MeetingMail")->truncate();
        $this->connection->table("MailRecipient")->truncate();
        
        $this->connection->table("Notification")->truncate();
        $this->connection->table("MeetingAttendeeNotification")->truncate();
        $this->connection->table("PersonnelNotificationRecipient")->truncate();
        
        $this->consultantOne = new RecordOfConsultant($this->programParticipation->participant->program, null, 1);
        $this->consultantOne->personnel->email = "purnama.adi+consultant@gmail.com";
        $this->consultantOne->persistSelf($this->connection);
        $this->consultantOne->personnel->persistSelf($this->connection);
        
        
        $this->setConsultantCanBeInvited();
        $uri = $this->attendeeUri . "/invite-consultant";
        $input = ["consultantId" => $this->consultantOne->id];
        $this->put($uri, $input, $this->programParticipation->client->token);

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
        $this->setParticipantCanBeInvited();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("T_Member")->truncate();
        $this->connection->table("ClientNotificationRecipient")->truncate();
        
        $program = $this->programParticipation->participant->program;
        $firm = $program->firm;
        
        $this->teamParticipantOne = new RecordOfTeamProgramParticipation(null, null, 1);
        $this->teamParticipantOne->persistSelf($this->connection);
        $this->teamParticipantOne->team->firm = $firm;
        $this->teamParticipantOne->team->persistSelf($this->connection);
        $this->teamParticipantOne->participant->program = $program;
        $this->teamParticipantOne->participant->persistSelf($this->connection);
        
        $this->teamMemberOne = new RecordOfMember($this->teamParticipantOne->team, null, 1);
        $this->teamMemberOne->persistSelf($this->connection);
        $this->teamMemberOne->client->email = "purnama.adi+teamMemberOne@gmail.com";
        $this->teamMemberOne->client->firm = $firm;
        $this->teamMemberOne->client->persistSelf($this->connection);
        

        $uri = $this->attendeeUri . "/invite-participant";
        $input = ["participantId" => $this->teamParticipantOne->participant->id];
        $this->put($uri, $input, $this->programParticipation->client->token);
    }
    public function test_inviteParticipant_200()
    {
        $this->executeInviteParticipant();
        $this->seeStatusCode(200);
        
        $participantInviteeEntry = [
            "Participant_id" => $this->teamParticipantOne->participant->id,
        ];
        $this->seeInDatabase("ParticipantInvitee", $participantInviteeEntry);
    }
    public function test_inviteParticipant_sendEmailAndNotification()
    {
        $this->executeInviteParticipant();
        
        (new MailChecker())->checkMailExist($subject = "Meeting Invitation", $recipientEmail = $this->teamMemberOne->client->email);
        (new NotificationChecker())
                ->checkNotificationExist($message = "meeting invitation received")
                ->checkClientNotificationExist($this->teamMemberOne->client->id);
    }
    
    protected function executeCancelInvitation()
    {
        $this->connection->table("CoordinatorInvitee")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("PersonnelNotificationRecipient")->truncate();
        
        $this->coordinatorAttendee = new RecordOfActivityInvitation(null, null, 1);
        $this->coordinatorAttendee->invitee->activity = $this->meeting;
        $this->coordinatorAttendee->coordinator->personnel->email = "purnama.adi+coordinator@gmail.com";
        
        
        $this->coordinatorAttendee->persistSelf($this->connection);
        $this->coordinatorAttendee->invitee->persistSelf($this->connection);
        $this->coordinatorAttendee->coordinator->persistSelf($this->connection);
        $this->coordinatorAttendee->coordinator->personnel->persistSelf($this->connection);

        $uri = $this->attendeeUri . "/cancel-invitation/{$this->coordinatorAttendee->invitee->id}";
        $this->patch($uri, [], $this->programParticipation->client->token);
    }
    public function test_cancelInvitation_200()
    {
        $this->executeCancelInvitation();
        $this->seeStatusCode(200);
        
        $inviteeEntry = [
            "id" => $this->coordinatorAttendee->invitee->id,
            "cancelled" => true,
        ];
        $this->seeInDatabase("Invitee", $inviteeEntry);
    }
    public function test_cancelInvitation_sendEmailAndNotification()
    {
        $this->executeCancelInvitation();
        
        (new MailChecker())->checkMailExist(
                $subject = "Meeting Invitation Cancelled", $recipientEmail = $this->coordinatorAttendee->coordinator->personnel->email);
        (new NotificationChecker())
                ->checkNotificationExist($message = "meeting invitation cancelled")
                ->checkPersonnelNotificationExist($this->coordinatorAttendee->coordinator->personnel->id);
    }

}
