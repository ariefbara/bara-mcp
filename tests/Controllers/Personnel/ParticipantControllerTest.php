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
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMetric;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfMission;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\RecordOfWorksheetForm;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\Shared\RecordOfForm;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class ParticipantControllerTest extends PersonnelTestCase
{
    protected $viewSummaryListInCoordinatedProgramUri;
    protected $ListInCoordinatedProgramUri;
    protected $dedicatedMenteeListUri;
    
    protected $coordinatorOne;
    protected $coordinatorTwo;
    protected $coordinatorThree;
    
    protected $consultantOne;
    protected $consultantTwo;
    protected $consultantThree;
    
    protected $missionOneA;
    protected $missionOneB;
    protected $missionOneC;

    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $clientParticipantTwoA;
    protected $userParticipantThree;
    
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;
    protected $dedicatedMentorThree;

    protected $completedMissionOneA;
    protected $completedMissionOneB;
    
    protected $metricAssignmentTwo;
    protected $assignmentFieldTwoA;
    protected $assignmentFieldTwoB;
    
    protected $metricAssignmentReportTwo;
    protected $metricAssignmentReportTwo_previous;
    protected $assignmentFieldValueTwoA;
    protected $assignmentFieldValueTwoB;
    protected $assignmentFieldValueTwoA_previous;
    protected $assignmentFieldValueTwoB_previous;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        //
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        //
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('CompletedMission')->truncate();
        //
        $this->connection->table('Metric')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
        
        $this->viewSummaryListInCoordinatedProgramUri = $this->personnelUri . "/participant-summary-list-in-coordinated-program";
        $this->ListInCoordinatedProgramUri = $this->personnelUri . "/participant-list-in-coordinated-program";
        $this->dedicatedMenteeListUri = $this->personnelUri . "/dedicated-mentee-list";
        
        $firm = $this->personnel->firm;
        
        $programOne = new RecordOfProgram($firm, 1);
        $programTwo = new RecordOfProgram($firm, 2);
        $programThree = new RecordOfProgram($firm, 3);
        
        $this->coordinatorOne = new RecordOfCoordinator($programOne, $this->personnel, 1);
        $this->coordinatorTwo = new RecordOfCoordinator($programTwo, $this->personnel, 2);
        $this->coordinatorThree = new RecordOfCoordinator($programThree, $this->personnel, 3);
        
        $this->consultantOne = new RecordOfConsultant($programOne, $this->personnel, 1);
        $this->consultantTwo = new RecordOfConsultant($programTwo, $this->personnel, 2);
        $this->consultantThree = new RecordOfConsultant($programThree, $this->personnel, 3);
        
        //
        $formOne = new RecordOfForm(1);
        $formTwo = new RecordOfForm(2);
        $formThree = new RecordOfForm(3);
        
        $worksheetFormOne = new RecordOfWorksheetForm($firm, $formOne);
        $worksheetFormTwo = new RecordOfWorksheetForm($firm, $formTwo);
        $worksheetFormThree = new RecordOfWorksheetForm($firm, $formThree);
        
        $this->missionOneA = new RecordOfMission($programOne, $worksheetFormOne, 1, null);
        $this->missionOneA->published = true;
        $this->missionOneB = new RecordOfMission($programOne, $worksheetFormTwo, 2, null);
        $this->missionOneB->published = true;
        $this->missionOneC = new RecordOfMission($programOne, $worksheetFormThree, 3, null);
        $this->missionOneC->published = true;
        
        //
        $clientOne = new RecordOfClient($firm, 1);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        
        $userOne = new RecordOfUser(1);
        
        $participantOne = new RecordOfParticipant($programOne, 1);
        $participantTwo = new RecordOfParticipant($programTwo, 2);
        $participantTwoA = new RecordOfParticipant($programTwo, '2a');
        $participantThree = new RecordOfParticipant($programThree, 3);
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        $this->clientParticipantTwoA= new RecordOfClientParticipant($clientOne, $participantTwoA);
        $this->userParticipantThree = new RecordOfUserParticipant($userOne, $participantThree);
        
        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($participantOne, $this->consultantOne, 1);
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($participantTwo, $this->consultantTwo, 2);
        $this->dedicatedMentorThree = new RecordOfDedicatedMentor($participantOne, $this->consultantThree, 3);
        
        //
        $this->completedMissionOneA = new RecordOfCompletedMission($participantOne, $this->missionOneA, '1a');
        $this->completedMissionOneB = new RecordOfCompletedMission($participantOne, $this->missionOneB, '1b');
        $this->completedMissionOneC = new RecordOfCompletedMission($participantOne, $this->missionOneC, '1c');
        
        //
        $metricTwoA = new RecordOfMetric($programTwo, '2a');
        $metricTwoB = new RecordOfMetric($programTwo, '2b');
        
        $this->metricAssignmentTwo = new RecordOfMetricAssignment($participantTwo, 2);
        
        $this->assignmentFieldTwoA = new RecordOfAssignmentField($this->metricAssignmentTwo, $metricTwoA, '2a');
        $this->assignmentFieldTwoA->target = 100;
        $this->assignmentFieldTwoB = new RecordOfAssignmentField($this->metricAssignmentTwo, $metricTwoB, '2b');
        $this->assignmentFieldTwoB->target = 1000;
        
        $this->metricAssignmentReportTwo = new RecordOfMetricAssignmentReport($this->metricAssignmentTwo, 2);
        $this->metricAssignmentReportTwo->observationTime = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $this->metricAssignmentReportTwo->approved = true;
        $this->metricAssignmentReportTwo_previous = new RecordOfMetricAssignmentReport($this->metricAssignmentTwo, '2previous');
        $this->metricAssignmentReportTwo_previous->observationTime = (new DateTime('-2 months'))->format('Y-m-d H:i:s');
        $this->metricAssignmentReportTwo_previous->approved = true;
        
        $this->assignmentFieldValueTwoA = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo, $this->assignmentFieldTwoA, '2a');
        $this->assignmentFieldValueTwoA->inputValue = 60;
        $this->assignmentFieldValueTwoB = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo, $this->assignmentFieldTwoB, '2b');
        $this->assignmentFieldValueTwoB->inputValue = 1200;
        $this->assignmentFieldValueTwoA_previous = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo_previous, $this->assignmentFieldTwoA, '2aPrevious');
        $this->assignmentFieldValueTwoA_previous->inputValue = 30;
        $this->assignmentFieldValueTwoB_previous = new RecordOfAssignmentFieldValue($this->metricAssignmentReportTwo_previous, $this->assignmentFieldTwoB, '2bPrevious');
        $this->assignmentFieldValueTwoB_previous->inputValue = 300;
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();
        //
        $this->connection->table('Form')->truncate();
        $this->connection->table('WorksheetForm')->truncate();
        $this->connection->table('Mission')->truncate();
        //
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('CompletedMission')->truncate();
        //
        $this->connection->table('Metric')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('AssignmentField')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        $this->connection->table('AssignmentFieldValue')->truncate();
    }
    
    protected function viewSummaryListInCoordinatedProgram()
    {
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorTwo->program->insert($this->connection);
        $this->coordinatorThree->program->insert($this->connection);
        
        $this->coordinatorOne->insert($this->connection);
        $this->coordinatorTwo->insert($this->connection);
        $this->coordinatorThree->insert($this->connection);
        
        //
        $this->missionOneA->worksheetForm->form->insert($this->connection);
        $this->missionOneB->worksheetForm->form->insert($this->connection);
        $this->missionOneC->worksheetForm->form->insert($this->connection);
        
        $this->missionOneA->worksheetForm->insert($this->connection);
        $this->missionOneB->worksheetForm->insert($this->connection);
        $this->missionOneC->worksheetForm->insert($this->connection);
        
        $this->missionOneA->insert($this->connection);
        $this->missionOneB->insert($this->connection);
        $this->missionOneC->insert($this->connection);
        
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->clientParticipantTwoA->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        //
        $this->completedMissionOneA->insert($this->connection);
        $this->completedMissionOneB->insert($this->connection);
        
        //
        $this->metricAssignmentTwo->insert($this->connection);
        
        $this->assignmentFieldTwoA->metric->insert($this->connection);
        $this->assignmentFieldTwoB->metric->insert($this->connection);
        
        $this->assignmentFieldTwoA->insert($this->connection);
        $this->assignmentFieldTwoB->insert($this->connection);
        
        $this->metricAssignmentReportTwo->insert($this->connection);
        $this->metricAssignmentReportTwo_previous->insert($this->connection);
        
        $this->assignmentFieldValueTwoA->insert($this->connection);
        $this->assignmentFieldValueTwoB->insert($this->connection);
        $this->assignmentFieldValueTwoA_previous->insert($this->connection);
        $this->assignmentFieldValueTwoB_previous->insert($this->connection);
        
//echo $this->viewSummaryListInCoordinatedProgramUri;
        $this->get($this->viewSummaryListInCoordinatedProgramUri, $this->personnel->token);
    }
    public function test_viewSummaryListInCoordinatedProgram_200()
    {
$this->disableExceptionHandling();
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $response = [
//'print',
            'total' => '4',
            'list' => [
                [
                    'id' => $this->clientParticipantOne->participant->id,
                    'userId' => null,
                    'clientId' => $this->clientParticipantOne->client->id,
                    'teamId' => null,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                    //
                    'totalCompletedMission' => '2',
                    'totalMission' => '3',
                    'missionCompletion' => '67',
                    //
                    'normalizedAchievement' => null,
                    'achievement' => null,
                    'completedMetric' => null,
                    'totalAssignedMetric' => null,
                    //
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->coordinatorOne->program->id,
                    'programName' => $this->coordinatorOne->program->name,
                ],
                [
                    'id' => $this->teamParticipantTwo->participant->id,
                    'userId' => null,
                    'clientId' => null,
                    'teamId' => $this->teamParticipantTwo->team->id,
                    'name' => $this->teamParticipantTwo->team->name,
                    //
                    'totalCompletedMission' => null,
                    'totalMission' => null,
                    'missionCompletion' => null,
                    //
                    'normalizedAchievement' => '80',
                    'achievement' => '90',
                    'completedMetric' => '1',
                    'totalAssignedMetric' => '2',
                    //
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'programId' => $this->coordinatorTwo->program->id,
                    'programName' => $this->coordinatorTwo->program->name,
                ],
                [
                    'id' => $this->clientParticipantTwoA->participant->id,
                    'userId' => null,
                    'clientId' => $this->clientParticipantTwoA->client->id,
                    'teamId' => null,
                    'name' => $this->clientParticipantTwoA->client->getFullName(),
                    //
                    'totalCompletedMission' => null,
                    'totalMission' => null,
                    'missionCompletion' => null,
                    //
                    'normalizedAchievement' => null,
                    'achievement' => null,
                    'completedMetric' => null,
                    'totalAssignedMetric' => null,
                    //
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'programId' => $this->coordinatorTwo->program->id,
                    'programName' => $this->coordinatorTwo->program->name,
                ],
                [
                    'id' => $this->userParticipantThree->participant->id,
                    'userId' => $this->userParticipantThree->user->id,
                    'clientId' => null,
                    'teamId' => null,
                    'name' => $this->userParticipantThree->user->getFullName(),
                    //
                    'totalCompletedMission' => null,
                    'totalMission' => null,
                    'missionCompletion' => null,
                    //
                    'normalizedAchievement' => null,
                    'achievement' => null,
                    'completedMetric' => null,
                    'totalAssignedMetric' => null,
                    //
                    'coordinatorId' => $this->coordinatorThree->id,
                    'programId' => $this->coordinatorThree->program->id,
                    'programName' => $this->coordinatorThree->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewSummaryListInCoordinatedProgram_excludeInaccessibleParticipant_inactiveParticipant()
    {
        $this->clientParticipantTwoA->participant->active = false;
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_excludeInaccessibleParticipant_otherProgram()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        $this->clientParticipantTwoA->participant->program = $otherProgram;
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_excludeInaccessibleParticipant_inactiveCoordinator()
    {
        $this->coordinatorOne->active = false;
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_excludeUnpublishedMissionFromMissionCompletionCalculation()
    {
        $this->missionOneB->published = false;
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '4']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
        //
        $this->seeJsonContains([
            'totalCompletedMission' => '1',
            'totalMission' => '2',
            'missionCompletion' => '50'
        ]);
    }
    public function test_viewSummaryListInCoordinatedProgram_programIdFilter()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?programId={$this->coordinatorOne->program->id}";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_nameFilter()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?name=team";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_missionCompletionFromFilter()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?missionCompletionFrom=68";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '0']);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_missionCompletionToFilter()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?missionCompletionTo=66";
        $this->viewSummaryListInCoordinatedProgramUri .= "?missionCompletionTo=66";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_missionCompletionToFilterSetAsZero_showParticipantWithoutMissionCompletion_bug20230510()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?missionCompletionTo=0";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_missionCompletionFromSetAsZero_shouldIncludeParticipantWithousMissionCompletion_bug20230516()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?missionCompletionFrom=0";
        $this->viewSummaryListInCoordinatedProgramUri .= "&missionCompletionTo=88";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '4']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_metricAchievementFromFilter()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?metricAchievementFrom=81";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '0']);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_metricAchievementToFilter()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?metricAchievementTo=79";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_metricAchievementToFilterSetAsZero_showParticipantWithoutMetricAchievement_bug20230510()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?metricAchievementTo=0";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_metricAchievementFromToFilterSetAsZero_shouldIncludeParticipantWithoutMetricAchievement_bug20230516()
    {
        $this->viewSummaryListInCoordinatedProgramUri .= "?metricAchievementFrom=0";
        $this->viewSummaryListInCoordinatedProgramUri .= "&metricAchievementTo=98";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '4']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonContains(['id' => $this->userParticipantThree->id]);
    }
    public function test_viewSummaryListInCoordinatedProgram_allFilter()
    {
        $this->metricAssignmentTwo->participant = $this->clientParticipantOne->participant;
        
        $this->viewSummaryListInCoordinatedProgramUri .= 
                "?programId={$this->coordinatorOne->program->id}"
                . "&name=client"
                . "&missionCompletionFrom=67"
                . "&missionCompletionTo=67"
                . "&metricAchievementFrom=80"
                . "&metricAchievementTo=80";
        
        $this->viewSummaryListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
//// $this->seeJsonContains(['print']);
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['id' => $this->clientParticipantOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantTwoA->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantThree->id]);
    }
    
    protected function ListInCoordinatedProgram()
    {
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorTwo->program->insert($this->connection);
        
        $this->coordinatorOne->insert($this->connection);
        $this->coordinatorTwo->insert($this->connection);
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
//echo $this->ListInCoordinatedProgramUri;
        $this->get($this->ListInCoordinatedProgramUri, $this->personnel->token);
    }
    public function test_listInCoordinatedProgram_200()
    {
$this->disableExceptionHandling();
        $this->ListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains([
            'id' => $this->clientParticipantOne->participant->id,
            'name' => $this->clientParticipantOne->client->getFullName(),
        ]);
        $this->seeJsonContains([
            'id' => $this->teamParticipantTwo->participant->id,
            'name' => $this->teamParticipantTwo->team->name,
        ]);
    }
    public function test_listInCoordinatedProgram_excludeInacessibleParticipant_inNonCoordinatedProgram()
    {
        $this->userParticipantThree->participant->program->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        $this->ListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['id' => $this->clientParticipantOne->participant->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->participant->id]);
        $this->seeJsonDoesntContains(['id' => $this->userParticipantThree->participant->id]);
    }
    public function test_listInCoordinatedProgram_excludeInacessibleParticipant_inInactiveCoordinator()
    {
        $this->coordinatorOne->active = false;
        
        $this->ListInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->participant->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->participant->id]);
    }
    public function test_listInCoordinatedProgram_allFilter_200()
    {
$this->disableExceptionHandling();
        $this->ListInCoordinatedProgramUri .=
                "?programId={$this->coordinatorOne->program->id}"
                . "&name=client";
                
        $this->ListInCoordinatedProgram();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['print']);
        
        $this->seeJsonContains(['id' => $this->clientParticipantOne->participant->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->participant->id]);
    }
    
    protected function dedicatedMenteeList()
    {
        $this->consultantOne->program->insert($this->connection);
        $this->consultantTwo->program->insert($this->connection);
        
        $this->consultantOne->insert($this->connection);
        $this->consultantTwo->insert($this->connection);
        
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        
        $this->dedicatedMentorOne->insert($this->connection);
        $this->dedicatedMentorTwo->insert($this->connection);
        
        $this->get($this->dedicatedMenteeListUri, $this->personnel->token);
//echo $this->dedicatedMenteeListUri;
//$this->seeJsonContains(['print']);
    }
    public function test_dedicatedMenteeList_200()
    {
$this->disableExceptionHandling();
        $this->dedicatedMenteeList();
        $this->seeStatusCode(200);
        
        $response = [
            'list' => [
                [
                    'id' => $this->clientParticipantOne->participant->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                [
                    'id' => $this->teamParticipantTwo->participant->id,
                    'name' => $this->teamParticipantTwo->team->name,
                ],
            ],
        ];
    }
    public function test_dedicatedMenteeList_excludeInacessibleParticipant_nonDedicatedMentee()
    {
        $this->dedicatedMentorTwo->cancelled = true;
        
        $this->dedicatedMenteeList();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['id' => $this->clientParticipantOne->participant->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->participant->id]);
    }
    public function test_dedicatedMenteeList_excludeInacessibleParticipant_dedicatedToOtherMentor()
    {
        $otherPersonnel = new RecordOfPersonnel($this->personnel->firm, 'other');
        $otherPersonnel->insert($this->connection);
        $otherMentor = new RecordOfConsultant($this->clientParticipantOne->participant->program, $otherPersonnel, 'other');
        $otherMentor->insert($this->connection);
        
        $this->dedicatedMentorOne->consultant = $otherMentor;
        
        $this->dedicatedMenteeList();
        $this->seeStatusCode(200);
        
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->participant->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->participant->id]);
    }
    public function test_dedicatedMenteeList_excludeInacessibleParticipant_inInactiveConsultant()
    {
        $this->consultantOne->active = false;
        
        $this->dedicatedMenteeList();
        $this->seeStatusCode(200);
        
        $this->seeJsonDoesntContains(['id' => $this->clientParticipantOne->participant->id]);
        $this->seeJsonContains(['id' => $this->teamParticipantTwo->participant->id]);
    }
    public function test_dedicatedMenteeList_allFilter_200()
    {
$this->disableExceptionHandling();
        $this->dedicatedMenteeListUri .=
                "?programId={$this->consultantOne->program->id}"
                . "&name=client";
                
        $this->dedicatedMenteeList();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['print']);
        
        $this->seeJsonContains(['id' => $this->clientParticipantOne->participant->id]);
        $this->seeJsonDoesntContains(['id' => $this->teamParticipantTwo->participant->id]);
    }

}
