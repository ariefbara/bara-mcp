<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfDedicatedMentor;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\RecordOfTaskReport;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Firm\RecordOfTeam;
use Tests\Controllers\RecordPreparation\Firm\Team\RecordOfTeamProgramParticipation;

class TaskControllerTest extends PersonnelTestCase
{

    protected $viewAllTaskInCoordinatedProgramUri;
    protected $viewAllRelevanTaskAsProgramConsultantUri;
    protected $programOne;
    protected $programTwo;
    protected $personnelOne;
    protected $personnelTwo;
    protected $ownConsultant_p1;
    protected $ownConsultant_p2;
    protected $consultantOne;
    protected $consultantTwo;
    protected $ownCoordinator_p1;
    protected $ownCoordinator_p2;
    protected $coordinatorOne;
    protected $clientOne;
    protected $individualParticipantOne;
    protected $teamParticipantOne;
    protected $dedicatedMentorOne;
    protected $dedicatedMentorTwo;
    protected $consultantTaskOne;
    protected $coordinatorTaskOne;
    protected $taskReport;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Coordinator')->truncate();

        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();

        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();

        $this->connection->table('Task')->truncate();
        $this->connection->table('ConsultantTask')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        $this->connection->table('TaskReport')->truncate();

        $this->viewAllRelevanTaskAsProgramConsultantUri = $this->personnelUri . "/task-list-in-consulted-programs";
        $this->viewAllTaskInCoordinatedProgramUri = $this->personnelUri . "/tasks-list-in-coordinated-programs";

        $firm = $this->personnel->firm;

        $this->programOne = new RecordOfProgram($firm, 1);
        $this->programTwo = new RecordOfProgram($firm, 2);

        $this->personnelOne = new RecordOfPersonnel($firm, 1);
        $this->personnelTwo = new RecordOfPersonnel($firm, 2);

        $this->ownConsultant_p1 = new RecordOfConsultant($this->programOne, $this->personnel, 'own-p1');
        $this->ownConsultant_p2 = new RecordOfConsultant($this->programTwo, $this->personnel, 'own-p2');
        $this->consultantOne = new RecordOfConsultant($this->programOne, $this->personnelOne, 1);
        $this->consultantTwo = new RecordOfConsultant($this->programTwo, $this->personnelOne, 2);

        $this->ownCoordinator_p1 = new RecordOfCoordinator($this->programOne, $this->personnel, 'own-p1');
        $this->ownCoordinator_p2 = new RecordOfCoordinator($this->programTwo, $this->personnel, 'own-p2');
        $this->coordinatorOne = new RecordOfCoordinator($this->programTwo, $this->personnelTwo, 1);

        $participantOne = new RecordOfParticipant($this->programOne, 1);
        $participantTwo = new RecordOfParticipant($this->programTwo, 2);

        $this->clientOne = new RecordOfClient($firm, 1);

        $teamOne = new RecordOfTeam($firm, $this->clientOne, 1);

        $this->individualParticipantOne = new RecordOfClientParticipant($this->clientOne, $participantOne);

        $this->teamParticipantOne = new RecordOfTeamProgramParticipation($teamOne, $participantTwo);

        $this->dedicatedMentorOne = new RecordOfDedicatedMentor($participantOne, $this->ownConsultant_p1, 1);
        $this->dedicatedMentorTwo = new RecordOfDedicatedMentor($participantTwo, $this->ownConsultant_p2, 2);

        $taskOne = new RecordOfTask($participantOne, 1);
        $taskTwo = new RecordOfTask($participantTwo, 2);

        $this->consultantTaskOne = new RecordOfConsultantTask($this->consultantOne, $taskOne);

        $this->coordinatorTaskOne = new RecordOfCoordinatorTask($this->coordinatorOne, $taskTwo);
        
        $this->taskReport = new RecordOfTaskReport($this->coordinatorTaskOne->task, '1');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Coordinator')->truncate();

        $this->connection->table('Client')->truncate();
        $this->connection->table('Team')->truncate();

        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();
        $this->connection->table('UserParticipant')->truncate();
        $this->connection->table('TeamParticipant')->truncate();
        $this->connection->table('DedicatedMentor')->truncate();

