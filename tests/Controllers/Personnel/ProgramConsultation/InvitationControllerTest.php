<?php

namespace Tests\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\ProgramConsultation\ProgramConsultationBaseController;
use DateTimeImmutable;
use Tests\Controllers\RecordPreparation\ {
    Firm\Program\Activity\RecordOfInvitee,
    Firm\Program\ActivityType\RecordOfActivityParticipant,
    Firm\Program\Consultant\RecordOfActivityInvitation,
    Firm\Program\RecordOfActivity,
    Firm\Program\RecordOfActivityType,
    Firm\RecordOfFeedbackForm,
    Shared\RecordOfForm
};

class InvitationControllerTest extends ProgramConsultationTestCase
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
        $this->invitationUri = $this->programConsultationUri . "/invitations";
        
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ActivityParticipant")->truncate();
        $this->connection->table("Activity")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("InviteeReport")->truncate();
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
        
        $activity = new RecordOfActivity($activityType, 0);
        $activity->startDateTime = (new \DateTimeImmutable("+72 hours"))->format("Y-m-d H:i:s");
        $activity->endDateTime = (new \DateTimeImmutable("+73 hours"))->format("Y-m-d H:i:s");
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
        
        $this->invitation = new RecordOfActivityInvitation($this->programConsultation, $invitation);
        $this->invitationOne = new RecordOfActivityInvitation($this->programConsultation, $invitationOne);
        $this->invitationTwo = new RecordOfActivityInvitation($this->programConsultation, $invitationTwo);
        $this->invitationThree = new RecordOfActivityInvitation($this->programConsultation, $invitationThree);
        $this->connection->table("ConsultantInvitee")->insert($this->invitation->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitee")->insert($this->invitationOne->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitee")->insert($this->invitationTwo->toArrayForDbEntry());
        $this->connection->table("ConsultantInvitee")->insert($this->invitationThree->toArrayForDbEntry());
        
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
        $this->connection->table("ConsultantInvitee")->truncate();
    }
    
    public function test_submitReport_200()
    {
        $response = [
            "id" => $this->invitation->id,
            "willAttend" => $this->invitation->invitee->willAttend,
            "attended" => $this->invitation->invitee->attended,
            "anInitiator" => $this->invitation->invitee->anInitiator,
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
        $this->put($uri, $this->submitReportInput, $this->programConsultation->personnel->token)
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
            "anInitiator" => $this->invitation->invitee->anInitiator,
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
                    "id" => $this->invitation->id,
                    "willAttend" => $this->invitation->invitee->willAttend,
                    "attended" => $this->invitation->invitee->attended,
                    "anInitiator" => $this->invitation->invitee->anInitiator,
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
                    "anInitiator" => $this->invitationOne->invitee->anInitiator,
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
                    "anInitiator" => $this->invitationTwo->invitee->anInitiator,
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
                    "anInitiator" => $this->invitationThree->invitee->anInitiator,
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
        
        $this->get($this->invitationUri, $this->programConsultation->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_timeIntervalFilterSet()
    {
        $uri = $this->invitationUri
                . "?from=" . (new \DateTimeImmutable("+70 hours"))->format("Y-m-d H:i:s")
                . "&to=" . (new \DateTimeImmutable("+75 hours"))->format("Y-m-d H:i:s");
        $totalResponse = ["total" => 1];
        $listResponse = [
            "id" => $this->invitation->id,
        ];
        
        $this->get($uri, $this->programConsultation->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($listResponse)
                ->seeStatusCode(200);
                
    }
}
