<?php

namespace Tests\Controllers\Personnel\AsProgramCoordinator;

use DateTime;
use Tests\Controllers\RecordPreparation\ {
    Firm\Client\RecordOfClientParticipant,
    Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\RecordOfAssignmentFieldValue,
    Firm\Program\Participant\MetricAssignment\RecordOfAssignmentField,
    Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport,
    Firm\Program\Participant\RecordOfMetricAssignment,
    Firm\Program\Participant\Worksheet\RecordOfCompletedMission,
    Firm\Program\RecordOfMetric,
    Firm\Program\RecordOfMission,
    Firm\Program\RecordOfParticipant,
    Firm\RecordOfClient,
    Firm\RecordOfTeam,
    Firm\RecordOfWorksheetForm,
    Firm\Team\RecordOfTeamProgramParticipation,
    RecordOfUser,
    Shared\RecordOfForm,
    User\RecordOfUserParticipant
};

class ParticipantSummaryControllerTest extends AsProgramCoordinatorTestCase
{
    protected $participantSummaryUri;
    protected $participant_client;
    protected $participantOne_team;
    protected $participantTwo_clientinactive;
    protected $participantThree_user;
    protected $clientParticipant;
    protected $teamParticipant;
    protected $clientParticipant_inactive;
    protected $userParticipant;
    protected $mission;
    protected $missionOne;
    protected $missionTwo;
    protected $completedMission_00;
    protected $completedMission_01;
    protected $completedMission_02;
    protected $completedMission_20;
    protected $completedMission_30;
    protected $completedMission_32;
    
/**
 * participant One has no metric assignment
 * participant two never submit assignment report
 */
    protected $participantMetricAssignment_0;
    protected $participantTwoMetricAssignment_2;
    protected $participantThreeMetricAssignment_3;

    protected $fieldOne_ofParticipantMetricAssignment_01;
    protected $fieldTwo_ofParticipantMetricAssignment_02_removed;
    protected $fieldThree_ofParticipantMetricAssignment_03;
    protected $fieldOne_ofParticipantTwoMetricAssignment_21;
    protected $fieldTwo_ofParticipantTwoMetricAssignment_22_removed;
    protected $fieldThree_ofParticipantTwoMetricAssignment_23;
    protected $fieldOne_ofParticipantThreeMetricAssignment_31;
    protected $fieldTwo_ofParticipantThreeMetricAssignment_32;
    protected $fieldThree_ofParticipantThreeMetricAssignment_33_removed;
    
    protected $assignmentReportOne_fromParticipant_01_approved;
    protected $assignmentReportOne_fromParticipantThree_31_approved;
    protected $assignmentReportTwo_fromParticipantThree_32_approved;
    protected $assignmentReportThree_fromParticipantThree_33_approved_removed;
    
    protected $value_forFieldOne_assignmentReport01_011;
    protected $value_forFieldTwo_assignmentReport01_012;
    protected $value_forFieldThree_assignmentReport01_013;
    protected $value_forFieldOne_assignmentReport31_311;
    protected $value_forFieldTwo_assignmentReport31_312;
    protected $value_forFieldThree_assignmentReport31_313;
    protected $value_forFieldOne_assignmentReport32_321;
    protected $value_forFieldTwo_assignmentReport32_322;
    protected $value_forFieldThree_assignmentReport32_323;
    protected $value_forFieldOne_assignmentReport33_331;
    protected $value_forFieldTwo_assignmentReport33_332;
    protected $value_forFieldThree_assignmentReport33_333;

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantSummaryUri = $this->asProgramCoordinatorUri . "/participant-summary";
        
