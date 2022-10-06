<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;

class CoordinatedProgramsSummaryControllerTest extends AggregatedCoordinatorInPersonnelContextTestCase
{
    protected $viewUri;
    ////
    protected $participantOne_prog1;
    protected $participantTwo_prog1;
    //
    protected $participantThree_prog2;
    protected $participantFour_prog2;
    protected $participantFive_prog2;
    ////
    protected $missionOne_prog1;
    protected $missionTwo_prog1;
    protected $missionThree_prog1;
    //
    protected $missionFour_prog2;
    protected $missionFive_prog2;
    ////
    protected $completedMissionOne_p1;
    protected $completedMissionTwo_p1;
    protected $completedMissionThree_p2;
    //
    protected $completedMissionFour_p4;
    ////
//    protected $metricAssignmentOne_p1;
    protected $metricAssignmentTwo_p1;
    protected $metricAssignmentThree_p2;
    //
    protected $metricAssignmentFour_p3;
    protected $metricAssignmentFive_p4;
    ////
    protected $metricOne_prog1;
    protected $metricTwo_prog1;
    //
    protected $metricThree_prog2;
    protected $metricFour_prog2;
    ////
//    protected $assignedFieldOne_ma1m1;
    protected $assignedFieldTwo_ma2m1;
    protected $assignedFieldThree_ma2m2;
    protected $assignedFieldFour_ma3m1;
    ////
    protected $assignedFieldFive_ma4m3;
    protected $assignedFieldSix_ma4m4;
    protected $assignedFieldSeven_ma5m3;
    ////
//    protected $metricAssignmentReportOne_ma1;
    protected $metricAssignmentReportTwo_ma2;
    protected $metricAssignmentReportThree_ma2;
    protected $metricAssignmentReportFour_ma3;
    //
    protected $metricAssignmentReportFive_ma4;
    ////
//    protected $assignmentFieldValueOne_mar1af1;
    protected $assignmentFieldValueTwo_mar2af2;
    protected $assignmentFieldValueThree_mar2af3;
    protected $assignmentFieldValueFour_mar3af2;
    protected $assignmentFieldValueFive_mar3af3;
    protected $assignmentFieldValueSix_mar4af4;
    //
    protected $assignmentFieldValueSeven_mar5af5;
    protected $assignmentFieldValueEight_mar5af6;

