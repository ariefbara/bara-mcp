<?php

namespace Tests\Controllers\Personnel\Coordinator;

use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Manager\RecordOfManagerActivity,
    Firm\Program\Activity\RecordOfInvitee,
    Firm\Program\ActivityType\RecordOfActivityParticipant,
    Firm\Program\Consultant\RecordOfConsultantActivity,
    Firm\Program\Coordinator\RecordOfActivityInvitation,
    Firm\Program\Coordinator\RecordOfCoordinatorActivity,
    Firm\Program\Participant\RecordOfParticipantActivity,
    Firm\Program\RecordOfActivity,
    Firm\Program\RecordOfActivityType,
    Firm\Program\RecordOfConsultant,
    Firm\Program\RecordOfCoordinator,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfFeedbackForm,
    Firm\RecordOfManager,
    Firm\RecordOfPersonnel,
    Shared\RecordOfForm
};

class InvitationControllerTest extends CoordinatorTestCase
{
    protected $invitationUri;
    protected $managerActivity;
    protected $coordinatorActivity;
    protected $consultantActivity;
    protected $participantActivity;
    protected $clientParticipant;
    protected $invitation_fromManager;
    protected $invitationOne_fromCoordinator;
    protected $invitationTwo_fromConsultant;
    protected $invitationThree_fromParticipant;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationUri = $this->coordinatorUri . "/{$this->coordinator->id}/invitations";
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
        
        
        $activityType = new RecordOfActivityType($program, 0);
        $this->connection->table("ActivityType")->insert($activityType->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $activityParticipant = new RecordOfActivityParticipant($activityType, $feedbackForm, 0);
        $activityParticipantOne = new RecordOfActivityParticipant($activityType, null, 1);
        $activityParticipantTwo = new RecordOfActivityParticipant($activityType, null, 2);
        $activityParticipantThree = new RecordOfActivityParticipant($activityType, null, 3);
        $this->connection->table("ActivityParticipant")->insert($activityParticipant->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantOne->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantTwo->toArrayForDbEntry());
        $this->connection->table("ActivityParticipant")->insert($activityParticipantThree->toArrayForDbEntry());
        
        $activity = new RecordOfActivity($program, $activityType, 0);
        $activityOne = new RecordOfActivity($program, $activityType, 1);
        $activityTwo = new RecordOfActivity($program, $activityType, 2);
        $activityThree = new RecordOfActivity($program, $activityType, 3);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityOne->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityTwo->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityThree->toArrayForDbEntry());
        
        $manager = new RecordOfManager($firm, 0, "manager@email.org", "Password123");
        $this->connection->table("Manager")->insert($manager->toArrayForDbEntry());
        
        $personnel = new RecordOfPersonnel($firm, 0);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        
        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        
        $this->managerActivity = new RecordOfManagerActivity($manager, $activity);
        $this->connection->table("ManagerActivity")->insert($this->managerActivity->toArrayForDbEntry());
        
