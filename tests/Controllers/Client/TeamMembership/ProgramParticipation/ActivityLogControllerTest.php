<?php

namespace Tests\Controllers\Client\TeamMembership\ProgramParticipation;

use Tests\Controllers\ {
    Client\TeamMembership\ProgramParticipationTestCase,
    RecordPreparation\Firm\Program\Participant\ConsultationRequest\RecordOfConsultationRequestActivityLog,
    RecordPreparation\Firm\Program\Participant\RecordOfConsultationRequest,
    RecordPreparation\Firm\Program\RecordOfConsultant,
    RecordPreparation\Firm\Program\RecordOfConsultationSetup,
    RecordPreparation\Firm\RecordOfFeedbackForm,
    RecordPreparation\Firm\RecordOfPersonnel,
    RecordPreparation\Firm\Team\Member\RecordOfTeamMemberActivityLog,
    RecordPreparation\Shared\RecordOfActivityLog,
    RecordPreparation\Shared\RecordOfForm
};

class ActivityLogControllerTest extends ProgramParticipationTestCase
{
    protected $activityLogUri;
    protected $activityLog;
    protected $activityLogOne;
    protected $consultationRequest;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->activityLogUri = $this->programParticipationUri . "/{$this->programParticipation->id}/activity-logs";
        
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("TeamMemberActivityLog")->truncate();
        $this->connection->table("ConsultationRequestActivityLog")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $firm = $program->firm;
        
        $this->activityLog = new RecordOfActivityLog(0);
        $this->activityLogOne = new RecordOfActivityLog(1);
        $this->connection->table("ActivityLog")->insert($this->activityLog->toArrayForDbEntry());
        $this->connection->table("ActivityLog")->insert($this->activityLogOne->toArrayForDbEntry());
        
        $teamMemberActivityLog = new RecordOfTeamMemberActivityLog($this->teamMembership, $this->activityLog);
        $teamMemberActivityLogOne = new RecordOfTeamMemberActivityLog($this->teamMembership, $this->activityLogOne);
        $this->connection->table("TeamMemberActivityLog")->insert($teamMemberActivityLog->toArrayForDbEntry());
        $this->connection->table("TeamMemberActivityLog")->insert($teamMemberActivityLogOne->toArrayForDbEntry());
        
        
        $personnel = new RecordOfPersonnel($firm, 0, 'personnel@email.org', 'password213');
        $this->connection->table("Personnel")->insert($personnel->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $feedbackForm = new RecordOfFeedbackForm($firm, $form);
        $this->connection->table("FeedbackForm")->insert($feedbackForm->toArrayForDbEntry());
        
        $consultationSetup = new RecordOfConsultationSetup($program, $feedbackForm, $feedbackForm, 0);
        $this->connection->table("ConsultationSetup")->insert($consultationSetup->toArrayForDbEntry());
        
        
        $consultant = new RecordOfConsultant($program, $personnel, 0);
        $this->connection->table("Consultant")->insert($consultant->toArrayForDbEntry());
        
        $this->consultationRequest = new RecordOfConsultationRequest($consultationSetup, $participant, $consultant, 0);
        $this->connection->table("ConsultationRequest")->insert($this->consultationRequest->toArrayForDbEntry());
        
        $consultationRequestActivityLog = new RecordOfConsultationRequestActivityLog($this->consultationRequest, $this->activityLog);
        $consultationRequestActivityLogOne = new RecordOfConsultationRequestActivityLog($this->consultationRequest, $this->activityLogOne);
        $this->connection->table("ConsultationRequestActivityLog")->insert($consultationRequestActivityLog->toArrayForDbEntry());
        $this->connection->table("ConsultationRequestActivityLog")->insert($consultationRequestActivityLogOne->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("ActivityLog")->truncate();
        $this->connection->table("TeamMemberActivityLog")->truncate();
        $this->connection->table("ConsultationRequestActivityLog")->truncate();
        $this->connection->table("Personnel")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("FeedbackForm")->truncate();
        $this->connection->table("ConsultationSetup")->truncate();
        $this->connection->table("Consultant")->truncate();
        $this->connection->table("ConsultationRequest")->truncate();
    }
    
    public function test_showAll_200()
    {
        $response = [
            "total" => 2,
            "list" => [
                [
                    "id" => $this->activityLog->id,
                    "message" => $this->activityLog->message,
                    "occuredTime" => $this->activityLog->occuredTime,
                    "teamMember" => [
                        "id" => $this->teamMembership->id,
                        "client" => [
                            "id" => $this->teamMembership->client->id,
                            "name" => $this->teamMembership->client->getFullName(),
                        ],
                    ],
                    "consultationRequest" => [
                        "id" => $this->consultationRequest->id,
                    ],
                ],
                [
                    "id" => $this->activityLogOne->id,
                    "message" => $this->activityLogOne->message,
                    "occuredTime" => $this->activityLogOne->occuredTime,
                    "teamMember" => [
                        "id" => $this->teamMembership->id,
                        "client" => [
                            "id" => $this->teamMembership->client->id,
                            "name" => $this->teamMembership->client->getFullName(),
                        ],
                    ],
                    "consultationRequest" => [
                        "id" => $this->consultationRequest->id,
                    ],
                ],
            ],
        ];
        $this->get($this->activityLogUri, $this->teamMembership->client->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
}