    protected function setUp(): void
    {
        parent::setUp();
        $this->viewUri = $this->personnelUri . "/coordinated-programs-summary";
        
        $this->connection->table('Participant')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('Metric')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
        
        $programOne = $this->coordinatorOne->program;
        $programTwo = $this->coordinatorTwo->program;
        
        $this->participantOne_prog1 = new RecordOfParticipant($programOne, 1);
        $this->participantTwo_prog1 = new RecordOfParticipant($programOne, 2);
        $this->participantThree_prog2 = new RecordOfParticipant($programTwo, 3);
        $this->participantFour_prog2 = new RecordOfParticipant($programTwo, 4);
        $this->participantFive_prog2 = new RecordOfParticipant($programTwo, 5);
        
        $this->missionOne_prog1 = new RecordOfMission($programOne, null, 1, null);
        $this->missionOne_prog1->published = true;
        $this->missionTwo_prog1 = new RecordOfMission($programOne, null, 2, null);
        $this->missionTwo_prog1->published = true;
        $this->missionThree_prog1 = new RecordOfMission($programOne, null, 3, null);
        $this->missionThree_prog1->published = true;
        $this->missionFour_prog2 = new RecordOfMission($programTwo, null, 4, null);
        $this->missionFour_prog2->published = true;
        $this->missionFive_prog2 = new RecordOfMission($programTwo, null, 5, null);
        $this->missionFive_prog2->published = true;
        
        $this->completedMissionOne_p1 = new RecordOfCompletedMission($this->participantOne_prog1, $this->missionOne_prog1, 1);
        $this->completedMissionTwo_p1 = new RecordOfCompletedMission($this->participantOne_prog1, $this->missionTwo_prog1, 2);
        $this->completedMissionThree_p2 = new RecordOfCompletedMission($this->participantTwo_prog1, $this->missionOne_prog1, 3);
        $this->completedMissionFour_p4 = new RecordOfCompletedMission($this->participantFour_prog2, $this->missionFour_prog2, 4);
        
//        $this->metricAssignmentOne_p1 = new RecordOfMetricAssignment($this->participantOne_prog1, 1);
        $this->metricAssignmentTwo_p1 = new RecordOfMetricAssignment($this->participantOne_prog1, 2);
        $this->metricAssignmentThree_p2 = new RecordOfMetricAssignment($this->participantTwo_prog1, 3);
        $this->metricAssignmentFour_p3 = new RecordOfMetricAssignment($this->participantThree_prog2, 4);
        $this->metricAssignmentFive_p4 = new RecordOfMetricAssignment($this->participantFour_prog2, 5);
        
        $this->metricOne_prog1 = new RecordOfMetric($programOne, 1);
        $this->metricTwo_prog1 = new RecordOfMetric($programOne, 2);
        $this->metricThree_prog2 = new RecordOfMetric($programTwo, 3);
        $this->metricFour_prog2 = new RecordOfMetric($programTwo, 4);
        
//        $this->assignedFieldOne_ma1m1 = new RecordOfAssignmentField($this->metricAssignmentOne_p1, $this->metricOne_prog1, 1);
//        $this->assignedFieldOne_ma1m1->target = 100;
        $this->assignedFieldTwo_ma2m1 = new RecordOfAssignmentField($this->metricAssignmentTwo_p1, $this->metricOne_prog1, 2);
        $this->assignedFieldTwo_ma2m1->target = 200;
        $this->assignedFieldThree_ma2m2 = new RecordOfAssignmentField($this->metricAssignmentTwo_p1, $this->metricTwo_prog1, 3);
        $this->assignedFieldThree_ma2m2->target = 300;
        $this->assignedFieldFour_ma3m1 = new RecordOfAssignmentField($this->metricAssignmentThree_p2, $this->metricOne_prog1, 4);
        $this->assignedFieldFour_ma3m1->target = 400;
        $this->assignedFieldFive_ma4m3 = new RecordOfAssignmentField($this->metricAssignmentFour_p3, $this->metricThree_prog2, 5);
        $this->assignedFieldFive_ma4m3->target = 500;
        $this->assignedFieldSix_ma4m4 = new RecordOfAssignmentField($this->metricAssignmentFour_p3, $this->metricFour_prog2, 6);
        $this->assignedFieldSix_ma4m4->target = 600;
        $this->assignedFieldSeven_ma5m3 = new RecordOfAssignmentField($this->metricAssignmentFive_p4, $this->metricThree_prog2, 7);
        $this->assignedFieldSeven_ma5m3->target = 700;
        
//        $this->metricAssignmentReportOne_ma1 = new RecordOfMetricAssignmentReport($this->metricAssignmentOne_p1, 1);
//        $this->metricAssignmentReportOne_ma1->approved = true;
        $this->metricAssignmentReportTwo_ma2 = new RecordOfMetricAssignmentReport($this->metricAssignmentTwo_p1, 2);
        $this->metricAssignmentReportTwo_ma2->approved = true;
        $this->metricAssignmentReportTwo_ma2->observationTime = (new \DateTimeImmutable('-48 hours'));
        $this->metricAssignmentReportThree_ma2 = new RecordOfMetricAssignmentReport($this->metricAssignmentTwo_p1, 3);
        $this->metricAssignmentReportThree_ma2->approved = true;
        $this->metricAssignmentReportThree_ma2->observationTime = (new \DateTimeImmutable('-24 hours'));
        $this->metricAssignmentReportFour_ma3 = new RecordOfMetricAssignmentReport($this->metricAssignmentThree_p2, 4);
        $this->metricAssignmentReportFour_ma3->approved = true;
        $this->metricAssignmentReportFive_ma4 = new RecordOfMetricAssignmentReport($this->metricAssignmentFour_p3, 5);
        $this->metricAssignmentReportFive_ma4->approved = true;
        
//        $this->assignmentFieldValueOne_mar1af1 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_ma1, $this->assignedFieldOne_ma1m1, 1);
//        $this->assignmentFieldValueOne_mar1af1->inputValue = 25;
        $this->assignmentFieldValueTwo_mar2af2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo_ma2, $this->assignedFieldTwo_ma2m1, 2);
        $this->assignmentFieldValueTwo_mar2af2->inputValue = 125;
        $this->assignmentFieldValueThree_mar2af3 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo_ma2, $this->assignedFieldThree_ma2m2, 3);
        $this->assignmentFieldValueThree_mar2af3->inputValue = 225;
        $this->assignmentFieldValueFour_mar3af2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportThree_ma2, $this->assignedFieldTwo_ma2m1, 4);
        $this->assignmentFieldValueFour_mar3af2->inputValue = 175;
        $this->assignmentFieldValueFive_mar3af3 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportThree_ma2, $this->assignedFieldThree_ma2m2, 5);
        $this->assignmentFieldValueFive_mar3af3->inputValue = 275;
        $this->assignmentFieldValueSix_mar4af4 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportFour_ma3, $this->assignedFieldFour_ma3m1, 6);
        $this->assignmentFieldValueSix_mar4af4->inputValue = 325;
        $this->assignmentFieldValueSeven_mar5af5 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportFive_ma4, $this->assignedFieldFive_ma4m3, 7);
        $this->assignmentFieldValueSeven_mar5af5->inputValue = 425;
        $this->assignmentFieldValueEight_mar5af6 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportFive_ma4, $this->assignedFieldSix_ma4m4, 8);
        $this->assignmentFieldValueEight_mar5af6->inputValue = 525;
    }
    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table('Participant')->truncate();
