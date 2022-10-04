<?php

namespace Tests\Controllers\Personnel;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\MetricAssignment\RecordOfMetricAssignmentReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfMetricAssignment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;
use Tests\Controllers\RecordPreparation\RecordOfUser;
use Tests\Controllers\RecordPreparation\User\RecordOfUserParticipant;

class UnreviewedMetricReportListInCoordinatedProgramControllerTest extends AggregatedCoordinatorInPersonnelContextTestCase
{
    protected $viewAllUri;
    
    protected $clientParticipantOne_prog1;
    protected $teamParticipantTwo_prog2;
    protected $userParticipantThree_prog1;

    protected $metricAssignmentReportOne;
    protected $metricAssignmentReportTwo;
    protected $metricAssignmentReportThree;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->viewAllUri = $this->personnelUri . "/unreviewed-metric-report-list-in-coordinated-programs";
        
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
        
        $firm = $this->personnel->firm;
        $programOne = $this->coordinatorOne->program;
        $programTwo = $this->coordinatorTwo->program;

        $clientOne = new RecordOfClient($firm, 1);

        $teamOne = new RecordOfTeam($firm, $clientOne, 1);

        $userOne = new RecordOfUser(1);

        $participantOne = new RecordOfParticipant($programOne, 1);
        $this->clientParticipantOne_prog1 = new RecordOfClientParticipant($clientOne, $participantOne);

        $participantTwo = new RecordOfParticipant($programTwo, 2);
        $this->teamParticipantTwo_prog2 = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);

        $participantThree = new RecordOfParticipant($programOne, 3);
        $this->userParticipantThree_prog1 = new RecordOfUserParticipant($userOne, $participantThree);

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
        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();
        $this->connection->table('User')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('MetricAssignment')->truncate();
        $this->connection->table('MetricAssignmentReport')->truncate();
    }
    
    protected function viewAll()
    {
        $this->persistAggregatedCoordinatorDependency();
        
        $this->clientParticipantOne_prog1->client->insert($this->connection);
        $this->teamParticipantTwo_prog2->team->insert($this->connection);
        $this->userParticipantThree_prog1->user->insert($this->connection);
        
        $this->clientParticipantOne_prog1->insert($this->connection);
        $this->teamParticipantTwo_prog2->insert($this->connection);
        $this->userParticipantThree_prog1->insert($this->connection);
        
        $this->metricAssignmentReportOne->metricAssignment->insert($this->connection);
        $this->metricAssignmentReportTwo->metricAssignment->insert($this->connection);
        $this->metricAssignmentReportThree->metricAssignment->insert($this->connection);
        
        $this->metricAssignmentReportOne->insert($this->connection);
        $this->metricAssignmentReportTwo->insert($this->connection);
        $this->metricAssignmentReportThree->insert($this->connection);
        
        $this->get($this->viewAllUri, $this->personnel->token);
    }
    public function test_viewAll_200()
    {
$this->disableExceptionHandling();
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '3',
            'list' => [
                [
                    'id' => $this->metricAssignmentReportOne->id,
                    'observationTime' => $this->metricAssignmentReportOne->observationTime,
                    'submitTime' => $this->metricAssignmentReportOne->submitTime,
                    'assignmentStartDate' => $this->metricAssignmentReportOne->metricAssignment->startDate,
                    'assignmentEndDate' => $this->metricAssignmentReportOne->metricAssignment->endDate,
                    'participantId' => $this->metricAssignmentReportOne->metricAssignment->participant->id,
                    'participantName' => $this->clientParticipantOne_prog1->client->getFullName(),
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->metricAssignmentReportOne->metricAssignment->participant->program->id,
                    'programName' => $this->metricAssignmentReportOne->metricAssignment->participant->program->name,
                ],
                [
                    'id' => $this->metricAssignmentReportTwo->id,
                    'observationTime' => $this->metricAssignmentReportTwo->observationTime,
                    'submitTime' => $this->metricAssignmentReportTwo->submitTime,
                    'assignmentStartDate' => $this->metricAssignmentReportTwo->metricAssignment->startDate,
                    'assignmentEndDate' => $this->metricAssignmentReportTwo->metricAssignment->endDate,
                    'participantId' => $this->metricAssignmentReportTwo->metricAssignment->participant->id,
                    'participantName' => $this->teamParticipantTwo_prog2->team->name,
                    'coordinatorId' => $this->coordinatorTwo->id,
                    'programId' => $this->metricAssignmentReportTwo->metricAssignment->participant->program->id,
                    'programName' => $this->metricAssignmentReportTwo->metricAssignment->participant->program->name,
                ],
                [
                    'id' => $this->metricAssignmentReportThree->id,
                    'observationTime' => $this->metricAssignmentReportThree->observationTime,
                    'submitTime' => $this->metricAssignmentReportThree->submitTime,
                    'assignmentStartDate' => $this->metricAssignmentReportThree->metricAssignment->startDate,
                    'assignmentEndDate' => $this->metricAssignmentReportThree->metricAssignment->endDate,
                    'participantId' => $this->metricAssignmentReportThree->metricAssignment->participant->id,
                    'participantName' => $this->userParticipantThree_prog1->user->getFullName(),
                    'coordinatorId' => $this->coordinatorOne->id,
                    'programId' => $this->metricAssignmentReportThree->metricAssignment->participant->program->id,
                    'programName' => $this->metricAssignmentReportThree->metricAssignment->participant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAll_excludeReviewedReport_approved()
    {
        $this->metricAssignmentReportThree->approved = true;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewAll_excludeReviewedReport_rejected()
    {
        $this->metricAssignmentReportThree->approved = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewAll_excludeRemovedReport()
    {
        $this->metricAssignmentReportThree->removed = true;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewAll_excludeUnmanagedReport_belongsToInactiveParticipant()
    {
        $this->metricAssignmentReportThree->metricAssignment->participant->active = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportThree->id]);
    }
    public function test_viewAll_excludeUnmanagedReport_belongsParticipantInUnmanagedProgram_inactiveCoordinator()
    {
        $this->coordinatorTwo->active = false;
        
        $this->viewAll();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportOne->id]);
        $this->seeJsonDoesntContains(['id' => $this->metricAssignmentReportTwo->id]);
        $this->seeJsonContains(['id' => $this->metricAssignmentReportThree->id]);
    }
}
