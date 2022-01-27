<?php

namespace Tests\Controllers\Client\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;

class MetricSummaryControllerTest extends ExtendedClientParticipantTestCase
{
    protected $metricOne;
    protected $metricTwo;
    protected $metricThree;
    protected $metricFour;
    
    protected $metricAssignment;
    
    protected $assignmentFieldOne_m1;
    protected $assignmentFieldTwo_m2;
    protected $assignmentFieldThree_m3;
    
    protected $metricAssignmentReportOne;
    protected $metricAssignmentReportTwo;
    protected $metricAssignmentReportThree;
    
    protected $assignmentFieldValueOne_af1mar1;
    protected $assignmentFieldValueTwo_af2mar1;
    protected $assignmentFieldValueThree_af3mar1;
    protected $assignmentFieldValueFour_af1mar2;
    protected $assignmentFieldValueFive_af2mar2;
    protected $assignmentFieldValueSix_af3mar2;
    protected $assignmentFieldValueSeven_af1mar3;
    protected $assignmentFieldValueEight_af2mar3;
    protected $assignmentFieldValueNine_af3mar3;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->connection->table('Metric')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
        
        $participant = $this->clientParticipant->participant;
        $program = $participant->program;
        
        $this->metricOne = new RecordOfMetric($program, '1');
        $this->metricTwo = new RecordOfMetric($program, '2');
        $this->metricThree = new RecordOfMetric($program, '3');
        $this->metricFour = new RecordOfMetric($program, '4');
        
        $this->metricAssignment = new RecordOfMetricAssignment($participant, '1');
        
        $this->assignmentFieldOne_m1 = new RecordOfAssignmentField($this->metricAssignment, $this->metricOne, '1');
        $this->assignmentFieldTwo_m2 = new RecordOfAssignmentField($this->metricAssignment, $this->metricTwo, '2');
        $this->assignmentFieldThree_m3 = new RecordOfAssignmentField($this->metricAssignment, $this->metricThree, '3');
        
        $this->metricAssignmentReportOne = new RecordOfMetricAssignmentReport($this->metricAssignment, '1');
        $this->metricAssignmentReportOne->approved = true;
        $this->metricAssignmentReportOne->observationTime = (new \DateTimeImmutable('-60 days'))->format('Y-m-d H:i:s');
        $this->metricAssignmentReportTwo = new RecordOfMetricAssignmentReport($this->metricAssignment, '2');
        $this->metricAssignmentReportTwo->observationTime = (new \DateTimeImmutable('-30 days'))->format('Y-m-d H:i:s');
        $this->metricAssignmentReportThree = new RecordOfMetricAssignmentReport($this->metricAssignment, '3');
        $this->metricAssignmentReportThree->observationTime = (new \DateTimeImmutable('-4 days'))->format('Y-m-d H:i:s');
        