//        $this->connection->table('Mission')->truncate();
//        $this->connection->table('CompletedMission')->truncate();
//        $this->connection->table('MetricAssignment')->truncate();
//        $this->connection->table('Metric')->truncate();
//        $this->connection->table('AssignmentField')->truncate();
//        $this->connection->table('MetricAssignmentReport')->truncate();
//        $this->connection->table('AssignmentFieldValue')->truncate();
    }
    
    protected function view()
    {
        $this->persistAggregatedCoordinatorDependency();
        
        $this->participantOne_prog1->insert($this->connection);
        $this->participantTwo_prog1->insert($this->connection);
        $this->participantThree_prog2->insert($this->connection);
        $this->participantFour_prog2->insert($this->connection);
        $this->participantFive_prog2->insert($this->connection);
        
        $this->missionOne_prog1->insert($this->connection);
        $this->missionTwo_prog1->insert($this->connection);
        $this->missionThree_prog1->insert($this->connection);
        $this->missionFour_prog2->insert($this->connection);
        $this->missionFive_prog2->insert($this->connection);
        
        $this->completedMissionOne_p1->insert($this->connection);
        $this->completedMissionTwo_p1->insert($this->connection);
        $this->completedMissionThree_p2->insert($this->connection);
        $this->completedMissionFour_p4->insert($this->connection);
        
//        $this->metricAssignmentOne_p1->insert($this->connection);
        $this->metricAssignmentTwo_p1->insert($this->connection);
        $this->metricAssignmentThree_p2->insert($this->connection);
        $this->metricAssignmentFour_p3->insert($this->connection);
        $this->metricAssignmentFive_p4->insert($this->connection);
        
        $this->metricOne_prog1->insert($this->connection);
        $this->metricTwo_prog1->insert($this->connection);
        $this->metricThree_prog2->insert($this->connection);
        $this->metricFour_prog2->insert($this->connection);
        
//        $this->assignedFieldOne_ma1m1->insert($this->connection);
        $this->assignedFieldTwo_ma2m1->insert($this->connection);
        $this->assignedFieldThree_ma2m2->insert($this->connection);
        $this->assignedFieldFour_ma3m1->insert($this->connection);
        $this->assignedFieldFive_ma4m3->insert($this->connection);
        $this->assignedFieldSix_ma4m4->insert($this->connection);
        $this->assignedFieldSeven_ma5m3->insert($this->connection);
        
//        $this->metricAssignmentReportOne_ma1->insert($this->connection);
        $this->metricAssignmentReportTwo_ma2->insert($this->connection);
        $this->metricAssignmentReportThree_ma2->insert($this->connection);
        $this->metricAssignmentReportFour_ma3->insert($this->connection);
        $this->metricAssignmentReportFive_ma4->insert($this->connection);
        
//        $this->assignmentFieldValueOne_mar1af1->insert($this->connection);
        $this->assignmentFieldValueTwo_mar2af2->insert($this->connection);
        $this->assignmentFieldValueThree_mar2af3->insert($this->connection);
        $this->assignmentFieldValueFour_mar3af2->insert($this->connection);
        $this->assignmentFieldValueFive_mar3af3->insert($this->connection);
        $this->assignmentFieldValueSix_mar4af4->insert($this->connection);
        $this->assignmentFieldValueSeven_mar5af5->insert($this->connection);
        $this->assignmentFieldValueEight_mar5af6->insert($this->connection);
        
        $this->get($this->viewUri, $this->personnel->token);
    }
    public function test_view_200()
    {
$this->disableExceptionHandling();
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [
            'data' => [
                [
                    'id' => $this->coordinatorOne->program->id,
                    'name' => $this->coordinatorOne->program->name,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'participantCount' => '2',
                    'minCompletedMission' => '1',
                    'maxCompletedMission' => '2',
                    'averageCompletedMission' => '1.5000',
                    'missionCount' => '3',
                    'minMetricAchievement' => '0.8125',
                    'maxMetricAchievement' => '0.8958333333333333',
                    'averageMetricAchievement' => '0.8541666666666666',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.0000',
                    'averageMetricCompletion' => '0.00000000',
                ],
                [
                    'id' => $this->coordinatorTwo->program->id,
                    'name' => $this->coordinatorTwo->program->name,
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'participantCount' => '3',
                    'minCompletedMission' => '0',
                    'maxCompletedMission' => '1',
                    'averageCompletedMission' => '0.3333',
                    'missionCount' => '2',
                    'minMetricAchievement' => '0',
                    'maxMetricAchievement' => '0.8625',
                    'averageMetricAchievement' => '0.43125',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.0000',
                    'averageMetricCompletion' => '0.00000000',
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_view_excludeDataFromInactiveParticipant()
    {
        $this->participantTwo_prog1->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [
            'data' => [
                [
                    'id' => $this->coordinatorOne->program->id,
                    'name' => $this->coordinatorOne->program->name,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'participantCount' => '1',
                    'minCompletedMission' => '2',
                    'maxCompletedMission' => '2',
                    'averageCompletedMission' => '2.0000',
                    'missionCount' => '3',
                    'minMetricAchievement' => '0.8958333333333333',
                    'maxMetricAchievement' => '0.8958333333333333',
                    'averageMetricAchievement' => '0.8958333333333333',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.0000',
                    'averageMetricCompletion' => '0.00000000',
                ],
                [
                    'id' => $this->coordinatorTwo->program->id,
                    'name' => $this->coordinatorTwo->program->name,
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'participantCount' => '3',
                    'minCompletedMission' => '0',
                    'maxCompletedMission' => '1',
                    'averageCompletedMission' => '0.3333',
                    'missionCount' => '2',
                    'minMetricAchievement' => '0',
                    'maxMetricAchievement' => '0.8625',
                    'averageMetricAchievement' => '0.43125',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.0000',
                    'averageMetricCompletion' => '0.00000000',
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_view_excludeUnamangedProgram_inactiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [
            'data' => [
                [
                    'id' => $this->coordinatorOne->program->id,
                    'name' => $this->coordinatorOne->program->name,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'participantCount' => '2',
                    'minCompletedMission' => '1',
                    'maxCompletedMission' => '2',
                    'averageCompletedMission' => '1.5000',
                    'missionCount' => '3',
                    'minMetricAchievement' => '0.8125',
                    'maxMetricAchievement' => '0.8958333333333333',
                    'averageMetricAchievement' => '0.8541666666666666',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.0000',
                    'averageMetricCompletion' => '0.00000000',
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_view_excludeUnaproovedMetricReport()
    {
        $this->metricAssignmentReportThree_ma2->approved = false;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [
            'data' => [
                [
                    'id' => $this->coordinatorOne->program->id,
                    'name' => $this->coordinatorOne->program->name,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'participantCount' => '2',
                    'minCompletedMission' => '1',
                    'maxCompletedMission' => '2',
                    'averageCompletedMission' => '1.5000',
                    'missionCount' => '3',
                    'minMetricAchievement' => '0.6875',
                    'maxMetricAchievement' => '0.8125',
                    'averageMetricAchievement' => '0.75',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.0000',
                    'averageMetricCompletion' => '0.00000000',
                ],
                [
                    'id' => $this->coordinatorTwo->program->id,
                    'name' => $this->coordinatorTwo->program->name,
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'participantCount' => '3',
                    'minCompletedMission' => '0',
                    'maxCompletedMission' => '1',
                    'averageCompletedMission' => '0.3333',
                    'missionCount' => '2',
                    'minMetricAchievement' => '0',
                    'maxMetricAchievement' => '0.8625',
                    'averageMetricAchievement' => '0.43125',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.0000',
                    'averageMetricCompletion' => '0.00000000',
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_view_containOverAchievedMetric_capAtTarget()
    {
        $this->assignmentFieldValueFive_mar3af3->inputValue = 350;
        
        $this->view();
        $this->seeStatusCode(200);
        
        $response = [
            'data' => [
                [
                    'id' => $this->coordinatorOne->program->id,
                    'name' => $this->coordinatorOne->program->name,
                    'coordinatorId' => $this->coordinatorOne->id,
                    'participantCount' => '2',
                    'minCompletedMission' => '1',
                    'maxCompletedMission' => '2',
                    'averageCompletedMission' => '1.5000',
                    'missionCount' => '3',
                    'minMetricAchievement' => '0.8125',
                    'maxMetricAchievement' => '0.9375',
                    'averageMetricAchievement' => '0.875',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.5000',
                    'averageMetricCompletion' => '0.25000000',
                ],
                [
                    'id' => $this->coordinatorTwo->program->id,
                    'name' => $this->coordinatorTwo->program->name,
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'participantCount' => '3',
                    'minCompletedMission' => '0',
                    'maxCompletedMission' => '1',
                    'averageCompletedMission' => '0.3333',
                    'missionCount' => '2',
                    'minMetricAchievement' => '0',
                    'maxMetricAchievement' => '0.8625',
                    'averageMetricAchievement' => '0.43125',
                    'minMetricCompletion' => '0.0000',
                    'maxMetricCompletion' => '0.0000',
                    'averageMetricCompletion' => '0.00000000',
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    
}
