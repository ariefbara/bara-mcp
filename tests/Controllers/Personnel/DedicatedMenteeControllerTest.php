<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Worksheet\RecordOfCompletedMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;

class DedicatedMenteeControllerTest extends PersonnelTestCase
{

    protected $allWithSummaryUri;
    //
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;
    //
    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    //
    protected $missionOne_p1;
    protected $missionTwo_p1;
    protected $missionThree_p2;
    protected $missionFour_p2;
    protected $missionFive_p2;
    //
    protected $completedMissionOne_p1m1;
    //
    protected $metricAssignmentOne_p1;
    protected $assignmentFieldOne_ma1;
    protected $assignmentFieldTwo_ma1;
    
    protected $metricAssignmentReportOne_ma1p1;
    protected $metricAssignmentReportTwo_ma1p1;
    protected $assignmentFieldValueOne_mar1af1;
    protected $assignmentFieldValueTwo_mar1af2;
    protected $assignmentFieldValueThree_mar2af1;
    protected $assignmentFieldValueFour_mar2af2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        //
        $this->connection->table('Program')->truncate();
        $this->connection->table('Mission')->truncate();
        $this->connection->table('Metric')->truncate();
        //
        $this->connection->table('Participant')->truncate();
        $this->connection->table('CompletedMission')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
        //
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        //
        $this->connection->table('Client')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        //
        $this->connection->table('Team')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        //
        
        $this->allWithSummaryUri = $this->personnelUri . "/dedicated-mentees/summary";

        $firm = $this->personnel->firm;

        $programOne = new RecordOfProgram($firm, '1');
        $programTwo = new RecordOfProgram($firm, '2');

        $participantOne = new RecordOfParticipant($programOne, '1');
        $participantTwo = new RecordOfParticipant($programTwo, '2');

        $consultantOne = new RecordOfConsultant($programOne, $this->personnel, '1');
        $consultantTwo = new RecordOfConsultant($programTwo, $this->personnel, '2');

        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($participantOne, $consultantOne, '1');
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($participantTwo, $consultantTwo, '2');

        $clientOne = new RecordOfClient($firm, '1');
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);

        $teamOne = new RecordOfTeam($firm, $clientOne, '1');
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);

        $formOne = new RecordOfForm('1');
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);

        $this->missionOne_p1 = new RecordOfMission($programOne, $worksheetFormOne, '1', null);
        $this->missionOne_p1->published = true;
        $this->missionTwo_p1 = new RecordOfMission($programOne, $worksheetFormOne, '2', null);
        $this->missionTwo_p1->published = true;
        $this->missionThree_p2 = new RecordOfMission($programTwo, $worksheetFormOne, '3', null);
        $this->missionThree_p2->published = true;
        $this->missionFour_p2 = new RecordOfMission($programTwo, $worksheetFormOne, '4', null);
        $this->missionFour_p2->published = true;
        $this->missionFive_p2 = new RecordOfMission($programTwo, $worksheetFormOne, '5', null);
        $this->missionFive_p2->published = true;
        
        $this->completedMissionOne_p1m1 = new RecordOfCompletedMission($participantOne, $this->missionOne_p1, '1');
        
        $metricOne = new RecordOfMetric($programOne, '1');
        $metricTwo = new RecordOfMetric($programOne, '2');
        
        $this->metricAssignmentOne_p1 = new RecordOfMetricAssignment($participantOne, '1');
        
        $this->assignmentFieldOne_ma1 = new RecordOfAssignmentField($this->metricAssignmentOne_p1, $metricOne, '1');
        $this->assignmentFieldOne_ma1->target = 1000;
        $this->assignmentFieldTwo_ma1 = new RecordOfAssignmentField($this->metricAssignmentOne_p1, $metricTwo, '2');
        $this->assignmentFieldTwo_ma1->target = 100;
        
        $this->metricAssignmentReportOne_ma1p1 = new RecordOfMetricAssignmentReport($this->metricAssignmentOne_p1, '1');
        $this->metricAssignmentReportOne_ma1p1->approved = true;
        $this->metricAssignmentReportOne_ma1p1->observationTime = (new DateTime('-48 hours'));
        $this->assignmentFieldValueOne_mar1af1 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_ma1p1, $this->assignmentFieldOne_ma1, '1');
        $this->assignmentFieldValueOne_mar1af1->inputValue = 200;
        $this->assignmentFieldValueTwo_mar1af2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportOne_ma1p1, $this->assignmentFieldTwo_ma1, '2');
        $this->assignmentFieldValueTwo_mar1af2->inputValue = 50;
        
        $this->metricAssignmentReportTwo_ma1p1 = new RecordOfMetricAssignmentReport($this->metricAssignmentOne_p1, '2');
        $this->metricAssignmentReportTwo_ma1p1->approved = true;
        $this->metricAssignmentReportTwo_ma1p1->observationTime = (new DateTime('-24 hours'));
        $this->assignmentFieldValueThree_mar2af1 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo_ma1p1, $this->assignmentFieldOne_ma1, '3');
        $this->assignmentFieldValueThree_mar2af1->inputValue = 400;
        $this->assignmentFieldValueFour_mar2af2 = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo_ma1p1, $this->assignmentFieldTwo_ma1, '4');
        $this->assignmentFieldValueFour_mar2af2->inputValue = 80;
    }

    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table('Form')->truncate();
