<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Manager\RecordOfManagerActivity,
    Firm\Program\Activity\RecordOfInvitee,
    Firm\Program\ActivityType\RecordOfActivityParticipant,
    Firm\Program\Consultant\RecordOfActivityInvitation,
    Firm\Program\Consultant\RecordOfConsultantActivity,
    Firm\Program\Consultant\RecordOfConsultantInvitation,
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

class InvitationControllerTest extends ProgramConsultationTestCase
{

    protected $invitationUri;
    protected $consultantActivity;
    protected $managerActivity;
    protected $coordinatorActivity;
    protected $participantActivity;
    protected $clientParticipant;
    protected $consultantInvitation_fromConsultant;
    protected $consultantInvitationOne_fromManager;
    protected $consultantInvitationTwo_fromCoordinator;
    protected $consultantInvitationThree_fromParticipant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inviteeUri = $this->programConsultationUri . "/invitations";

        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();

        $program = $this->programConsultation->program;
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

        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());

        $coordinator = new RecordOfCoordinator($program, $personnel, 0);
        $this->connection->table("Coordinator")->insert($coordinator->toArrayForDbEntry());

        $participant = new RecordOfParticipant($program, 0);
        $this->connection->table("Participant")->insert($participant->toArrayForDbEntry());

        $this->clientParticipant = new RecordOfClientParticipant($client, $participant);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());

        $this->consultantActivity = new RecordOfConsultantActivity($consultant, $activity);
        $this->connection->table("ConsultantActivity")->insert($this->consultantActivity->toArrayForDbEntry());

        $this->managerActivity = new RecordOfManagerActivity($manager, $activityOne);
        $this->connection->table("ManagerActivity")->insert($this->managerActivity->toArrayForDbEntry());

        $this->coordinatorActivity = new RecordOfCoordinatorActivity($coordinator, $activityTwo);
        $this->connection->table("CoordinatorActivity")->insert($this->coordinatorActivity->toArrayForDbEntry());

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

        $this->consultantInvitation_fromConsultant = new RecordOfActivityInvitation($this->programConsultation, $invitation);
        $this->consultantInvitationOne_fromManager = new RecordOfActivityInvitation($this->programConsultation, $invitationOne);
        $this->consultantInvitationTwo_fromCoordinator = new RecordOfActivityInvitation($this->programConsultation,
                $invitationTwo);
        $this->consultantInvitationThree_fromParticipant = new RecordOfActivityInvitation($this->programConsultation,
                $invitationThree);
        $this->connection->table("ConsultantInvitee")->insert($this->consultantInvitation_fromConsultant->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitee")->insert($this->consultantInvitationOne_fromManager->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitee")->insert($this->consultantInvitationTwo_fromCoordinator->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitee")->insert($this->consultantInvitationThree_fromParticipant->toArrayForDbEntry());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Program")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Client")->truncate();
        $this->connection->table("Manager")->truncate();
        $this->connection->table("Coordinator")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("ConsultantActivity")->truncate();
        $this->connection->table("ManagerActivity")->truncate();
        $this->connection->table("CoordinatorActivity")->truncate();
        $this->connection->table("ParticipantActivity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ConsultantInvitee")->truncate();
    }

    public function test_show_200()
    {
        $response = [
            "id" => $this->consultantInvitation_fromConsultant->id,
            "willAttend" => $this->consultantInvitation_fromConsultant->invitee->willAttend,
            "attended" => $this->consultantInvitation_fromConsultant->invitee->attended,
            "activityParticipant" => [
                "id" => $this->consultantInvitation_fromConsultant->invitee->activityParticipant->id,
                "reportForm" => [
                    "id" => $this->consultantInvitation_fromConsultant->invitee->activityParticipant->feedbackForm->id,
                    "name" => $this->consultantInvitation_fromConsultant->invitee->activityParticipant->feedbackForm->form->name,
                    "description" => $this->consultantInvitation_fromConsultant->invitee->activityParticipant->feedbackForm->form->description,
                    "stringFields" => [],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "activity" => [
                "id" => $this->consultantInvitation_fromConsultant->invitee->activity->id,
                "name" => $this->consultantInvitation_fromConsultant->invitee->activity->name,
                "description" => $this->consultantInvitation_fromConsultant->invitee->activity->description,
                "location" => $this->consultantInvitation_fromConsultant->invitee->activity->location,
                "note" => $this->consultantInvitation_fromConsultant->invitee->activity->note,
                "startTime" => $this->consultantInvitation_fromConsultant->invitee->activity->startDateTime,
                "endTime" => $this->consultantInvitation_fromConsultant->invitee->activity->endDateTime,
                "cancelled" => $this->consultantInvitation_fromConsultant->invitee->activity->cancelled,
                "program" => [
                    "id" => $this->consultantInvitation_fromConsultant->invitee->activity->program->id,
                    "name" => $this->consultantInvitation_fromConsultant->invitee->activity->program->name,
                ],
                "activityType" => [
                    "id" => $this->consultantInvitation_fromConsultant->invitee->activity->activityType->id,
                    "name" => $this->consultantInvitation_fromConsultant->invitee->activity->activityType->name,
                ],
                "consultant" => [
                    "id" => $this->consultantActivity->consultant->id,
                    "personnel" => [
                        "id" => $this->consultantActivity->consultant->personnel->id,
                        "name" => $this->consultantActivity->consultant->personnel->getFullName(),
                    ],
                ],
                "manager" => null,
                "coordinator" => null,
                "participant" => null,
            ],
        ];

        $uri = $this->inviteeUri . "/{$this->consultantInvitation_fromConsultant->id}";
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

    public function test_showAll_200()
    {
        $response = [
            "total" => 4,
            "list" => [
                [
                    "id" => $this->consultantInvitation_fromConsultant->id,
                    "willAttend" => $this->consultantInvitation_fromConsultant->invitee->willAttend,
                    "attended" => $this->consultantInvitation_fromConsultant->invitee->attended,
                    "activity" => [
                        "id" => $this->consultantInvitation_fromConsultant->invitee->activity->id,
                        "name" => $this->consultantInvitation_fromConsultant->invitee->activity->name,
                        "location" => $this->consultantInvitation_fromConsultant->invitee->activity->location,
                        "startTime" => $this->consultantInvitation_fromConsultant->invitee->activity->startDateTime,
                        "endTime" => $this->consultantInvitation_fromConsultant->invitee->activity->endDateTime,
                        "cancelled" => $this->consultantInvitation_fromConsultant->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->consultantInvitation_fromConsultant->invitee->activity->program->id,
                            "name" => $this->consultantInvitation_fromConsultant->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->consultantInvitationOne_fromManager->id,
                    "willAttend" => $this->consultantInvitationOne_fromManager->invitee->willAttend,
                    "attended" => $this->consultantInvitationOne_fromManager->invitee->attended,
                    "activity" => [
                        "id" => $this->consultantInvitationOne_fromManager->invitee->activity->id,
                        "name" => $this->consultantInvitationOne_fromManager->invitee->activity->name,
                        "location" => $this->consultantInvitationOne_fromManager->invitee->activity->location,
                        "startTime" => $this->consultantInvitationOne_fromManager->invitee->activity->startDateTime,
                        "endTime" => $this->consultantInvitationOne_fromManager->invitee->activity->endDateTime,
                        "cancelled" => $this->consultantInvitationOne_fromManager->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->consultantInvitationOne_fromManager->invitee->activity->program->id,
                            "name" => $this->consultantInvitationOne_fromManager->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->consultantInvitationTwo_fromCoordinator->id,
                    "willAttend" => $this->consultantInvitationTwo_fromCoordinator->invitee->willAttend,
                    "attended" => $this->consultantInvitationTwo_fromCoordinator->invitee->attended,
                    "activity" => [
                        "id" => $this->consultantInvitationTwo_fromCoordinator->invitee->activity->id,
                        "name" => $this->consultantInvitationTwo_fromCoordinator->invitee->activity->name,
                        "location" => $this->consultantInvitationTwo_fromCoordinator->invitee->activity->location,
                        "startTime" => $this->consultantInvitationTwo_fromCoordinator->invitee->activity->startDateTime,
                        "endTime" => $this->consultantInvitationTwo_fromCoordinator->invitee->activity->endDateTime,
                        "cancelled" => $this->consultantInvitationTwo_fromCoordinator->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->consultantInvitationTwo_fromCoordinator->invitee->activity->program->id,
                            "name" => $this->consultantInvitationTwo_fromCoordinator->invitee->activity->program->name,
                        ],
                    ],
                ],
                [
                    "id" => $this->consultantInvitationThree_fromParticipant->id,
                    "willAttend" => $this->consultantInvitationThree_fromParticipant->invitee->willAttend,
                    "attended" => $this->consultantInvitationThree_fromParticipant->invitee->attended,
                    "activity" => [
                        "id" => $this->consultantInvitationThree_fromParticipant->invitee->activity->id,
                        "name" => $this->consultantInvitationThree_fromParticipant->invitee->activity->name,
                        "location" => $this->consultantInvitationThree_fromParticipant->invitee->activity->location,
                        "startTime" => $this->consultantInvitationThree_fromParticipant->invitee->activity->startDateTime,
                        "endTime" => $this->consultantInvitationThree_fromParticipant->invitee->activity->endDateTime,
                        "cancelled" => $this->consultantInvitationThree_fromParticipant->invitee->activity->cancelled,
                        "program" => [
                            "id" => $this->consultantInvitationThree_fromParticipant->invitee->activity->program->id,
                            "name" => $this->consultantInvitationThree_fromParticipant->invitee->activity->program->name,
                        ],
                    ],
                ],
            ],
        ];

        $this->get($this->inviteeUri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }

}
