<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTimeImmutable;
use Tests\Controllers\ {
    Client\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Activity\RecordOfInvitee,
    RecordPreparation\Firm\Program\ActivityType\RecordOfActivityParticipant,
    RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation,
    RecordPreparation\Firm\Program\Participant\RecordOfParticipantActivity,
    RecordPreparation\Firm\Program\RecordOfActivity,
    RecordPreparation\Firm\Program\RecordOfActivityType,
    RecordPreparation\Firm\Program\RecordOfParticipant,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\Shared\RecordOfForm
};

class InvitationControllerTest extends ProgramParticipationTestCase
{
    protected $invitationUri;
    protected $clientParticipant;
    protected $invitation;
    protected $invitationOne;
    protected $invitationTwo;
    protected $invitationThree;
    protected $submitReportInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invitationUri = $this->programParticipationUri . "/{$this->programParticipation->id}/invitations";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("InviteeReport")->truncate();
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
        
        $activity = new RecordOfActivity($activityType, 0);
        $activityOne = new RecordOfActivity($activityType, 1);
        $activityTwo = new RecordOfActivity($activityType, 2);
        $activityThree = new RecordOfActivity($activityType, 3);
        $this->connection->table("Activity")->insert($activity->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityOne->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityTwo->toArrayForDbEntry());
        $this->connection->table("Activity")->insert($activityThree->toArrayForDbEntry());
        
        $invitation = new RecordOfInvitee($activity, $activityParticipant, 0);
        $invitationOne = new RecordOfInvitee($activityOne, $activityParticipantOne, 1);
        $invitationTwo = new RecordOfInvitee($activityTwo, $activityParticipantTwo, 2);
        $invitationThree = new RecordOfInvitee($activityThree, $activityParticipantThree, 3);
        $this->connection->table("Invitee")->insert($invitation->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationOne->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationTwo->toArrayForDbEntry());
        $this->connection->table("Invitee")->insert($invitationThree->toArrayForDbEntry());
        
        $this->invitation = new RecordOfActivityInvitation($participant, $invitation);
        $this->invitationOne = new RecordOfActivityInvitation($participant, $invitationOne);
        $this->invitationTwo = new RecordOfActivityInvitation($participant, $invitationTwo);
        $this->invitationThree = new RecordOfActivityInvitation($participant, $invitationThree);
        $this->connection->table("ParticipantInvitee")->insert($this->invitation->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->invitationOne->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->invitationTwo->toArrayForDbEntry());
        $this->connection->table("ParticipantInvitee")->insert($this->invitationThree->toArrayForDbEntry());
        
        $this->submitReportInput = [
            "stringFieldRecords" => [],
            "integerFieldRecords" => [],
            "textAreaFieldRecords" => [],
            "attachmentFieldRecords" => [],
            "singleSelectFieldRecords" => [],
            "multiSelectFieldRecords" => [],
        ];
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("InviteeReport")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
    