        $this->coordinatorActivity = new RecordOfCoordinatorActivity($coordinator, $activityOne);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivity->toArrayForDbEntry());
        
        $this->consultantActivity = new RecordOfConsultantActivity($consultant, $activityTwo);
        $this->connection->table("ConsultantActivity")->insert($this->consultantActivity->toArrayForDbEntry());
        
        $this->participantActivity = new RecordOfParticipantActivity($participant, $activityThree);
        $this->connection->table("ParticipantActivity")->insert($this->participantActivity->toArrayForDbEntry());
        
        
        $invitation = new RecordOfInvitee($activity, $activityParticipant, 0);
        $invitationOne = new RecordOfInvitee($activityOne, $activityParticipantOne, 1);
        $invitationTwo = new RecordOfInvitee($activityTwo, $activityParticipantTwo, 2);
        $invitationThree = new RecordOfInvitee($activityThree, $activityParticipantThree, 3);
        $this->connection->table("Invitee")->insert($invitation->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationOne->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationTwo->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationThree->toArrayForDbEntry());
        
        $this->invitation_fromManager = new RecordOfActivityInvitation($this->coordinator, $invitation);
        $this->invitationOne_fromCoordinator = new RecordOfActivityInvitation($this->coordinator, $invitationOne);
        $this->invitationTwo_fromConsultant = new RecordOfActivityInvitation($this->coordinator, $invitationTwo);
        $this->invitationThree_fromParticipant = new RecordOfActivityInvitation($this->coordinator, $invitationThree);
        $this->connection->table("CoordinatorInvitee")->insert($this->invitation_fromManager->toArrayForDbEntry());
        $this->connection->table("CoordinatorInvitee")->insert($this->invitationOne_fromCoordinator->toArrayForDbEntry());
        $this->connection->table("CoordinatorInvitee")->insert($this->invitationTwo_fromConsultant->toArrayForDbEntry());
        $this->connection->table("CoordinatorInvitee")->insert($this->invitationThree_fromParticipant->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("CoordinatorInvitee")->truncate();
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->invitation_fromManager->id,
            "willAttend" => $this->invitation_fromManager->invitee->willAttend,
            "attended" => $this->invitation_fromManager->invitee->attended,
            "activityParticipant" => [
                "id" => $this->invitation_fromManager->invitee->activityParticipant->id,
                "reportForm" => [
                    "id" => $this->invitation_fromManager->invitee->activityParticipant->feedbackForm->id,
                    "name" => $this->invitation_fromManager->invitee->activityParticipant->feedbackForm->form->name,
                    "description" => $this->invitation_fromManager->invitee->activityParticipant->feedbackForm->form->description,
                    "stringFields" => [],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "activity" => [
                "id" => $this->invitation_fromManager->invitee->activity->id,
                "name" => $this->invitation_fromManager->invitee->activity->name,
                "description" => $this->invitation_fromManager->invitee->activity->description,
                "location" => $this->invitation_fromManager->invitee->activity->location,
                "note" => $this->invitation_fromManager->invitee->activity->note,
                "startTime" => $this->invitation_fromManager->invitee->activity->startDateTime,
                "endTime" => $this->invitation_fromManager->invitee->activity->endDateTime,
                "cancelled" => $this->invitation_fromManager->invitee->activity->cancelled,
                "program" => [
                    "id" => $this->invitation_fromManager->invitee->activity->program->id,
                    "name" => $this->invitation_fromManager->invitee->activity->program->name,
                ],
                "activityType" => [
                    "id" => $this->invitation_fromManager->invitee->activity->activityType->id,
                    "name" => $this->invitation_fromManager->invitee->activity->activityType->name,
                ],
                "manager" => [
                    "id" => $this->managerActivity->manager->id,
                    "name" => $this->managerActivity->manager->name,
                ],
                "coordinator" => null,
                "consultant" => null,
                "participant" => null,
            ],
        ];
        
        $uri = $this->invitationUri . "/{$this->invitation_fromManager->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->invitation_fromManager->id,
                    "willAttend" => $this->invitation_fromManager->invitee->willAttend,
                    "attended" => $this->invitation_fromManager->invitee->attended,
                    "activity" => [
                        "id" => $this->invitation_fromManager->invitee->activity->id,
                        "name" => $this->invitation_fromManager->invitee->activity->name,
                        "location" => $this->invitation_fromManager->invitee->activity->location,
                        "startTime" => $this->invitation_fromManager->invitee->activity->startDateTime,
                        "endTime" => $this->invitation_fromManager->invitee->activity->endDateTime,
                        "cancelled" => $this->invitation_fromManager->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->invitation_fromManager->invitee->activity->program->id,
                            "name" => $this->invitation_fromManager->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->invitationOne_fromCoordinator->id,
                    "willAttend" => $this->invitationOne_fromCoordinator->invitee->willAttend,
                    "attended" => $this->invitationOne_fromCoordinator->invitee->attended,
                    "activity" => [
                        "id" => $this->invitationOne_fromCoordinator->invitee->activity->id,
                        "name" => $this->invitationOne_fromCoordinator->invitee->activity->name,
                        "location" => $this->invitationOne_fromCoordinator->invitee->activity->location,
                        "startTime" => $this->invitationOne_fromCoordinator->invitee->activity->startDateTime,
                        "endTime" => $this->invitationOne_fromCoordinator->invitee->activity->endDateTime,
                        "cancelled" => $this->invitationOne_fromCoordinator->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->invitationOne_fromCoordinator->invitee->activity->program->id,
                            "name" => $this->invitationOne_fromCoordinator->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->invitationTwo_fromConsultant->id,
                    "willAttend" => $this->invitationTwo_fromConsultant->invitee->willAttend,
                    "attended" => $this->invitationTwo_fromConsultant->invitee->attended,
                    "activity" => [
                        "id" => $this->invitationTwo_fromConsultant->invitee->activity->id,
                        "name" => $this->invitationTwo_fromConsultant->invitee->activity->name,
                        "location" => $this->invitationTwo_fromConsultant->invitee->activity->location,
                        "startTime" => $this->invitationTwo_fromConsultant->invitee->activity->startDateTime,
                        "endTime" => $this->invitationTwo_fromConsultant->invitee->activity->endDateTime,
                        "cancelled" => $this->invitationTwo_fromConsultant->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->invitationTwo_fromConsultant->invitee->activity->program->id,
                            "name" => $this->invitationTwo_fromConsultant->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->invitationThree_fromParticipant->id,
                    "willAttend" => $this->invitationThree_fromParticipant->invitee->willAttend,
                    "attended" => $this->invitationThree_fromParticipant->invitee->attended,
                    "activity" => [
                        "id" => $this->invitationThree_fromParticipant->invitee->activity->id,
                        "name" => $this->invitationThree_fromParticipant->invitee->activity->name,
                        "location" => $this->invitationThree_fromParticipant->invitee->activity->location,
                        "startTime" => $this->invitationThree_fromParticipant->invitee->activity->startDateTime,
                        "endTime" => $this->invitationThree_fromParticipant->invitee->activity->endDateTime,
                        "cancelled" => $this->invitationThree_fromParticipant->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->invitationThree_fromParticipant->invitee->activity->program->id,
                            "name" => $this->invitationThree_fromParticipant->invitee->activity->program->name,
                        ],
                    ],
                ],
            ],
        ];
        
        $this->get($this->invitationUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
