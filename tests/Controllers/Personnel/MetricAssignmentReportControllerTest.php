<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class MetricAssignmentReportControllerTest extends PersonnelTestCase
{
    protected $coordinatorOne;
    protected $coordinatorTwo;
    protected $coordinatorThree;
    
    protected $consultantOne;
    protected $consultantTwo;
    protected $consultantThree;

    protected $clientParticipantOne;
    protected $teamParticipantTwo;
    protected $userParticipantThree;
    
    protected $metricAssignmentReportOne;
    protected $metricAssignmentReportTwo;
    protected $metricAssignmentReportThree;
    
    protected $viewListInCoordinatedProgramsUri;
    protected $viewListInConsultedProgramsUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Consultant')->truncate();
        //
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        
        $this->viewListInConsultedProgramsUri = $this->personnelUri . "/metric-assignment-report-list-in-consulted-programs";
        $this->viewListInCoordinatedProgramsUri = $this->personnelUri . "/metric-assignment-report-list-in-coordinated-programs";
        
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
        $clientOne = new RecordOfClient($firm, 1);
        
        $teamOne = new RecordOfTeam($firm, $clientOne, 1);
        
        $userOne = new RecordOfUser(1);
        
        $participantOne = new RecordOfParticipant($programOne, 1);
        $participantTwo = new RecordOfParticipant($programTwo, 2);
        $participantThree = new RecordOfParticipant($programThree, 3);
        
        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);
        
        $this->teamParticipantTwo = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);
        
        $this->userParticipantThree = new RecordOfUserParticipant($userOne, $participantThree);
        
        //
        $metricAssignmentOne = new RecordOfMetricAssignment($participantOne, 1);
        $metricAssignmentTwo = new RecordOfMetricAssignment($participantTwo, 2);
        $metricAssignmentThree = new RecordOfMetricAssignment($participantThree, 3);
        
        $this->metricAssignmentReportOne = new RecordOfMetricAssignmentReport($metricAssignmentOne, 1);
        $this->metricAssignmentReportTwo = new RecordOfMetricAssignmentReport($metricAssignmentTwo, 2);
        $this->metricAssignmentReportThree = new RecordOfMetricAssignmentReport($metricAssignmentThree, 3);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Program')->truncate();
        $this->connection->table('Coordinator')->truncate();
        $this->connection->table('Consultant')->truncate();
        //
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        //
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
    }
    
    protected function viewListInCoordinatedPrograms()
    {
        $this->coordinatorOne->program->insert($this->connection);
        $this->coordinatorTwo->program->insert($this->connection);
        $this->coordinatorThree->program->insert($this->connection);
        
        $this->coordinatorOne->insert($this->connection);
        $this->coordinatorTwo->insert($this->connection);
        $this->coordinatorThree->insert($this->connection);
        
        //
        $this->clientParticipantOne->client->insert($this->connection);
        $this->teamParticipantTwo->team->insert($this->connection);
        $this->userParticipantThree->user->insert($this->connection);
        
        $this->clientParticipantOne->insert($this->connection);
        $this->teamParticipantTwo->insert($this->connection);
        $this->userParticipantThree->insert($this->connection);
        
        //
        $this->metricAssignmentReportOne->metricAssignment->insert($this->connection);
        $this->metricAssignmentReportTwo->metricAssignment->insert($this->connection);
        $this->metricAssignmentReportThree->metricAssignment->insert($this->connection);
        
        $this->metricAssignmentReportOne->insert($this->connection);
        $this->metricAssignmentReportTwo->insert($this->connection);
        $this->metricAssignmentReportThree->insert($this->connection);
        
echo $this->viewListInCoordinatedProgramsUri;
        $this->get($this->viewListInCoordinatedProgramsUri, $this->personnel->token);
    }
    public function test_viewListInCoordinatedPrograms_200()
    {
$this->disableExceptionHandling();
        $this->viewListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'id' => $this->metricAssignmentReportOne->id,
                    'observationTime' => $this->metricAssignmentReportOne->observationTime,
                    'submitTime' => $this->metricAssignmentReportOne->submitTime,
                    'approved' => $this->metricAssignmentReportOne->approved,
                    //
                    'participantId' => $this->clientParticipantOne->participant->id,
                    'participantName' => $this->clientParticipantOne->client->getFullName(),
                    //
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->coordinatorOne->program->id,
                    'programName' => $this->coordinatorOne->program->name,
                ],
                [
                    'id' => $this->metricAssignmentReportTwo->id,
                    'observationTime' => $this->metricAssignmentReportTwo->observationTime,
                    'submitTime' => $this->metricAssignmentReportTwo->submitTime,
                    'approved' => $this->metricAssignmentReportTwo->approved,
                    //
                    'participantId' => $this->teamParticipantTwo->participant->id,
                    'participantName' => $this->teamParticipantTwo->team->name,
                    //
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'programId' => $this->coordinatorTwo->program->id,
                    'programName' => $this->coordinatorTwo->program->name,
                ],
                [
                    'id' => $this->metricAssignmentReportThree->id,
                    'observationTime' => $this->metricAssignmentReportThree->observationTime,
                    'submitTime' => $this->metricAssignmentReportThree->submitTime,
                    'approved' => $this->metricAssignmentReportThree->approved,
                    //
                    'participantId' => $this->userParticipantThree->participant->id,
                    'participantName' => $this->userParticipantThree->user->getFullName(),
                    //
                    'coordinatorId' => $this->coordinatorThree->id,
                    'programId' => $this->coordinatorThree->program->id,
                    'programName' => $this->coordinatorThree->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewListInCoordinatedProgram_excludeInacessibleReport_belongsToOtherProgram()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);
        
        $this->clientParticipantOne->participant->program = $otherProgram;
        
        $this->viewListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewListInCoordinatedProgram_excludeInacessibleReport_belongsToInactiveParticipant()
    {
        $this->clientParticipantOne->participant->active = false;
        
        $this->viewListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewListInCoordinatedProgram_excludeInacessibleReport_inactiveCoordinator()
    {
        $this->coordinatorOne->active = false;
        
        $this->viewListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewListInCoordinatedProgram_allFilter()
    {
        $this->viewListInCoordinatedProgramsUri .= 
                "?programId={$this->coordinatorOne->program->id}"
                . "&participantId={$this->clientParticipantOne->participant->id}"
                . "&reviewStatus=unreviewed"
                . "&order=observation-asc";
        
        $this->viewListInCoordinatedPrograms();
        $this->seeStatusCode(200);
$this->seeJsonContains(['print']);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewListInCoordinatedPrograms_fromLeftMenu()
    {
        $this->viewListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '3']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportThree->id]);
        
    }
    public function test_viewListInCoordinatedPrograms_fromParticipantPage()
    {
        $this->viewListInCoordinatedProgramsUri .= "?participantId={$this->clientParticipantOne->participant->id}";
        
        $this->viewListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewListInCoordinatedPrograms_fromCoordinatorDashboard()
    {
        $this->metricAssignmentReportTwo->approved = true;
        $this->viewListInCoordinatedProgramsUri .= 
                "?reviewStatus=unreviewed";
        
        $this->viewListInCoordinatedPrograms();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportThree->id]);
    }
}