        $this->connection->table("Client")->truncate();
        $this->connection->table("User")->truncate();
        $this->connection->table("Team")->truncate();
        $this->connection->table("Form")->truncate();
        $this->connection->table("WorksheetForm")->truncate();
        $this->connection->table("Mission")->truncate();
        $this->connection->table("Participant")->truncate();
        $this->connection->table("ClientParticipant")->truncate();
        $this->connection->table("TeamParticipant")->truncate();
        $this->connection->table("UserParticipant")->truncate();
        $this->connection->table("CompletedMission")->truncate();
        $this->connection->table("Metric")->truncate();
        $this->connection->table("MetricAssignment")->truncate();
        $this->connection->table("AssignmentField")->truncate();
        $this->connection->table("MetricAssignmentReport")->truncate();
        $this->connection->table("AssignmentFieldValue")->truncate();
        
        $program = $this->coordinator->program;
        $firm = $program->firm;
        
        $this->participant_client = new RecordOfParticipant($program, 0);
        $this->participantOne_team = new RecordOfParticipant($program, 1);
        $this->participantTwo_clientinactive = new RecordOfParticipant($program, 2);
        $this->participantTwo_clientinactive->active = false;
        $this->participantThree_user = new RecordOfParticipant($program, 3);
        $this->connection->table("Participant")->insert($this->participant_client->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantOne_team->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantTwo_clientinactive->toArrayForDbEntry());
        $this->connection->table("Participant")->insert($this->participantThree_user->toArrayForDbEntry());
        
        $client = new RecordOfClient($firm, 0);
        $clientOne = new RecordOfClient($firm, 1);
        $this->connection->table("Client")->insert($client->toArrayForDbEntry());
        $this->connection->table("Client")->insert($clientOne->toArrayForDbEntry());
        
        $team = new RecordOfTeam($firm, $clientOne, 0);
        $this->connection->table("Team")->insert($team->toArrayForDbEntry());
        
        $user = new RecordOfUser(0);
        $this->connection->table("User")->insert($user->toArrayForDbEntry());
        
