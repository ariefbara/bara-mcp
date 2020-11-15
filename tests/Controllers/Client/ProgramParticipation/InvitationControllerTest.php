<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\ {
    Client\ProgramParticipationTestCase,
    RecordPreparation\Firm\Client\RecordOfClientParticipant,
    RecordPreparation\Firm\Manager\RecordOfManagerActivity,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\Consultant\RecordOfConsultantActivity,
    RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorActivity,
    RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\Participant\RecordOfParticipantActivity,
    RecordPreparation\Firm\Program\Participant\RecordOfParticipantInvitation,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfCoordinator,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfClient,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\Firm\RecordOfManager,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Shared\RecordOfForm
};

class InvitationControllerTest extends ProgramParticipationTestCase
{

    protected $invitationUri;
    protected $participantActivity;
    protected $managerActivity;
    protected $coordinatorActivity;
    protected $consultantActivity;
    protected $clientParticipant;
    protected $participantInvitation_fromParticipant;
    protected $participantInvitationOne_fromManager;
    protected $participantInvitationTwo_fromCoordinator;
    protected $participantInvitationThree_fromConsultant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inviteeUri = $this->programParticipationUri . "/{$this->programParticipation->id}/invitations";

        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();

        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;

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

        $personnel = new RecordOfPersonnel($firm, 0);
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());

        $client = new RecordOfClient($firm, 0);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());

        $manager = new RecordOfManager($firm, 0, "manager@email.org", "Password123");
        $this->connection->table("Manager")->insert($manager->toArrayForDbEntry());

        $participantOne = new RecordOfParticipant($program, 1);
        $this->connection->table("Participant")->insert($participantOne->toArrayForDbEntry());

        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());

        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());

        $this->clientParticipant = new RecordOfClientParticipant($client, $participantOne);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());

        $this->participantActivity = new RecordOfParticipantActivity($participantOne, $activity);
        $this->connection->table("ParticipantActivity")->insert($this->participantActivity->toArrayForDbEntry());

        $this->managerActivity = new RecordOfManagerActivity($manager, $activityOne);
        $this->connection->table("ManagerActivity")->insert($this->managerActivity->toArrayForDbEntry());

        $this->coordinatorActivity = new RecordOfCoordinatorActivity($coordinator, $activityTwo);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivity->toArrayForDbEntry());

        $this->consultantActivity = new RecordOfConsultantActivity($consultant, $activityThree);
        $this->connection->table("ConsultantActivity")->insert($this->consultantActivity->toArrayForDbEntry());


        $invitation = new RecordOfInvitee($activity, $activityParticipant, 0);
        $invitationOne = new RecordOfInvitee($activityOne, $activityParticipantOne, 1);
        $invitationTwo = new RecordOfInvitee($activityTwo, $activityParticipantTwo, 2);
        $invitationThree = new RecordOfInvitee($activityThree, $activityParticipantThree, 3);
        $this->connection->table("Invitee")->insert($invitation->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationOne->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationTwo->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationThree->toArrayForDbEntry());

        $this->participantInvitation_fromParticipant = new RecordOfActivityInvitation($participant, $invitation);
        $this->participantInvitationOne_fromManager = new RecordOfActivityInvitation($participant, $invitationOne);
        $this->participantInvitationTwo_fromCoordinator = new RecordOfActivityInvitation($participant, $invitationTwo);
        $this->participantInvitationThree_fromConsultant = new RecordOfActivityInvitation($participant, $invitationThree);
        $this->connection->table("ParticipantInvitee")->insert($this->participantInvitation_fromParticipant->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->participantInvitationOne_fromManager->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->participantInvitationTwo_fromCoordinator->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->participantInvitationThree_fromConsultant->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }

    public function test_show_200()
    {
        $response = [
            "id" => $this->participantInvitation_fromParticipant->id,
            "willAttend" => $this->participantInvitation_fromParticipant->invitee->willAttend,
            "attended" => $this->participantInvitation_fromParticipant->invitee->attended,
            "activityParticipant" => [
                "id" => $this->participantInvitation_fromParticipant->invitee->activityParticipant->id,
                "reportForm" => [
                    "id" => $this->participantInvitation_fromParticipant->invitee->activityParticipant->feedbackForm->id,
                    "name" => $this->participantInvitation_fromParticipant->invitee->activityParticipant->feedbackForm->form->name,
                    "description" => $this->participantInvitation_fromParticipant->invitee->activityParticipant->feedbackForm->form->description,
                    "stringFields" => [],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "activity" => [
                "id" => $this->participantInvitation_fromParticipant->invitee->activity->id,
                "name" => $this->participantInvitation_fromParticipant->invitee->activity->name,
                "description" => $this->participantInvitation_fromParticipant->invitee->activity->description,
                "location" => $this->participantInvitation_fromParticipant->invitee->activity->location,
                "note" => $this->participantInvitation_fromParticipant->invitee->activity->note,
                "startTime" => $this->participantInvitation_fromParticipant->invitee->activity->startDateTime,
                "endTime" => $this->participantInvitation_fromParticipant->invitee->activity->endDateTime,
                "cancelled" => $this->participantInvitation_fromParticipant->invitee->activity->cancelled,
                "program" => [
                    "id" => $this->participantInvitation_fromParticipant->invitee->activity->program->id,
                    "name" => $this->participantInvitation_fromParticipant->invitee->activity->program->name,
                ],
                "activityType" => [
                    "id" => $this->participantInvitation_fromParticipant->invitee->activity->activityType->id,
                    "name" => $this->participantInvitation_fromParticipant->invitee->activity->activityType->name,
                ],
                "participant" => [
                    "id" => $this->participantActivity->participant->id,
                    "client" => [
                        "id" => $this->clientParticipant->client->id,
                        "name" => $this->clientParticipant->client->getFullName(),
                    ],
                    "user" => null,
                    "team" => null,
                ],
                "manager" => null,
                "coordinator" => null,
                "consultant" => null,
            ],
        ];

        $uri = $this->inviteeUri . "/{$this->participantInvitation_fromParticipant->id}";
        $this->get($uri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->participantInvitation_fromParticipant->id,
                    "willAttend" => $this->participantInvitation_fromParticipant->invitee->willAttend,
                    "attended" => $this->participantInvitation_fromParticipant->invitee->attended,
                    "activity" => [
                        "id" => $this->participantInvitation_fromParticipant->invitee->activity->id,
                        "name" => $this->participantInvitation_fromParticipant->invitee->activity->name,
                        "location" => $this->participantInvitation_fromParticipant->invitee->activity->location,
                        "startTime" => $this->participantInvitation_fromParticipant->invitee->activity->startDateTime,
                        "endTime" => $this->participantInvitation_fromParticipant->invitee->activity->endDateTime,
                        "cancelled" => $this->participantInvitation_fromParticipant->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->participantInvitation_fromParticipant->invitee->activity->program->id,
                            "name" => $this->participantInvitation_fromParticipant->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->participantInvitationOne_fromManager->id,
                    "willAttend" => $this->participantInvitationOne_fromManager->invitee->willAttend,
                    "attended" => $this->participantInvitationOne_fromManager->invitee->attended,
                    "activity" => [
                        "id" => $this->participantInvitationOne_fromManager->invitee->activity->id,
                        "name" => $this->participantInvitationOne_fromManager->invitee->activity->name,
                        "location" => $this->participantInvitationOne_fromManager->invitee->activity->location,
                        "startTime" => $this->participantInvitationOne_fromManager->invitee->activity->startDateTime,
                        "endTime" => $this->participantInvitationOne_fromManager->invitee->activity->endDateTime,
                        "cancelled" => $this->participantInvitationOne_fromManager->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->participantInvitationOne_fromManager->invitee->activity->program->id,
                            "name" => $this->participantInvitationOne_fromManager->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->participantInvitationTwo_fromCoordinator->id,
                    "willAttend" => $this->participantInvitationTwo_fromCoordinator->invitee->willAttend,
                    "attended" => $this->participantInvitationTwo_fromCoordinator->invitee->attended,
                    "activity" => [
                        "id" => $this->participantInvitationTwo_fromCoordinator->invitee->activity->id,
                        "name" => $this->participantInvitationTwo_fromCoordinator->invitee->activity->name,
                        "location" => $this->participantInvitationTwo_fromCoordinator->invitee->activity->location,
                        "startTime" => $this->participantInvitationTwo_fromCoordinator->invitee->activity->startDateTime,
                        "endTime" => $this->participantInvitationTwo_fromCoordinator->invitee->activity->endDateTime,
                        "cancelled" => $this->participantInvitationTwo_fromCoordinator->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->participantInvitationTwo_fromCoordinator->invitee->activity->program->id,
                            "name" => $this->participantInvitationTwo_fromCoordinator->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->participantInvitationThree_fromConsultant->id,
                    "willAttend" => $this->participantInvitationThree_fromConsultant->invitee->willAttend,
                    "attended" => $this->participantInvitationThree_fromConsultant->invitee->attended,
                    "activity" => [
                        "id" => $this->participantInvitationThree_fromConsultant->invitee->activity->id,
                        "name" => $this->participantInvitationThree_fromConsultant->invitee->activity->name,
                        "location" => $this->participantInvitationThree_fromConsultant->invitee->activity->location,
                        "startTime" => $this->participantInvitationThree_fromConsultant->invitee->activity->startDateTime,
                        "endTime" => $this->participantInvitationThree_fromConsultant->invitee->activity->endDateTime,
                        "cancelled" => $this->participantInvitationThree_fromConsultant->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->participantInvitationThree_fromConsultant->invitee->activity->program->id,
                            "name" => $this->participantInvitationThree_fromConsultant->invitee->activity->program->name,
                        ],
                    ],
                ],
            ],
        ];

        $this->get($this->inviteeUri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

}