//        $this->connection->table('WorksheetForm')->truncate();
//        //
//        $this->connection->table('Program')->truncate();
//        $this->connection->table('Mission')->truncate();
//        $this->connection->table('Metric')->truncate();
//        //
//        $this->connection->table('Participant')->truncate();
//        $this->connection->table('CompletedMission')->truncate();
//        $this->connection->table('MetricAssignment')->truncate();
//        $this->connection->table('AssignmentField')->truncate();
//        $this->connection->table('MetricAssignmentReport')->truncate();
//        $this->connection->table('AssignmentFieldValue')->truncate();
//        //
//        $this->connection->table('Consultant')->truncate();
//        $this->connection->table('DedicatedMentor')->truncate();
//        //
//        $this->connection->table('Client')->truncate();
//        $this->connection->table('ClientParticipant')->truncate();
//        //
//        $this->connection->table('Team')->truncate();
//        $this->connection->table('TeamParticipant')->truncate();
    }
    
    protected function allWithSummary()
    {
        
        $this->dedicatedMentorOne->consultant->program->insert($this->connection);
        $this->dedicatedMentorTwo->consultant->program->insert($this->connection);
        //
        $this->dedicatedMentorOne->consultant->insert($this->connection);
        $this->dedicatedMentorTwo->consultant->insert($this->connection);
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        //
        $this->dedicatedMentorOne->insert($this->connection);
        $this->dedicatedMentorTwo->insert($this->connection);
        //
        $this->missionOne_p1->worksheetForm->form->insert($this->connection);
        $this->missionOne_p1->worksheetForm->insert($this->connection);
        //
        $this->missionOne_p1->insert($this->connection);
        $this->missionTwo_p1->insert($this->connection);
        $this->missionThree_p2->insert($this->connection);
        $this->missionFour_p2->insert($this->connection);
        $this->missionFive_p2->insert($this->connection);
        $this->completedMissionOne_p1m1->insert($this->connection);
        //
        $this->metricAssignmentOne_p1->insert($this->connection);
        $this->assignmentFieldOne_ma1->metric->insert($this->connection);
        $this->assignmentFieldTwo_ma1->metric->insert($this->connection);
        $this->assignmentFieldOne_ma1->insert($this->connection);
        $this->assignmentFieldTwo_ma1->insert($this->connection);
        //
        $this->metricAssignmentReportOne_ma1p1->insert($this->connection);
        $this->metricAssignmentReportTwo_ma1p1->insert($this->connection);
        $this->assignmentFieldValueOne_mar1af1->insert($this->connection);
        $this->assignmentFieldValueTwo_mar1af2->insert($this->connection);
        $this->assignmentFieldValueThree_mar2af1->insert($this->connection);
        $this->assignmentFieldValueFour_mar2af2->insert($this->connection);
        //
        $this->get($this->allWithSummaryUri, $this->personnel->token);
    }
    public function test_allWithSummary_200()
    {
$this->disableExceptionHandling();
        $this->allWithSummary();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '2',
            'list' => [
                [
                    'participantId' => $this->dedicatedMentorOne->participant->id,
                    'participantName' => $this->clientParticipantOne->client->getFullName(),
                    'totalCompletedMission' => '1',
                    'totalMission' => '2',
                    'metricAchievement' => '60',
                    'completedMetric' => '0',
                    'totalAssignedMetric' => '2',
                    'programId' => $this->dedicatedMentorOne->participant->program->id,
                    'programConsultationId' => $this->dedicatedMentorOne->consultant->id,
                    'participantType' => 'client'
                ],
                [
                    'participantId' => $this->dedicatedMentorTwo->participant->id,
                    'participantName' => $this->teamParticipantTwo->team->name,
                    'totalCompletedMission' => null,
                    'totalMission' => '3',
                    'metricAchievement' => null,
                    'completedMetric' => null,
                    'totalAssignedMetric' => null,
                    'programId' => $this->dedicatedMentorTwo->participant->program->id,
                    'programConsultationId' => $this->dedicatedMentorTwo->consultant->id,
                    'participantType' => 'team'
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_allWithSummary_excludeCancelledDedicatedMentee()
    {
        $this->dedicatedMentorOne->cancelled = true;
        $this->allWithSummary();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['participantId' => $this->dedicatedMentorOne->participant->id]);
        $this->seeJsonContains(['participantId' => $this->dedicatedMentorTwo->participant->id]);
    }
    public function test_allWithSummary_excludeInactiveConsultant()
    {
        $this->dedicatedMentorOne->consultant->active = false;
        $this->allWithSummary();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['participantId' => $this->dedicatedMentorOne->participant->id]);
        $this->seeJsonContains(['participantId' => $this->dedicatedMentorTwo->participant->id]);
    }
    public function test_allWithSummary_excludeInactiveParticipant()
    {
        $this->dedicatedMentorOne->participant->active = false;
        $this->allWithSummary();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['participantId' => $this->dedicatedMentorOne->participant->id]);
        $this->seeJsonContains(['participantId' => $this->dedicatedMentorTwo->participant->id]);
    }
    public function test_allWithSummary_excludeUnapprovedReport()
    {
        $this->metricAssignmentReportTwo_ma1p1->approved = false;
        $this->allWithSummary();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'participantId' => $this->dedicatedMentorOne->participant->id,
            'totalCompletedMission' => '1',
            'totalMission' => '2',
            'metricAchievement' => '35',
            'completedMetric' => '0',
            'totalAssignedMetric' => '2',
        ]);
    }

}
