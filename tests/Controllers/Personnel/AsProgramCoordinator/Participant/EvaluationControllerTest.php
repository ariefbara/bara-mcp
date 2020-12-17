<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator\Participant;

use DateTimeImmutable;
use Tests\Controllers\Personnel\AsProgramCoordinator\ParticipantTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Activity\RecordOfInvitee;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfActivityInvitation;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfEvaluation;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfEvaluationPlan;

class EvaluationControllerTest extends ParticipantTestCase
{
    protected $evaluationUri;
    protected $evaluationPlan;
    protected $evaluationOne;
    protected $evaluationTwo;
    protected $evaluateInput;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->evaluationUri = $this->participantUri . "/{$this->participant->id}/evaluations";
        
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("Evaluation")->truncate();
        
        $program = $this->coordinator->program;
        
        $this->evaluationPlan = new RecordOfEvaluationPlan($program, null, 99);
        $evaluationPlanOne = new RecordOfEvaluationPlan($program, null, 1);
        $evaluationPlanTwo = new RecordOfEvaluationPlan($program, null, 2);
        $this->connection->table("EvaluationPlan")->insert($this->evaluationPlan->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($evaluationPlanOne->toArrayForDbEntry());
        $this->connection->table("EvaluationPlan")->insert($evaluationPlanTwo->toArrayForDbEntry());
        
        $this->evaluationOne = new RecordOfEvaluation($this->participant, $evaluationPlanOne, $this->coordinator, 1);
        $this->evaluationTwo = new RecordOfEvaluation($this->participant, $evaluationPlanTwo, $this->coordinator, 2);
        $this->connection->table("Evaluation")->insert($this->evaluationOne->toArrayForDbEntry());
        $this->connection->table("Evaluation")->insert($this->evaluationTwo->toArrayForDbEntry());
        
        $this->evaluateInput = [
            "evaluationPlanId" => $this->evaluationPlan->id,
            "status" => "pass",
            "extendDays" => null,
        ];
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("EvaluationPlan")->truncate();
        $this->connection->table("Evaluation")->truncate();
    }
    