        $this->connection->table('Task')->truncate();
        $this->connection->table('ConsultantTask')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        $this->connection->table('TaskReport')->truncate();
    }

    protected function viewAllRelevanTaskAsProgramConsultant()
    {
        $this->programOne->insert($this->connection);
        $this->programTwo->insert($this->connection);

        $this->personnelOne->insert($this->connection);
        $this->personnelTwo->insert($this->connection);

        $this->consultantOne->insert($this->connection);
        $this->ownConsultant_p1->insert($this->connection);
        $this->ownConsultant_p2->insert($this->connection);

        $this->coordinatorOne->insert($this->connection);

        $this->clientOne->insert($this->connection);
        $this->teamParticipantOne->team->insert($this->connection);

        $this->individualParticipantOne->insert($this->connection);
        $this->teamParticipantOne->insert($this->connection);

        $this->dedicatedMentorOne->insert($this->connection);
        $this->dedicatedMentorTwo->insert($this->connection);

        $this->consultantTaskOne->insert($this->connection);
        $this->coordinatorTaskOne->insert($this->connection);

// echo $this->viewAllRelevanTaskAsProgramConsultantUri;
        $this->get($this->viewAllRelevanTaskAsProgramConsultantUri, $this->personnel->token);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_200()
    {
        $this->disableExceptionHandling();
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['printme']);

        $response = [
            'total' => "2",
            'list' => [
                [
                    'name' => $this->consultantTaskOne->task->name,
                    'description' => $this->consultantTaskOne->task->description,
                    'cancelled' => strval(intval($this->consultantTaskOne->task->cancelled)),
                    'createdTime' => $this->consultantTaskOne->task->createdTime,
                    'modifiedTime' => $this->consultantTaskOne->task->modifiedTime,
                    'consultantTaskId' => $this->consultantTaskOne->id,
                    'consultantId' => $this->consultantTaskOne->consultant->id,
                    'consultantPersonnelId' => $this->consultantTaskOne->consultant->personnel->id,
                    'consultantName' => $this->consultantTaskOne->consultant->personnel->getFullName(),
                    'coordinatorTaskId' => null,
                    'coordinatorId' => null,
                    'coordinatorPersonnelId' => null,
                    'coordinatorName' => null,
                    'participantId' => $this->consultantTaskOne->task->participant->id,
                    'participantName' => $this->individualParticipantOne->client->getFullName(),
                    'completed' => strval(intval(false)),
                    'selfConsultantId' => $this->ownConsultant_p1->id,
                    'programId' => $this->consultantTaskOne->task->participant->program->id,
                    'programName' => $this->consultantTaskOne->task->participant->program->name,
                ],
                [
                    'name' => $this->coordinatorTaskOne->task->name,
                    'description' => $this->coordinatorTaskOne->task->description,
                    'cancelled' => strval(intval($this->coordinatorTaskOne->task->cancelled)),
                    'createdTime' => $this->coordinatorTaskOne->task->createdTime,
                    'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
                    'consultantTaskId' => null,
                    'consultantId' => null,
                    'consultantPersonnelId' => null,
                    'consultantName' => null,
                    'coordinatorTaskId' => $this->coordinatorTaskOne->id,
                    'coordinatorId' => $this->coordinatorTaskOne->coordinator->id,
                    'coordinatorPersonnelId' => $this->coordinatorTaskOne->coordinator->personnel->id,
                    'coordinatorName' => $this->coordinatorTaskOne->coordinator->personnel->getFullName(),
                    'participantId' => $this->coordinatorTaskOne->task->participant->id,
                    'participantName' => $this->teamParticipantOne->team->name,
                    'completed' => strval(intval(false)),
                    'selfConsultantId' => $this->ownConsultant_p2->id,
                    'programId' => $this->coordinatorTaskOne->task->participant->program->id,
                    'programName' => $this->coordinatorTaskOne->task->participant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_excludeConsultantTaskForNonDedicatedMentee_200()
    {
        $this->dedicatedMentorOne->consultant = $this->consultantOne;
        
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_excludeConsultantTaskOnNonMentoredProgram_200()
    {
        $this->ownConsultant_p1->active = false;
        
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_excludeCoordinatorTaskOnNonMentoredProgram_200()
    {
        $this->ownConsultant_p2->active = false;
        
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_includeOwnTaskForNonDedicatedMentee_200()
    {
        $this->dedicatedMentorOne->consultant = $this->consultantOne;
        
        $this->consultantTaskOne->consultant = $this->ownConsultant_p1;
        
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_excludeCoordinatorTaskForNonDedicatedMentee_200()
    {
        $this->consultantTwo->insert($this->connection);
        $this->dedicatedMentorTwo->consultant = $this->consultantTwo;
        
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_applyAllFilter()
    {
        $from = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $to = (new DateTime())->format('Y-m-d H:i:s');
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?cancelled=false"
                . "&completed=false"
                . "&from=$from"
                . "&to=$to"
                . "&keyword=ask"
                . "&taskSource=CONSULTANT"
                . "&participantId={$this->individualParticipantOne->participant->id}";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_applyParticipantIdFilter_includeTasksToParticipantFromNonDedicatedOrNotOwned()
    {
        $this->consultantTwo->insert($this->connection);
        $this->dedicatedMentorTwo->consultant = $this->consultantTwo;
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?participantId={$this->teamParticipantOne->participant->id}";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_cancelledFilter()
    {
        $this->consultantTaskOne->task->cancelled = true;
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?cancelled=true";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_completedFilter()
    {
        $this->taskReport->insert($this->connection);
        
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?completed=true";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_fromFilter()
    {
        $this->consultantTaskOne->task->modifiedTime = (new \DateTime('-1 days'))->format('Y-m-d H:i:s');
        
        $from = (new \DateTime('-2 days'))->format('Y-m-d H:i:s');
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?from=$from";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_toFilter()
    {
        $this->consultantTaskOne->task->modifiedTime = (new \DateTime('-100 days'))->format('Y-m-d H:i:s');
        
        $to = (new \DateTime('-99 days'))->format('Y-m-d H:i:s');
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?to=$to";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_keywordFilter_searchThroughName()
    {
        $this->consultantTaskOne->task->name = "task one name";
        
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?keyword=one";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_keywordFilter_searchThroughDescription()
    {
        $this->consultantTaskOne->task->description = "task one description";
        
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?keyword=one";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_taskSource_CONSULTANT()
    {
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?taskSource=CONSULTANT";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllRelevanTaskAsProgramConsultant_taskSource_COORDINATOR()
    {
        $this->viewAllRelevanTaskAsProgramConsultantUri .= "?taskSource=COORDINATOR";
                
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_consultant_fromLeftMenu()
    {
        $this->consultantTaskOne->task->cancelled = true;
        $this->dedicatedMentorTwo->cancelled = true;
        
        $this->viewAllRelevanTaskAsProgramConsultantUri .=
                "?cancelled=false";
        
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '0']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_consultant_fromParticipantPage()
    {
        $this->viewAllRelevanTaskAsProgramConsultantUri .=
                "?participantId={$this->individualParticipantOne->participant->id}"
                . "&cancelled=false";
        
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_consultant_fromConsultantDashboard()
    {
        $this->viewAllRelevanTaskAsProgramConsultantUri .= 
                "?cancelled=false"
                . "&completed=false";
                
        $this->taskReport->insert($this->connection);
        $this->viewAllRelevanTaskAsProgramConsultant();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    
    protected function viewAllTaskInCoordinatedProgram()
    {
        $this->programOne->insert($this->connection);
        $this->programTwo->insert($this->connection);

        $this->personnelOne->insert($this->connection);
        $this->personnelTwo->insert($this->connection);

        $this->consultantOne->insert($this->connection);

        $this->coordinatorOne->insert($this->connection);
        $this->ownCoordinator_p1->insert($this->connection);
        $this->ownCoordinator_p2->insert($this->connection);

        $this->clientOne->insert($this->connection);
        $this->teamParticipantOne->team->insert($this->connection);

        $this->individualParticipantOne->insert($this->connection);
        $this->teamParticipantOne->insert($this->connection);

        $this->consultantTaskOne->insert($this->connection);
        $this->coordinatorTaskOne->insert($this->connection);

// echo $this->viewAllTaskInCoordinatedProgramUri;
        $this->get($this->viewAllTaskInCoordinatedProgramUri, $this->personnel->token);
    }
    public function test_viewAllTaskInCoordinatedProgram_200()
    {
$this->disableExceptionHandling();
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['printme']);
        
        $response = [
            'total' => "2",
            'list' => [
                [
                    'name' => $this->consultantTaskOne->task->name,
                    'description' => $this->consultantTaskOne->task->description,
                    'cancelled' => strval(intval($this->consultantTaskOne->task->cancelled)),
                    'createdTime' => $this->consultantTaskOne->task->createdTime,
                    'modifiedTime' => $this->consultantTaskOne->task->modifiedTime,
                    'consultantTaskId' => $this->consultantTaskOne->id,
                    'consultantId' => $this->consultantTaskOne->consultant->id,
                    'consultantPersonnelId' => $this->consultantTaskOne->consultant->personnel->id,
                    'consultantName' => $this->consultantTaskOne->consultant->personnel->getFullName(),
                    'coordinatorTaskId' => null,
                    'coordinatorId' => null,
                    'coordinatorPersonnelId' => null,
                    'coordinatorName' => null,
                    'participantId' => $this->consultantTaskOne->task->participant->id,
                    'participantName' => $this->individualParticipantOne->client->getFullName(),
                    'completed' => strval(intval(false)),
                    'selfCoordinatorId' => $this->ownCoordinator_p1->id,
                    'programId' => $this->consultantTaskOne->task->participant->program->id,
                    'programName' => $this->consultantTaskOne->task->participant->program->name,
                ],
                [
                    'name' => $this->coordinatorTaskOne->task->name,
                    'description' => $this->coordinatorTaskOne->task->description,
                    'cancelled' => strval(intval($this->coordinatorTaskOne->task->cancelled)),
                    'createdTime' => $this->coordinatorTaskOne->task->createdTime,
                    'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
                    'consultantTaskId' => null,
                    'consultantId' => null,
                    'consultantPersonnelId' => null,
                    'consultantName' => null,
                    'coordinatorTaskId' => $this->coordinatorTaskOne->id,
                    'coordinatorId' => $this->coordinatorTaskOne->coordinator->id,
                    'coordinatorPersonnelId' => $this->coordinatorTaskOne->coordinator->personnel->id,
                    'coordinatorName' => $this->coordinatorTaskOne->coordinator->personnel->getFullName(),
                    'participantId' => $this->coordinatorTaskOne->task->participant->id,
                    'participantName' => $this->teamParticipantOne->team->name,
                    'completed' => strval(intval(false)),
                    'selfCoordinatorId' => $this->ownCoordinator_p2->id,
                    'programId' => $this->coordinatorTaskOne->task->participant->program->id,
                    'programName' => $this->coordinatorTaskOne->task->participant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAllTaskInCoordinatedProgram_excludeTaskInNonCoordinatedProgram()
    {
        $this->ownCoordinator_p1->active = false;
        
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_applyAllFilter()
    {
        $from = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $to = (new DateTime())->format('Y-m-d H:i:s');
        $this->viewAllTaskInCoordinatedProgramUri .= "?cancelled=false"
                . "&completed=false"
                . "&from=$from"
                . "&to=$to"
                . "&keyword=ask"
                . "&taskSource=CONSULTANT"
                . "&participantId={$this->individualParticipantOne->participant->id}";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_applyParticipantIdFilter_returnAllTaskForSpesificParticipant()
    {
        $this->viewAllTaskInCoordinatedProgramUri .= "?participantId={$this->teamParticipantOne->participant->id}";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_cancelledFilter()
    {
        $this->consultantTaskOne->task->cancelled = true;
        $this->viewAllTaskInCoordinatedProgramUri .= "?cancelled=true";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_completedFilter()
    {
        $this->taskReport->insert($this->connection);
        
        $this->viewAllTaskInCoordinatedProgramUri .= "?completed=true";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_fromFilter()
    {
        $this->consultantTaskOne->task->modifiedTime = (new \DateTime('-1 days'))->format('Y-m-d H:i:s');
        
        $from = (new \DateTime('-2 days'))->format('Y-m-d H:i:s');
        $this->viewAllTaskInCoordinatedProgramUri .= "?from=$from";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_toFilter()
    {
        $this->consultantTaskOne->task->modifiedTime = (new \DateTime('-100 days'))->format('Y-m-d H:i:s');
        
        $to = (new \DateTime('-99 days'))->format('Y-m-d H:i:s');
        $this->viewAllTaskInCoordinatedProgramUri .= "?to=$to";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_keywordFilter_searchThroughName()
    {
        $this->consultantTaskOne->task->name = "task one name";
        
        $this->viewAllTaskInCoordinatedProgramUri .= "?keyword=one";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_keywordFilter_searchThroughDescription()
    {
        $this->consultantTaskOne->task->description = "task one description";
        
        $this->viewAllTaskInCoordinatedProgramUri .= "?keyword=one";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_taskSource_CONSULTANT()
    {
        $this->viewAllTaskInCoordinatedProgramUri .= "?taskSource=CONSULTANT";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_taskSource_COORDINATOR()
    {
        $this->viewAllTaskInCoordinatedProgramUri .= "?taskSource=COORDINATOR";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_coordinator_fromLeftMenu_200()
    {
        $this->viewAllTaskInCoordinatedProgramUri .= "?cancelled=false";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_coordinator_fromParticipantPage_200()
    {
        $this->viewAllTaskInCoordinatedProgramUri .= 
                "?cancelled=false"
                . "&participantId={$this->individualParticipantOne->participant->id}";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }

}