    public function test_submitReport_200()
    {
        $response = [
            "id" => $this->invitation->id,
            "willAttend" => $this->invitation->invitee->willAttend,
            "attended" => $this->invitation->invitee->attended,
            "report" => [
                "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
                "stringFieldRecords" => [],
                "integerFieldRecords" => [],
                "textAreaFieldRecords" => [],
                "attachmentFieldRecords" => [],
                "singleSelectFieldRecords" => [],
                "multiSelectFieldRecords" => [],
            ],
            "activityParticipant" => [
                "id" => $this->invitation->invitee->activityParticipant->id,
                "reportForm" => [
                    "id" => $this->invitation->invitee->activityParticipant->feedbackForm->id,
                    "name" => $this->invitation->invitee->activityParticipant->feedbackForm->form->name,
                    "description" => $this->invitation->invitee->activityParticipant->feedbackForm->form->description,
                    "stringFields" => [],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "activity" => [
                "id" => $this->invitation->invitee->activity->id,
                "name" => $this->invitation->invitee->activity->name,
                "description" => $this->invitation->invitee->activity->description,
                "location" => $this->invitation->invitee->activity->location,
                "note" => $this->invitation->invitee->activity->note,
                "startTime" => $this->invitation->invitee->activity->startDateTime,
                "endTime" => $this->invitation->invitee->activity->endDateTime,
                "cancelled" => $this->invitation->invitee->activity->cancelled,
                "activityType" => [
                    "id" => $this->invitation->invitee->activity->activityType->id,
                    "name" => $this->invitation->invitee->activity->activityType->name,
                    "program" => [
                        "id" => $this->invitation->invitee->activity->activityType->program->id,
                        "name" => $this->invitation->invitee->activity->activityType->program->name,
                    ],
                ],
            ],
        ];
        
        $uri = $this->invitationUri . "/{$this->invitation->id}";
        $this->put($uri, $this->submitReportInput, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
        
        $inviteeReportEntry = [
            "Invitee_id" => $this->invitation->id,
        ];
        $this->seeInDatabase("InviteeReport", $inviteeReportEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->invitation->id,
            "willAttend" => $this->invitation->invitee->willAttend,
            "attended" => $this->invitation->invitee->attended,
            "report" => null,
            "activityParticipant" => [
                "id" => $this->invitation->invitee->activityParticipant->id,
                "reportForm" => [
                    "id" => $this->invitation->invitee->activityParticipant->feedbackForm->id,
                    "name" => $this->invitation->invitee->activityParticipant->feedbackForm->form->name,
                    "description" => $this->invitation->invitee->activityParticipant->feedbackForm->form->description,
                    "stringFields" => [],
                    "integerFields" => [],
                    "textAreaFields" => [],
                    "attachmentFields" => [],
                    "singleSelectFields" => [],
                    "multiSelectFields" => [],
                ],
            ],
            "activity" => [
                "id" => $this->invitation->invitee->activity->id,
                "name" => $this->invitation->invitee->activity->name,
                "description" => $this->invitation->invitee->activity->description,
                "location" => $this->invitation->invitee->activity->location,
                "note" => $this->invitation->invitee->activity->note,
                "startTime" => $this->invitation->invitee->activity->startDateTime,
                "endTime" => $this->invitation->invitee->activity->endDateTime,
                "cancelled" => $this->invitation->invitee->activity->cancelled,
                "activityType" => [
                    "id" => $this->invitation->invitee->activity->activityType->id,
                    "name" => $this->invitation->invitee->activity->activityType->name,
                    "program" => [
                        "id" => $this->invitation->invitee->activity->activityType->program->id,
                        "name" => $this->invitation->invitee->activity->activityType->program->name,
                    ],
                ],
            ],
        ];
        
        $uri = $this->invitationUri . "/{$this->invitation->id}";
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
                    "id" => $this->invitation->id,
                    "willAttend" => $this->invitation->invitee->willAttend,
                    "attended" => $this->invitation->invitee->attended,
                    "activity" => [
                        "id" => $this->invitation->invitee->activity->id,
                        "name" => $this->invitation->invitee->activity->name,
                        "location" => $this->invitation->invitee->activity->location,
                        "startTime" => $this->invitation->invitee->activity->startDateTime,
                        "endTime" => $this->invitation->invitee->activity->endDateTime,
                        "cancelled" => $this->invitation->invitee->activity->cancelled,
                        "activityType" => [
                            "id" => $this->invitation->invitee->activity->activityType->id,
                            "name" => $this->invitation->invitee->activity->activityType->name,
                            "program" => [
                                "id" => $this->invitation->invitee->activity->activityType->program->id,
                                "name" => $this->invitation->invitee->activity->activityType->program->name,
                            ],
                        ],
                    ],
                ],
                [
                    "id" => $this->invitationOne->id,
                    "willAttend" => $this->invitationOne->invitee->willAttend,
                    "attended" => $this->invitationOne->invitee->attended,
                    "activity" => [
                        "id" => $this->invitationOne->invitee->activity->id,
                        "name" => $this->invitationOne->invitee->activity->name,
                        "location" => $this->invitationOne->invitee->activity->location,
                        "startTime" => $this->invitationOne->invitee->activity->startDateTime,
                        "endTime" => $this->invitationOne->invitee->activity->endDateTime,
                        "cancelled" => $this->invitationOne->invitee->activity->cancelled,
                        "activityType" => [
                            "id" => $this->invitationOne->invitee->activity->activityType->id,
                            "name" => $this->invitationOne->invitee->activity->activityType->name,
                            "program" => [
                                "id" => $this->invitationOne->invitee->activity->activityType->program->id,
                                "name" => $this->invitationOne->invitee->activity->activityType->program->name,
                            ],
                        ],
                    ],
                ],
                [
                    "id" => $this->invitationTwo->id,
                    "willAttend" => $this->invitationTwo->invitee->willAttend,
                    "attended" => $this->invitationTwo->invitee->attended,
                    "activity" => [
                        "id" => $this->invitationTwo->invitee->activity->id,
                        "name" => $this->invitationTwo->invitee->activity->name,
                        "location" => $this->invitationTwo->invitee->activity->location,
                        "startTime" => $this->invitationTwo->invitee->activity->startDateTime,
                        "endTime" => $this->invitationTwo->invitee->activity->endDateTime,
                        "cancelled" => $this->invitationTwo->invitee->activity->cancelled,
                        "activityType" => [
                            "id" => $this->invitationTwo->invitee->activity->activityType->id,
                            "name" => $this->invitationTwo->invitee->activity->activityType->name,
                            "program" => [
                                "id" => $this->invitationTwo->invitee->activity->activityType->program->id,
                                "name" => $this->invitationTwo->invitee->activity->activityType->program->name,
                            ],
                        ],
                    ],
                ],
                [
                    "id" => $this->invitationThree->id,
                    "willAttend" => $this->invitationThree->invitee->willAttend,
                    "attended" => $this->invitationThree->invitee->attended,
                    "activity" => [
                        "id" => $this->invitationThree->invitee->activity->id,
                        "name" => $this->invitationThree->invitee->activity->name,
                        "location" => $this->invitationThree->invitee->activity->location,
                        "startTime" => $this->invitationThree->invitee->activity->startDateTime,
                        "endTime" => $this->invitationThree->invitee->activity->endDateTime,
                        "cancelled" => $this->invitationThree->invitee->activity->cancelled,
                        "activityType" => [
                            "id" => $this->invitationThree->invitee->activity->activityType->id,
                            "name" => $this->invitationThree->invitee->activity->activityType->name,
                            "program" => [
                                "id" => $this->invitationThree->invitee->activity->activityType->program->id,
                                "name" => $this->invitationThree->invitee->activity->activityType->program->name,
                            ],
                        ],
                    ],
                ],
            ],
        ];
        
        $this->get($this->invitationUri, $this->programParticipation->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