    public function test_evaluate_pass_200()
    {
        $uri = $this->evaluationUri;
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
        $evaluationEntry = [
            "Participant_id" => $this->participant->id,
            "c_status" => $this->evaluateInput["status"],
            "extendDays" => null,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }
    public function test_evaluate_alreadyReceiveCompletedEvaluationForSamePlan_403()
    {
        $this->evaluateInput["evaluationPlanId"] = $this->evaluationOne->evaluationPlan->id;
        $uri = $this->evaluationUri;
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeStatusCode(403);
    }
    public function test_evaluate_fail_disableParticipant_200()
    {
$this->disableExceptionHandling();
        $this->evaluateInput["status"] = "fail";
        
        $uri = $this->evaluationUri;
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
        $participantEntry = [
            "id" => $this->participant->id,
            "active" => false,
        ];
        $this->seeInDatabase("Participant", $participantEntry);
        
        $evaluationEntry = [
            "Participant_id" => $this->participant->id,
            "c_status" => $this->evaluateInput["status"],
            "extendDays" => null,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }
    public function test_evaluate_fail_disableMeetingInvitation()
    {
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
        
        $meetingInvitation = new RecordOfInvitee(null, null, 0);
        $meetingInvitation->persistSelf($this->connection);
        $meetingInvitation->activity->persistSelf($this->connection);
        $meetingInvitation->activity->activityType->program = $this->coordinator->program;
        $meetingInvitation->activity->activityType->persistSelf($this->connection);
        $participantMeetingInvitation = new RecordOfActivityInvitation($this->participant, $meetingInvitation);
        $this->connection->table("ParticipantInvitee")->insert($participantMeetingInvitation->toArrayForDbEntry());
        
        $uri = $this->evaluationUri;
        $this->evaluateInput["status"] = "fail";
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
        $invitationEntry = [
            "id" => $meetingInvitation->id,
            "cancelled" => true,
        ];
        $this->seeInDatabase("Invitee", $invitationEntry);
        
        $this->connection->table("Activity")->truncate();
        $this->connection->table("ActivityType")->truncate();
        $this->connection->table("Invitee")->truncate();
        $this->connection->table("ParticipantInvitee")->truncate();
    }
    public function test_evaluate_fail_cancelUpcomingRequest()
    {
        $this->connection->table("ConsultationRequest")->truncate();
        $consultationRequest = new RecordOfConsultationRequest(null, $this->participant, null, 0);
        $consultationRequest->startDateTime = (new \DateTimeImmutable("+24 hours"))->format("Y-m-d H:i:s");
        $consultationRequest->endDateTime = (new \DateTimeImmutable("+25 hours"))->format("Y-m-d H:i:s");
        $this->connection->table("ConsultationRequest")->insert($consultationRequest->toArrayForDbEntry());
        
        $uri = $this->evaluationUri;
        $this->evaluateInput["status"] = "fail";
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
        $consultationRequestEntry = [
            "id" => $consultationRequest->id,
            "concluded" => true,
            "status" => "disabled by system",
        ];
        $this->seeInDatabase("ConsultationRequest", $consultationRequestEntry);
        
        $this->connection->table("ConsultationRequest")->truncate();
    }
    public function test_evaluate_fail_cancelUpcomingSession()
    {
        $this->connection->table("ConsultationSession")->truncate();
        $consultationSession = new RecordOfConsultationSession(null, $this->participant, null, 0);
        $consultationSession->startDateTime = (new \DateTimeImmutable("+24 hours"))->format("Y-m-d H:i:s");
        $consultationSession->endDateTime = (new \DateTimeImmutable("+25 hours"))->format("Y-m-d H:i:s");
        $this->connection->table("ConsultationSession")->insert($consultationSession->toArrayForDbEntry());
        
        $uri = $this->evaluationUri;
        $this->evaluateInput["status"] = "fail";
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
        $consultationSessionEntry = [
            "id" => $consultationSession->id,
            "cancelled" => true,
            "note" => "disabled by system",
        ];
        $this->seeInDatabase("ConsultationSession", $consultationSessionEntry);
        $this->connection->table("ConsultationSession")->truncate();
    }
    public function test_evaluate_extend_200()
    {
        $this->evaluateInput["status"] = "extend";
        $this->evaluateInput["extendDays"] = 99;
        
        $uri = $this->evaluationUri;
        $this->post($uri, $this->evaluateInput, $this->coordinator->personnel->token)
                ->seeStatusCode(200);
        
        $participantEntry = [
            "id" => $this->participant->id,
            "active" => true,
        ];
        $this->seeInDatabase("Participant", $participantEntry);
        
        $evaluationEntry = [
            "Participant_id" => $this->participant->id,
            "c_status" => $this->evaluateInput["status"],
            "extendDays" => 99,
            "submitTime" => (new DateTimeImmutable())->format("Y-m-d H:i:s"),
            "Coordinator_id" => $this->coordinator->id,
            "EvaluationPlan_id" => $this->evaluationPlan->id,
        ];
        $this->seeInDatabase("Evaluation", $evaluationEntry);
    }
    
    public function test_show_200()
    {
        $response = [
            "id" => $this->evaluationOne->id,
            "submitTime" => $this->evaluationOne->submitTime,
            "status" => $this->evaluationOne->status,
            "extendDays" => $this->evaluationOne->extendDays,
            "evaluationPlan" => [
                "id" => $this->evaluationOne->evaluationPlan->id,
                "name" => $this->evaluationOne->evaluationPlan->name,
            ],
            "coordinator" => [
                "id" => $this->evaluationOne->coordinator->id,
                "name" => $this->evaluationOne->coordinator->personnel->getFullName(),
            ],
        ];
        $uri = $this->evaluationUri . "/{$this->evaluationOne->id}";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->evaluationOne->id,
                    "submitTime" => $this->evaluationOne->submitTime,
                    "status" => $this->evaluationOne->status,
                    "extendDays" => $this->evaluationOne->extendDays,
                    "evaluationPlan" => [
                        "id" => $this->evaluationOne->evaluationPlan->id,
                        "name" => $this->evaluationOne->evaluationPlan->name,
                    ],
                    "coordinator" => [
                        "id" => $this->evaluationOne->coordinator->id,
                        "name" => $this->evaluationOne->coordinator->personnel->getFullName(),
                    ],
                ],
                [
                    "id" => $this->evaluationTwo->id,
                    "submitTime" => $this->evaluationTwo->submitTime,
                    "status" => $this->evaluationTwo->status,
                    "extendDays" => $this->evaluationTwo->extendDays,
                    "evaluationPlan" => [
                        "id" => $this->evaluationTwo->evaluationPlan->id,
                        "name" => $this->evaluationTwo->evaluationPlan->name,
                    ],
                    "coordinator" => [
                        "id" => $this->evaluationTwo->coordinator->id,
                        "name" => $this->evaluationTwo->coordinator->personnel->getFullName(),
                    ],
                ],
            ],
        ];
        $this->get($this->evaluationUri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
 