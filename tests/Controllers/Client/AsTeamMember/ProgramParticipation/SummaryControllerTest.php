<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use DateTime;
use Tests\Controllers\Client\AsTeamMember\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfConsultantFeedback;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;

class SummaryControllerTest extends ProgramParticipationTestCase
{
    protected $summaryUri;
    
    protected $mission;
    protected $missionOne;
    protected $missionTwo;
    protected $missionThree_unpublished;
    protected $completedMission_00;
    protected $completedMission_01;
    
    protected $consultantFeedback_01;
    protected $consultantFeedback_02;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->summaryUri = $this->programParticipationUri . "/{$this->programParticipation->id}/summary";
        
        $this->connection->table("Mission")->truncate();
        $this->connection->table("CompletedMission")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ConsultantFeedback")->truncate();
        
        $participant = $this->programParticipation->participant;
        $program = $participant->program;
        $participantOne = new RecordOfParticipant($program, 1);
        $this->connection->table("Participant")->insert($participantOne->toArrayForDbEntry());
        
        $this->mission = new RecordOfMission($program, null, 0, null);
        $this->mission->published = true;
        $this->missionOne = new RecordOfMission($program, null, 1, null);
        $this->missionOne->published = true;
        $this->missionTwo = new RecordOfMission($program, null, 2, null);
        $this->missionTwo->published = true;
        $this->missionThree_unpublished = new RecordOfMission($program, null, 3, null);
        $this->connection->table("Mission")->insert($this->mission->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionOne->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionTwo->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionThree_unpublished->toArrayForDbEntry());
        
        $this->completedMission_00 = new RecordOfCompletedMission($participant, $this->mission, "00");
        $this->completedMission_00->completedTime = (new DateTime("-48 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_01 = new RecordOfCompletedMission($participant, $this->missionOne, "01");
        $this->completedMission_01->completedTime = (new DateTime("-12 hours"))->format("Y-m-d H:i:s");
        $completedMission_12 = new RecordOfCompletedMission($participantOne, $this->missionTwo, "12");
        $completedMission_12->completedTime = (new \DateTime('-6 hours'))->format('Y-m-d H:i:s');
        $this->connection->table("CompletedMission")->insert($this->completedMission_00->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_01->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($completedMission_12->toArrayForDbEntry());
        
        $consultationSession_01 = new RecordOfConsultationSession(null, $participant, null, "01");
        $consultationSession_02 = new RecordOfConsultationSession(null, $participant, null, "02");
        $consultationSession_03 = new RecordOfConsultationSession(null, $participant, null, "03");
        $consultationSession_11 = new RecordOfConsultationSession(null, $participantOne, null, "11");
        $this->connection->table("ConsultationSession")->insert($consultationSession_01->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($consultationSession_02->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($consultationSession_03->toArrayForDbEntry());
        $this->connection->table("ConsultationSession")->insert($consultationSession_11->toArrayForDbEntry());
        
        $this->consultantFeedback_01 = new RecordOfConsultantFeedback($consultationSession_01, null, "01");
        $this->consultantFeedback_01->participantRating = 2;
        $this->consultantFeedback_02 = new RecordOfConsultantFeedback($consultationSession_02, null, "02");
        $this->consultantFeedback_02->participantRating = 2;
        $this->consultantFeedback_03 = new RecordOfConsultantFeedback($consultationSession_03, null, "03");
        $this->consultantFeedback_03->participantRating = 5;
        $this->consultantFeedback_11 = new RecordOfConsultantFeedback($consultationSession_11, null, "11");
        $this->consultantFeedback_11->participantRating = 1;
        $this->connection->table("ConsultantFeedback")->insert($this->consultantFeedback_01->toArrayForDbEntry());
        $this->connection->table("ConsultantFeedback")->insert($this->consultantFeedback_02->toArrayForDbEntry());
        $this->connection->table("ConsultantFeedback")->insert($this->consultantFeedback_03->toArrayForDbEntry());
        $this->connection->table("ConsultantFeedback")->insert($this->consultantFeedback_11->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("CompletedMission")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ConsultantFeedback")->truncate();
    }
    
    public function test_show_200()
    {
$this->disableExceptionHandling();
var_dump($this->summaryUri);
        $this->get($this->summaryUri, $this->teamMember->client->token);
        $this->seeStatusCode(200);
        
        $response = [
            'participantId' => $this->programParticipation->participant->id,
            'participantRating' => "3.000",
            'totalCompletedMission' => "2",
            'totalMission' => "3",
            'lastCompletedTime' => $this->completedMission_01->completedTime,
            'lastMissionId' => $this->completedMission_01->mission->id,
            'lastMissionName' => $this->completedMission_01->mission->name,
        ];
    }
}
