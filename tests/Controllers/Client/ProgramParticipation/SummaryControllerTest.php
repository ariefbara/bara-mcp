<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use DateTime;
use Tests\Controllers\Client\ProgramParticipationTestCase;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\ConsultationSession\RecordOfConsultantFeedback;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfConsultationSession;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
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
    
    protected $metricAssignment;
    protected $assignmentFieldOne;
    protected $assignmentFieldTwo_removed;
    protected $assignmentFieldThree;
    
    protected $assignmentReportOne_approved_earliest;
    protected $assignmentReportTwo_approved_latest;
    protected $assignmentReportThree_approved_removed;
    protected $assignmentReportFour_last;
    
    // report[index]assignmentField[index]
    protected $reportValue_11;
    protected $reportValue_12;
    protected $reportValue_13;
    protected $reportValue_21;
    protected $reportValue_22;
    protected $reportValue_23;
    protected $reportValue_31;
    protected $reportValue_32;
    protected $reportValue_33;
    protected $reportValue_41;
    protected $reportValue_42;
    protected $reportValue_43;


    protected function setUp(): void
    {
        parent::setUp();
        $this->summaryUri = $this->programParticipationUri . "/{$this->programParticipation->id}/summary";
        
        $this->connection->table("Mission")->truncate();
        $this->connection->table("CompletedMission")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ConsultantFeedback")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        
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
        
        $metric = new RecordOfMetric($program, '0');
        $this->connection->table("Metric")->insert($metric->toArrayForDbEntry());
        
        $this->metricAssignment = new RecordOfMetricAssignment($participant, '0');
        $this->connection->table("MetricAssignment")->insert($this->metricAssignment->toArrayForDbEntry());
        
        $this->assignmentFieldOne = new RecordOfAssignmentField($this->metricAssignment, $metric, '1');
        $this->assignmentFieldOne->target = 100;
        $this->assignmentFieldTwo_removed = new RecordOfAssignmentField($this->metricAssignment, $metric, '2');
        $this->assignmentFieldTwo_removed->removed = true;
        $this->assignmentFieldTwo_removed->target = 200;
        $this->assignmentFieldThree = new RecordOfAssignmentField($this->metricAssignment, $metric, '3');
        $this->assignmentFieldThree->target = 300;
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldOne->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldTwo_removed->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->assignmentFieldThree->toArrayForDbEntry());
        
        $this->assignmentReportOne_approved_earliest = new RecordOfMetricAssignmentReport($this->metricAssignment, '1');
        $this->assignmentReportOne_approved_earliest->approved = true;
        $this->assignmentReportOne_approved_earliest->observationTime = (new DateTime('-8 days'))->format('Y-m-d H:i:s');
        $this->assignmentReportTwo_approved_latest = new RecordOfMetricAssignmentReport($this->metricAssignment, '2');
        $this->assignmentReportTwo_approved_latest->approved = true;
        $this->assignmentReportTwo_approved_latest->observationTime = (new DateTime('-6 days'))->format('Y-m-d H:i:s');
        $this->assignmentReportThree_approved_removed = new RecordOfMetricAssignmentReport($this->metricAssignment, '3');
        $this->assignmentReportThree_approved_removed->approved = true;
        $this->assignmentReportThree_approved_removed->removed = true;
        $this->assignmentReportThree_approved_removed->observationTime = (new DateTime('-4 days'))->format('Y-m-d H:i:s');
        $this->assignmentReportFour_last = new RecordOfMetricAssignmentReport($this->metricAssignment, '4');
        $this->assignmentReportFour_last->approved = false;
        $this->assignmentReportFour_last->observationTime = (new DateTime('-2 days'))->format('Y-m-d H:i:s');
        $this->connection->table("MetricAssignmentReport")->insert($this->assignmentReportOne_approved_earliest->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->assignmentReportTwo_approved_latest->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->assignmentReportThree_approved_removed->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->assignmentReportFour_last->toArrayForDbEntry());
        
        $this->reportValue_11 = new RecordOfAssignmentFieldValue($this->assignmentReportOne_approved_earliest, $this->assignmentFieldOne, '11');
        $this->reportValue_11->inputValue = 10;
        $this->reportValue_12 = new RecordOfAssignmentFieldValue($this->assignmentReportOne_approved_earliest, $this->assignmentFieldTwo_removed, '12');
        $this->reportValue_12->inputValue = 40;
        $this->reportValue_13 = new RecordOfAssignmentFieldValue($this->assignmentReportOne_approved_earliest, $this->assignmentFieldThree, '13');
        $this->reportValue_13->inputValue = 60;
        $this->reportValue_21 = new RecordOfAssignmentFieldValue($this->assignmentReportTwo_approved_latest, $this->assignmentFieldOne, '21');
        $this->reportValue_21->inputValue = 30;
        $this->reportValue_22 = new RecordOfAssignmentFieldValue($this->assignmentReportTwo_approved_latest, $this->assignmentFieldTwo_removed, '22');
        $this->reportValue_22->inputValue = 80;
        $this->reportValue_23 = new RecordOfAssignmentFieldValue($this->assignmentReportTwo_approved_latest, $this->assignmentFieldThree, '23');
        $this->reportValue_23->inputValue = 450;
        $this->reportValue_31 = new RecordOfAssignmentFieldValue($this->assignmentReportThree_approved_removed, $this->assignmentFieldOne, '31');
        $this->reportValue_31->inputValue = 30;
        $this->reportValue_32 = new RecordOfAssignmentFieldValue($this->assignmentReportThree_approved_removed, $this->assignmentFieldTwo_removed, '32');
        $this->reportValue_32->inputValue = 120;
        $this->reportValue_33 = new RecordOfAssignmentFieldValue($this->assignmentReportThree_approved_removed, $this->assignmentFieldThree, '33');
        $this->reportValue_33->inputValue = 180;
        $this->reportValue_41 = new RecordOfAssignmentFieldValue($this->assignmentReportFour_last, $this->assignmentFieldOne, '41');
        $this->reportValue_41->inputValue = 40;
        $this->reportValue_42 = new RecordOfAssignmentFieldValue($this->assignmentReportFour_last, $this->assignmentFieldTwo_removed, '42');
        $this->reportValue_42->inputValue = 160;
        $this->reportValue_43 = new RecordOfAssignmentFieldValue($this->assignmentReportFour_last, $this->assignmentFieldThree, '43');
        $this->reportValue_43->inputValue = 240;
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_11->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_12->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_13->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_21->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_22->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_23->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_31->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_32->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_33->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_41->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_42->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->reportValue_43->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("CompletedMission")->truncate();
        $this->connection->table("ConsultationSession")->truncate();
        $this->connection->table("ConsultantFeedback")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
    }
    
    public function test_show_200()
    {
$this->disableExceptionHandling();
        $this->get($this->summaryUri, $this->programParticipation->client->token);
        $this->seeStatusCode(200);
        
        $response = [
            'participantId' => $this->programParticipation->participant->id,
            'participantRating' => "3.0000",
            'totalCompletedMission' => "2",
            'totalMission' => "3",
            'lastCompletedTime' => $this->completedMission_01->completedTime,
            'lastMissionId' => $this->completedMission_01->mission->id,
            'lastMissionName' => $this->completedMission_01->mission->name,
            'achievement' => "0.9",
            'completedMetric' => "1",
            'totalAssignedMetric' => "2",
        ];
        $this->seeJsonContains($response);
    }
}