        $this->clientParticipant = new RecordOfClientParticipant($client, $this->participant_client);
        $this->clientParticipant_inactive = new RecordOfClientParticipant($clientOne, $this->participantTwo_clientinactive);
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant->toArrayForDbEntry());
        $this->connection->table("ClientParticipant")->insert($this->clientParticipant_inactive->toArrayForDbEntry());
        
        $this->teamParticipant = new RecordOfTeamProgramParticipation($team, $this->participantOne_team);
        $this->connection->table("TeamParticipant")->insert($this->teamParticipant->toArrayForDbEntry());
        
        $this->userParticipant = new RecordOfUserParticipant($user, $this->participantThree_user);
        $this->connection->table("UserParticipant")->insert($this->userParticipant->toArrayForDbEntry());
        
        $form = new RecordOfForm(0);
        $this->connection->table("Form")->insert($form->toArrayForDbEntry());
        
        $worksheetForm = new RecordOfWorksheetForm($firm, $form);
        $this->connection->table("WorksheetForm")->insert($worksheetForm->toArrayForDbEntry());
        
        $this->mission = new RecordOfMission($program, $worksheetForm, 0, null);
        $this->mission->published = true;
        $this->missionOne = new RecordOfMission($program, $worksheetForm, 1, null);
        $this->missionOne->published = true;
        $this->missionTwo = new RecordOfMission($program, $worksheetForm, 2, null);
        $this->missionTwo->published = true;
        $this->connection->table("Mission")->insert($this->mission->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionOne->toArrayForDbEntry());
        $this->connection->table("Mission")->insert($this->missionTwo->toArrayForDbEntry());
        
        $this->completedMission_00 = new RecordOfCompletedMission($this->participant_client, $this->mission, "00");
        $this->completedMission_00->completedTime = (new DateTime("-48 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_01 = new RecordOfCompletedMission($this->participant_client, $this->missionOne, "01");
        $this->completedMission_01->completedTime = (new DateTime("-12 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_02 = new RecordOfCompletedMission($this->participant_client, $this->missionTwo, "02");
        $this->completedMission_02->completedTime = (new DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_20 = new RecordOfCompletedMission($this->participantTwo_clientinactive, $this->mission, "20");
        $this->completedMission_20->completedTime = (new DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_30 = new RecordOfCompletedMission($this->participantThree_user, $this->mission, "30");
        $this->completedMission_30->completedTime = (new DateTime("-24 hours"))->format("Y-m-d H:i:s");
        $this->completedMission_32 = new RecordOfCompletedMission($this->participantThree_user, $this->missionTwo, "32");
        $this->completedMission_32->completedTime = (new DateTime("-48 hours"))->format("Y-m-d H:i:s");
        $this->connection->table("CompletedMission")->insert($this->completedMission_00->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_01->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_02->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_20->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_30->toArrayForDbEntry());
        $this->connection->table("CompletedMission")->insert($this->completedMission_32->toArrayForDbEntry());
        
        $metric = new RecordOfMetric($program, 0);
        $this->connection->table("Metric")->insert($metric->toArrayForDbEntry());
        
        $this->participantMetricAssignment_0 = new RecordOfMetricAssignment($this->participant_client, 0);
        $this->participantTwoMetricAssignment_2 = new RecordOfMetricAssignment($this->participantTwo_clientinactive, 2);
        $this->participantThreeMetricAssignment_3 = new RecordOfMetricAssignment($this->participantThree_user, 3);
        $this->connection->table("MetricAssignment")->insert($this->participantMetricAssignment_0->toArrayForDbEntry());
        $this->connection->table("MetricAssignment")->insert($this->participantTwoMetricAssignment_2->toArrayForDbEntry());
        $this->connection->table("MetricAssignment")->insert($this->participantThreeMetricAssignment_3->toArrayForDbEntry());
        
        $this->fieldOne_ofParticipantMetricAssignment_01 = new RecordOfAssignmentField($this->participantMetricAssignment_0, $metric, "01");
        $this->fieldOne_ofParticipantMetricAssignment_01->target = 100;
        $this->fieldTwo_ofParticipantMetricAssignment_02_removed = new RecordOfAssignmentField($this->participantMetricAssignment_0, $metric, "02");
        $this->fieldTwo_ofParticipantMetricAssignment_02_removed->removed = true;
        $this->fieldTwo_ofParticipantMetricAssignment_02_removed->target = 200;
        $this->fieldThree_ofParticipantMetricAssignment_03 = new RecordOfAssignmentField($this->participantMetricAssignment_0, $metric, "03");
        $this->fieldThree_ofParticipantMetricAssignment_03->target = 300;
        $this->fieldOne_ofParticipantTwoMetricAssignment_21 = new RecordOfAssignmentField($this->participantTwoMetricAssignment_2, $metric, "21");
        $this->fieldOne_ofParticipantTwoMetricAssignment_21->target = 100;
        $this->fieldTwo_ofParticipantTwoMetricAssignment_22_removed = new RecordOfAssignmentField($this->participantTwoMetricAssignment_2, $metric, "22");
        $this->fieldTwo_ofParticipantTwoMetricAssignment_22_removed->removed = true;
        $this->fieldTwo_ofParticipantTwoMetricAssignment_22_removed->target = 200;
        $this->fieldThree_ofParticipantTwoMetricAssignment_23 = new RecordOfAssignmentField($this->participantTwoMetricAssignment_2, $metric, "23");
        $this->fieldThree_ofParticipantTwoMetricAssignment_23->target = 300;
        $this->fieldOne_ofParticipantThreeMetricAssignment_31 = new RecordOfAssignmentField($this->participantThreeMetricAssignment_3, $metric, "31");
        $this->fieldOne_ofParticipantThreeMetricAssignment_31->removed = true;
        $this->fieldOne_ofParticipantThreeMetricAssignment_31->target = 100;
        $this->fieldTwo_ofParticipantThreeMetricAssignment_32 = new RecordOfAssignmentField($this->participantThreeMetricAssignment_3, $metric, "32");
        $this->fieldTwo_ofParticipantThreeMetricAssignment_32->target = 200;
        $this->fieldThree_ofParticipantThreeMetricAssignment_33_removed = new RecordOfAssignmentField($this->participantThreeMetricAssignment_3, $metric, "33");
        $this->fieldThree_ofParticipantThreeMetricAssignment_33_removed->removed = true;
        $this->fieldThree_ofParticipantThreeMetricAssignment_33_removed->target = 300;
        $this->connection->table("AssignmentField")->insert($this->fieldOne_ofParticipantMetricAssignment_01->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->fieldTwo_ofParticipantMetricAssignment_02_removed->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->fieldThree_ofParticipantMetricAssignment_03->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->fieldOne_ofParticipantTwoMetricAssignment_21->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->fieldTwo_ofParticipantTwoMetricAssignment_22_removed->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->fieldThree_ofParticipantTwoMetricAssignment_23->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->fieldOne_ofParticipantThreeMetricAssignment_31->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->fieldTwo_ofParticipantThreeMetricAssignment_32->toArrayForDbEntry());
        $this->connection->table("AssignmentField")->insert($this->fieldThree_ofParticipantThreeMetricAssignment_33_removed->toArrayForDbEntry());
        
        $this->assignmentReportOne_fromParticipant_01_approved = new RecordOfMetricAssignmentReport($this->participantMetricAssignment_0,"01");
        $this->assignmentReportOne_fromParticipant_01_approved->approved = true;
        $this->assignmentReportOne_fromParticipantThree_31_approved = new RecordOfMetricAssignmentReport($this->participantThreeMetricAssignment_3,"31");
        $this->assignmentReportOne_fromParticipantThree_31_approved->approved = true;
        $this->assignmentReportOne_fromParticipantThree_31_approved->observationTime = (new \DateTimeImmutable("-14 days"))->format("Y-m-d H:i:s");
        $this->assignmentReportTwo_fromParticipantThree_32_approved = new RecordOfMetricAssignmentReport($this->participantThreeMetricAssignment_3,"32");
        $this->assignmentReportTwo_fromParticipantThree_32_approved->approved = true;
        $this->assignmentReportTwo_fromParticipantThree_32_approved->observationTim = (new \DateTimeImmutable("-7 days"))->format("Y-m-d H:i:s");
        $this->assignmentReportThree_fromParticipantThree_33_approved_removed = new RecordOfMetricAssignmentReport($this->participantThreeMetricAssignment_3,"33");
        $this->assignmentReportThree_fromParticipantThree_33_approved_removed->approved = true;
        $this->assignmentReportThree_fromParticipantThree_33_approved_removed->removed = true;
        $this->connection->table("MetricAssignmentReport")->insert($this->assignmentReportOne_fromParticipant_01_approved->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->assignmentReportOne_fromParticipantThree_31_approved->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->assignmentReportTwo_fromParticipantThree_32_approved->toArrayForDbEntry());
        $this->connection->table("MetricAssignmentReport")->insert($this->assignmentReportThree_fromParticipantThree_33_approved_removed->toArrayForDbEntry());
        
        $this->value_forFieldOne_assignmentReport01_011 = new RecordOfAssignmentFieldValue( 
                $this->assignmentReportOne_fromParticipant_01_approved, $this->fieldOne_ofParticipantMetricAssignment_01, "011");
        $this->value_forFieldOne_assignmentReport01_011->inputValue = 10;
        $this->value_forFieldTwo_assignmentReport01_012 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportOne_fromParticipant_01_approved, $this->fieldTwo_ofParticipantMetricAssignment_02_removed, "012");
        $this->value_forFieldTwo_assignmentReport01_012->inputValue = 20;
        $this->value_forFieldThree_assignmentReport01_013 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportOne_fromParticipant_01_approved, $this->fieldThree_ofParticipantMetricAssignment_03, "013");
        $this->value_forFieldThree_assignmentReport01_013->inputValue = 30;
        $this->value_forFieldOne_assignmentReport31_311 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportOne_fromParticipantThree_31_approved, $this->fieldOne_ofParticipantThreeMetricAssignment_31, "311");
        $this->value_forFieldOne_assignmentReport31_311->inputValue = 30;
        $this->value_forFieldTwo_assignmentReport31_312 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportOne_fromParticipantThree_31_approved, $this->fieldTwo_ofParticipantThreeMetricAssignment_32, "312");
        $this->value_forFieldTwo_assignmentReport31_312->inputValue = 60;
        $this->value_forFieldThree_assignmentReport31_313 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportOne_fromParticipantThree_31_approved, $this->fieldThree_ofParticipantThreeMetricAssignment_33_removed, "313");
        $this->value_forFieldThree_assignmentReport31_313->inputValue = 90;
        $this->value_forFieldOne_assignmentReport32_321 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportTwo_fromParticipantThree_32_approved, $this->fieldOne_ofParticipantThreeMetricAssignment_31, "321");
        $this->value_forFieldOne_assignmentReport32_321->inputValue = 60;
        $this->value_forFieldTwo_assignmentReport32_322 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportTwo_fromParticipantThree_32_approved, $this->fieldTwo_ofParticipantThreeMetricAssignment_32, "322");
        $this->value_forFieldTwo_assignmentReport32_322->inputValue = 120;
        $this->value_forFieldThree_assignmentReport32_323 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportTwo_fromParticipantThree_32_approved, $this->fieldThree_ofParticipantThreeMetricAssignment_33_removed, "323");
        $this->value_forFieldThree_assignmentReport32_323->inputValue = 180;
        $this->value_forFieldOne_assignmentReport33_331 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportThree_fromParticipantThree_33_approved_removed, $this->fieldOne_ofParticipantThreeMetricAssignment_31, "331");
        $this->value_forFieldOne_assignmentReport33_331->inputValue = 90;
        $this->value_forFieldTwo_assignmentReport33_332 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportThree_fromParticipantThree_33_approved_removed, $this->fieldTwo_ofParticipantThreeMetricAssignment_32, "332");
        $this->value_forFieldTwo_assignmentReport33_332->inputValue = 180;
        $this->value_forFieldThree_assignmentReport33_333 = new RecordOfAssignmentFieldValue(
                $this->assignmentReportThree_fromParticipantThree_33_approved_removed, $this->fieldThree_ofParticipantThreeMetricAssignment_33_removed, "333");
        $this->value_forFieldThree_assignmentReport33_333->inputValue = 270;
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldOne_assignmentReport01_011->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldTwo_assignmentReport01_012->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldThree_assignmentReport01_013->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldOne_assignmentReport31_311->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldTwo_assignmentReport31_312->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldThree_assignmentReport31_313->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldOne_assignmentReport32_321->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldTwo_assignmentReport32_322->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldThree_assignmentReport32_323->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldOne_assignmentReport33_331->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldTwo_assignmentReport33_332->toArrayForDbEntry());
        $this->connection->table("AssignmentFieldValue")->insert($this->value_forFieldThree_assignmentReport33_333->toArrayForDbEntry());
    }
    
    protected function tearDown(): void
    {
//        parent::tearDown();
//        $this->connection->table("Client")->truncate();
//        $this->connection->table("User")->truncate();
//        $this->connection->table("Team")->truncate();
//        $this->connection->table("Form")->truncate();
//        $this->connection->table("WorksheetForm")->truncate();
//        $this->connection->table("Mission")->truncate();
//        $this->connection->table("Participant")->truncate();
//        $this->connection->table("ClientParticipant")->truncate();
//        $this->connection->table("TeamParticipant")->truncate();
//        $this->connection->table("UserParticipant")->truncate();
//        $this->connection->table("CompletedMission")->truncate();
//        $this->connection->table("Metric")->truncate();
//        $this->connection->table("MetricAssignment")->truncate();
//        $this->connection->table("AssignmentField")->truncate();
//        $this->connection->table("MetricAssignmentReport")->truncate();
//        $this->connection->table("AssignmentFieldValue")->truncate();
    }
    
    public function test_showAll_200()
    {
        $totalResponse = [
            "total" => 3,
        ];
        $participantResponse = [
            "id" => $this->participant_client->id,
            "name" => $this->clientParticipant->client->getFullName(),
            "totalCompletedMission" => "3",
            "totalMission" => "3",
            "lastCompletedTime" => $this->completedMission_01->completedTime,
            "lastMissionId" => $this->missionOne->id,
            "lastMissionName" => $this->missionOne->name,
        ];
        $participantOneResponse = [
            "id" => $this->participantOne_team->id,
            "name" => $this->teamParticipant->team->name,
            "totalCompletedMission" => null,
            "totalMission" => "3",
            "lastCompletedTime" => null,
            "lastMissionId" => null,
            "lastMissionName" => null,
        ];
        $participantThreeResponse = [
            "id" => $this->participantThree_user->id,
            "name" => $this->userParticipant->user->getFullName(),
            "totalCompletedMission" => "2",
            "totalMission" => "3",
            "lastCompletedTime" => $this->completedMission_30->completedTime,
            "lastMissionId" => $this->mission->id,
            "lastMissionName" => $this->mission->name,
        ];
        
        $this->get($this->participantSummaryUri, $this->coordinator->personnel->token)
                ->seeJsonContains($totalResponse)
                ->seeJsonContains($participantResponse)
                ->seeJsonContains($participantOneResponse)
                ->seeJsonContains($participantThreeResponse)
                ->seeStatusCode(200);
    }
    
    public function test_showAll_paginationSet()
    {
        $response = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->participantOne_team->id,
                    "name" => $this->teamParticipant->team->name,
                    "totalCompletedMission" => null,
                    "totalMission" => "3",
                    "lastCompletedTime" => null,
                    "lastMissionId" => null,
                    "lastMissionName" => null,
                ],
            ],
        ];
        $uri = $this->participantSummaryUri . "?page=2&pageSize=2";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($response)
                ->seeStatusCode(200);
    }
    public function test_showAll_inactiveCoordinator_403()
    {
        $this->get($this->participantSummaryUri, $this->removedCoordinator->personnel->token)
                ->seeStatusCode(403);
    }
    
    public function test_showAllMetricAchievement_200()
    {
        $reponse = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->clientParticipant->participant->id,
                    "name" => $this->clientParticipant->client->getFullName(),
                    "achievement" => "10",
                ],
                [
                    "id" => $this->teamParticipant->participant->id,
                    "name" => $this->teamParticipant->team->name,
                    "achievement" => null,
                ],
                [
                    "id" => $this->userParticipant->participant->id,
                    "name" => $this->userParticipant->user->getFullName(),
                    "achievement" => "60",
                ],
            ],
        ];
        
        $uri = $this->asProgramCoordinatorUri . "/participant-achievement-summary";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($reponse)
                ->seeStatusCode(200);
    }
    public function test_showAllMetric_pageSizeApplied()
    {
        $reponse = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->userParticipant->participant->id,
                    "name" => $this->userParticipant->user->getFullName(),
                    "achievement" => "60",
                ],
            ],
        ];
        
        $uri = $this->asProgramCoordinatorUri . "/participant-achievement-summary?page=1&pageSize=1";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($reponse)
                ->seeStatusCode(200);
    }
    public function test_showAllMetric_pageSizeAndOrderApplied()
    {
        $reponse = [
            "total" => 3,
            "list" => [
                [
                    "id" => $this->clientParticipant->participant->id,
                    "name" => $this->clientParticipant->client->getFullName(),
                    "achievement" => "10",
                ],
                [
                    "id" => $this->teamParticipant->participant->id,
                    "name" => $this->teamParticipant->team->name,
                    "achievement" => null,
                ],
            ],
        ];
        
        $uri = $this->asProgramCoordinatorUri . "/participant-achievement-summary?page=1&pageSize=2&ascOrder=true";
        $this->get($uri, $this->coordinator->personnel->token)
                ->seeJsonContains($reponse)
                ->seeStatusCode(200);
    }
}