        $this->assignmentFieldValueOne_af1mar1 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldOne_m1, '1');
        $this->assignmentFieldValueTwo_af2mar1 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldTwo_m2, '2');
        $this->assignmentFieldValueThree_af3mar1 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne, $this->assignmentFieldThree_m3, '3');
        $this->assignmentFieldValueFour_af1mar2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo, $this->assignmentFieldOne_m1, '4');
        $this->assignmentFieldValueFive_af2mar2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo, $this->assignmentFieldTwo_m2, '5');
        $this->assignmentFieldValueSix_af3mar2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo, $this->assignmentFieldThree_m3, '6');
        $this->assignmentFieldValueSeven_af1mar3 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportThree, $this->assignmentFieldOne_m1, '7');
        $this->assignmentFieldValueEight_af2mar3 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportThree, $this->assignmentFieldTwo_m2, '8');
        $this->assignmentFieldValueNine_af3mar3 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportThree, $this->assignmentFieldThree_m3, '9');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Metric')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
    }
    
    protected function show()
    {
        $this->insertClientParticipantRecord();
        
        $this->metricOne->insert($this->connection);
        $this->metricTwo->insert($this->connection);
        $this->metricThree->insert($this->connection);
        $this->metricFour->insert($this->connection);
        
        $this->metricAssignment->insert($this->connection);
        
        $this->assignmentFieldOne_m1->insert($this->connection);
        $this->assignmentFieldTwo_m2->insert($this->connection);
        $this->assignmentFieldThree_m3->insert($this->connection);
        
        $this->metricAssignmentReportOne->insert($this->connection);
        $this->metricAssignmentReportTwo->insert($this->connection);
        $this->metricAssignmentReportThree->insert($this->connection);
        
        $this->assignmentFieldValueOne_af1mar1->insert($this->connection);
        $this->assignmentFieldValueTwo_af2mar1->insert($this->connection);
        $this->assignmentFieldValueThree_af3mar1->insert($this->connection);
        $this->assignmentFieldValueFour_af1mar2->insert($this->connection);
        $this->assignmentFieldValueFive_af2mar2->insert($this->connection);
        $this->assignmentFieldValueSix_af3mar2->insert($this->connection);
        $this->assignmentFieldValueSeven_af1mar3->insert($this->connection);
        $this->assignmentFieldValueEight_af2mar3->insert($this->connection);
        $this->assignmentFieldValueNine_af3mar3->insert($this->connection);
        
        $uri = $this->clientParticipantUri . "/metric-summary";
        $this->get($uri, $this->client->token);
    }
    public function test_show_200()
    {
        $this->show();
        $this->seeStatusCode(200);
        
        $recordOneResponse = [
            'startDate' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueOne_af1mar1->assignmentField->target),
            'lastApprovedReportId' => $this->assignmentFieldValueOne_af1mar1->metricAssignmentReport->id,
            'lastApprovedReportValue' => strval($this->assignmentFieldValueOne_af1mar1->inputValue),
            'lastApprovedObservationTime' => $this->assignmentFieldValueOne_af1mar1->metricAssignmentReport->observationTime,
            'lastUnapprovedReportId' => $this->assignmentFieldValueSeven_af1mar3->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueSeven_af1mar3->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueSeven_af1mar3->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordOneResponse);
        
        $recordTwoResponse = [
            'startDate' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueTwo_af2mar1->assignmentField->target),
            'lastApprovedReportId' => $this->assignmentFieldValueTwo_af2mar1->metricAssignmentReport->id,
            'lastApprovedReportValue' => strval($this->assignmentFieldValueTwo_af2mar1->inputValue),
            'lastApprovedObservationTime' => $this->assignmentFieldValueTwo_af2mar1->metricAssignmentReport->observationTime,
            'lastUnapprovedReportId' => $this->assignmentFieldValueEight_af2mar3->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueEight_af2mar3->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueEight_af2mar3->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordTwoResponse);
        
        $recordThreeResponse = [
            'startDate' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueThree_af3mar1->assignmentField->target),
            'lastApprovedReportId' => $this->assignmentFieldValueThree_af3mar1->metricAssignmentReport->id,
            'lastApprovedReportValue' => strval($this->assignmentFieldValueThree_af3mar1->inputValue),
            'lastApprovedObservationTime' => $this->assignmentFieldValueThree_af3mar1->metricAssignmentReport->observationTime,
            'lastUnapprovedReportId' => $this->assignmentFieldValueNine_af3mar3->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueNine_af3mar3->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueNine_af3mar3->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordThreeResponse);
    }
    public function test_show_ContainRemovedReport_excludeFromResult()
    {
        $this->metricAssignmentReportOne->removed = true;
        $this->show();
        $this->seeStatusCode(200);
        
        $recordOneResponse = [
            'startDate' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueOne_af1mar1->assignmentField->target),
            'lastApprovedReportId' => null,
            'lastApprovedReportValue' => null,
            'lastApprovedObservationTime' => null,
            'lastUnapprovedReportId' => $this->assignmentFieldValueSeven_af1mar3->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueSeven_af1mar3->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueSeven_af1mar3->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordOneResponse);
        
        $recordTwoResponse = [
            'startDate' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueTwo_af2mar1->assignmentField->target),
            'lastApprovedReportId' => null,
            'lastApprovedReportValue' => null,
            'lastApprovedObservationTime' => null,
            'lastUnapprovedReportId' => $this->assignmentFieldValueEight_af2mar3->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueEight_af2mar3->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueEight_af2mar3->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordTwoResponse);
        
        $recordThreeResponse = [
            'startDate' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueThree_af3mar1->assignmentField->target),
            'lastApprovedReportId' => null,
            'lastApprovedReportValue' => null,
            'lastApprovedObservationTime' => null,
            'lastUnapprovedReportId' => $this->assignmentFieldValueNine_af3mar3->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueNine_af3mar3->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueNine_af3mar3->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordThreeResponse);
    }
    public function test_show_ContainRejectedReport_excludeFromResult()
    {
        $this->metricAssignmentReportThree->approved = false;
        $this->show();
        $this->seeStatusCode(200);
        
        $recordOneResponse = [
            'startDate' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueOne_af1mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueOne_af1mar1->assignmentField->target),
            'lastApprovedReportId' => $this->assignmentFieldValueOne_af1mar1->metricAssignmentReport->id,
            'lastApprovedReportValue' => strval($this->assignmentFieldValueOne_af1mar1->inputValue),
            'lastApprovedObservationTime' => $this->assignmentFieldValueOne_af1mar1->metricAssignmentReport->observationTime,
            'lastUnapprovedReportId' => $this->assignmentFieldValueFour_af1mar2->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueFour_af1mar2->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueFour_af1mar2->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordOneResponse);
        
        $recordTwoResponse = [
            'startDate' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueTwo_af2mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueTwo_af2mar1->assignmentField->target),
            'lastApprovedReportId' => $this->assignmentFieldValueTwo_af2mar1->metricAssignmentReport->id,
            'lastApprovedReportValue' => strval($this->assignmentFieldValueTwo_af2mar1->inputValue),
            'lastApprovedObservationTime' => $this->assignmentFieldValueTwo_af2mar1->metricAssignmentReport->observationTime,
            'lastUnapprovedReportId' => $this->assignmentFieldValueFive_af2mar2->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueFive_af2mar2->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueFive_af2mar2->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordTwoResponse);
        
        $recordThreeResponse = [
            'startDate' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metricAssignment->startDate,
            'endDate' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metricAssignment->endDate,
            'metricName' => $this->assignmentFieldValueThree_af3mar1->assignmentField->metric->name,
            'target' => strval($this->assignmentFieldValueThree_af3mar1->assignmentField->target),
            'lastApprovedReportId' => $this->assignmentFieldValueThree_af3mar1->metricAssignmentReport->id,
            'lastApprovedReportValue' => strval($this->assignmentFieldValueThree_af3mar1->inputValue),
            'lastApprovedObservationTime' => $this->assignmentFieldValueThree_af3mar1->metricAssignmentReport->observationTime,
            'lastUnapprovedReportId' => $this->assignmentFieldValueSix_af3mar2->metricAssignmentReport->id,
            'lastUnapprovedReportValue' => strval($this->assignmentFieldValueSix_af3mar2->inputValue),
            'lastUnapprovedObservationTime' => strval($this->assignmentFieldValueSix_af3mar2->metricAssignmentReport->observationTime),
        ];
        $this->seeJsonContains($recordThreeResponse);
    }
    public function test_show_inactiveParticipant_403()
    {
        $this->clientParticipant->participant->active = false;
        $this->show();
        $this->seeStatusCode(403);
    }
}
