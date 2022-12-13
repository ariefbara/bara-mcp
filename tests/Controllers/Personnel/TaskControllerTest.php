<?php

namespace Tests\Controllers\Personnel;

use DateTime;
use SharedContext\Domain\ValueObject\TaskReportReviewStatus;
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
    protected $viewTaskListInAllConsultedProgramUri;
    //
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
    //
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

        $this->viewTaskListInAllConsultedProgramUri = $this->personnelUri . "/task-list-in-consulted-programs";
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

    protected function viewTaskListInAllConsultedProgram()
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

        $this->get($this->viewTaskListInAllConsultedProgramUri, $this->personnel->token);
//echo $this->viewTaskListInAllConsultedProgramUri;
//$this->seeJsonContains(['print']);
    }
    public function test_viewTaskListInAllConsultedProgram_200()
    {
        $this->disableExceptionHandling();
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);

        $response = [
            'total' => "2",
            'list' => [
                [
                    'name' => $this->consultantTaskOne->task->name,
                    'description' => $this->consultantTaskOne->task->description,
                    'cancelled' => strval(intval($this->consultantTaskOne->task->cancelled)),
                    'createdTime' => $this->consultantTaskOne->task->createdTime,
                    'modifiedTime' => $this->consultantTaskOne->task->modifiedTime,
                    'dueDate' => $this->consultantTaskOne->task->dueDate,
                    'reviewStatus' => 'no-report-submitted',
                    'consultantTaskId' => $this->consultantTaskOne->id,
                    'coordinatorTaskId' => null,
                    'taskGiverName' => $this->consultantTaskOne->consultant->personnel->getFullName(),
                    'participantId' => $this->consultantTaskOne->task->participant->id,
                    'participantName' => $this->individualParticipantOne->client->getFullName(),
                    'consultantId' => $this->ownConsultant_p1->id,
                    'programId' => $this->consultantTaskOne->task->participant->program->id,
                    'programName' => $this->consultantTaskOne->task->participant->program->name,
                ],
                [
                    'name' => $this->coordinatorTaskOne->task->name,
                    'description' => $this->coordinatorTaskOne->task->description,
                    'cancelled' => strval(intval($this->coordinatorTaskOne->task->cancelled)),
                    'createdTime' => $this->coordinatorTaskOne->task->createdTime,
                    'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
                    'dueDate' => $this->coordinatorTaskOne->task->dueDate,
                    'reviewStatus' => 'no-report-submitted',
                    'consultantTaskId' => null,
                    'coordinatorTaskId' => $this->coordinatorTaskOne->id,
                    'taskGiverName' => $this->coordinatorTaskOne->coordinator->personnel->getFullName(),
                    'participantId' => $this->coordinatorTaskOne->task->participant->id,
                    'participantName' => $this->teamParticipantOne->team->name,
                    'consultantId' => $this->ownConsultant_p2->id,
                    'programId' => $this->coordinatorTaskOne->task->participant->program->id,
                    'programName' => $this->coordinatorTaskOne->task->participant->program->name,
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewTaskListInAllConsultedProgram_excludeTaskForNonDedicatedMentee()
    {
        $this->dedicatedMentorTwo->cancelled = true;
        
        $this->disableExceptionHandling();
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_applyAllFilter()
    {
        $modifiedTimeFrom = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $modifiedTimeTo = (new DateTime())->format('Y-m-d H:i:s');
        $dueDateFrom = (new DateTime('-2 months'))->format('Y-m-d');
        $dueDateTo = (new DateTime('+2 months'))->format('Y-m-d');
        $this->viewTaskListInAllConsultedProgramUri .= ""
                . "?cancelled=false"
                . "&completed=false"
                . "&modifiedTimeFrom=$modifiedTimeFrom"
                . "&modifiedTimeTo=$modifiedTimeTo"
                . "&dueDateFrom=$dueDateFrom"
                . "&dueDateTo=$dueDateTo"
                . "&keyword=ask"
                . "&taskSource=CONSULTANT"
                . "&programId={$this->individualParticipantOne->participant->program->id}"
                . "&participantId={$this->individualParticipantOne->participant->id}"
                . "&onlyShowRelevantTask=true"
                . "&order=due-date-desc";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_onlyShowRelevantTaskFilter_excludeTaskForNonDedicatedMentee()
    {
        $this->dedicatedMentorOne->consultant = $this->consultantOne;
        
        $this->viewTaskListInAllConsultedProgramUri .= ""
                . "?onlyShowRelevantTask=true";
        
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_onlyShowRelevantTaskFilter_excludeCoordinatorTaskForNonMentoredProgram_200()
    {
        $this->dedicatedMentorTwo->cancelled = true;
        
        $this->viewTaskListInAllConsultedProgramUri .= ""
                . "?onlyShowRelevantTask=true";
        
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_onlyShowRelevantTaskFilter_includeOwnTaskForNonDedicatedMentee_200()
    {
        $this->dedicatedMentorOne->consultant = $this->consultantOne;
        $this->consultantTaskOne->consultant = $this->ownConsultant_p1;
        
        $this->viewTaskListInAllConsultedProgramUri .= ""
                . "?onlyShowRelevantTask=true";
        
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '2']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_programIdIdFilter_200()
    {
//        $this->consultantTwo->insert($this->connection);
//        $this->dedicatedMentorTwo->consultant = $this->consultantTwo;
        $this->viewTaskListInAllConsultedProgramUri .= "" 
                . "?programId={$this->teamParticipantOne->participant->program->id}";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_participantIdFilter_200()
    {
//        $this->consultantTwo->insert($this->connection);
//        $this->dedicatedMentorTwo->consultant = $this->consultantTwo;
        $this->viewTaskListInAllConsultedProgramUri .= "" 
                . "?participantId={$this->teamParticipantOne->participant->id}";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_cancelledFilter()
    {
        $this->consultantTaskOne->task->cancelled = true;
        $this->viewTaskListInAllConsultedProgramUri .= "?cancelled=true";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_completedFilter()
    {
        $this->taskReport->reviewStatus = TaskReportReviewStatus::APPROVED;
        $this->taskReport->insert($this->connection);
        
        $this->viewTaskListInAllConsultedProgramUri .= "?completed=true";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains([
            'coordinatorTaskId' => $this->coordinatorTaskOne->id,
            'reviewStatus' => 'approved',
        ]);
    }
    public function test_viewTaskListInAllConsultedProgram_fromFilter()
    {
        $this->consultantTaskOne->task->modifiedTime = (new \DateTime('-1 days'))->format('Y-m-d H:i:s');
        
        $modifiedTimeFrom = (new \DateTime('-2 days'))->format('Y-m-d H:i:s');
        $this->viewTaskListInAllConsultedProgramUri .= "?modifiedTimeFrom=$modifiedTimeFrom";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_toFilter()
    {
        $this->consultantTaskOne->task->modifiedTime = (new \DateTime('-100 days'))->format('Y-m-d H:i:s');
        
        $modifiedTimeTo = (new \DateTime('-99 days'))->format('Y-m-d H:i:s');
        $this->viewTaskListInAllConsultedProgramUri .= "?modifiedTimeTo=$modifiedTimeTo";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_programIdFilter()
    {
        $this->viewTaskListInAllConsultedProgramUri .= "?programId={$this->consultantOne->program->id}";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_keywordFilter_searchThroughName()
    {
        $this->consultantTaskOne->task->name = "task one name";
        
        $this->viewTaskListInAllConsultedProgramUri .= "?keyword=one";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_keywordFilter_searchThroughDescription()
    {
        $this->consultantTaskOne->task->description = "task one description";
        
        $this->viewTaskListInAllConsultedProgramUri .= "?keyword=one";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_taskSource_CONSULTANT()
    {
        $this->viewTaskListInAllConsultedProgramUri .= "?taskSource=CONSULTANT";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewTaskListInAllConsultedProgram_taskSource_COORDINATOR()
    {
        $this->viewTaskListInAllConsultedProgramUri .= "?taskSource=COORDINATOR";
                
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_consultant_fromLeftMenu()
    {
        $this->consultantTaskOne->task->cancelled = true;
        $this->dedicatedMentorTwo->cancelled = true;
        
        $this->viewTaskListInAllConsultedProgramUri .=
                "?cancelled=false"
                . "&onlyShowRelevantTask=true";
        
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '0']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_consultant_fromParticipantPage()
    {
        $this->viewTaskListInAllConsultedProgramUri .=
                "?participantId={$this->individualParticipantOne->participant->id}"
                . "&cancelled=false";
        
        $this->viewTaskListInAllConsultedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_consultant_fromConsultantDashboard()
    {
        $this->viewTaskListInAllConsultedProgramUri .= 
                "?cancelled=false"
                . "&onlyShowRelevantTask=true"
                . "&completed=false";
                
        $this->taskReport->reviewStatus = TaskReportReviewStatus::APPROVED;
        $this->taskReport->insert($this->connection);
        $this->viewTaskListInAllConsultedProgram();
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

        $this->get($this->viewAllTaskInCoordinatedProgramUri, $this->personnel->token);
// echo $this->viewAllTaskInCoordinatedProgramUri;
// $this->seeJsonContains(['print']);
    }
    public function test_viewAllTaskInCoordinatedProgram_200()
    {
$this->disableExceptionHandling();
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => "2",
            'list' => [
                [
                    'name' => $this->consultantTaskOne->task->name,
                    'description' => $this->consultantTaskOne->task->description,
                    'cancelled' => strval(intval($this->consultantTaskOne->task->cancelled)),
                    'createdTime' => $this->consultantTaskOne->task->createdTime,
                    'modifiedTime' => $this->consultantTaskOne->task->modifiedTime,
                    'dueDate' => $this->consultantTaskOne->task->dueDate,
                    'reviewStatus' => 'no-report-submitted',
                    'consultantTaskId' => $this->consultantTaskOne->id,
                    'coordinatorTaskId' => null,
                    'taskGiverName' => $this->consultantTaskOne->consultant->personnel->getFullName(),
                    'participantId' => $this->consultantTaskOne->task->participant->id,
                    'participantName' => $this->individualParticipantOne->client->getFullName(),
                    'coordinatorId' => $this->ownCoordinator_p1->id,
                    'programId' => $this->consultantTaskOne->task->participant->program->id,
                    'programName' => $this->consultantTaskOne->task->participant->program->name,
                ],
                [
                    'name' => $this->coordinatorTaskOne->task->name,
                    'description' => $this->coordinatorTaskOne->task->description,
                    'cancelled' => strval(intval($this->coordinatorTaskOne->task->cancelled)),
                    'createdTime' => $this->coordinatorTaskOne->task->createdTime,
                    'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
                    'dueDate' => $this->coordinatorTaskOne->task->dueDate,
                    'reviewStatus' => 'no-report-submitted',
                    'consultantTaskId' => null,
                    'coordinatorTaskId' => $this->coordinatorTaskOne->id,
                    'taskGiverName' => $this->coordinatorTaskOne->coordinator->personnel->getFullName(),
                    'participantId' => $this->coordinatorTaskOne->task->participant->id,
                    'participantName' => $this->teamParticipantOne->team->name,
                    'coordinatorId' => $this->ownCoordinator_p2->id,
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
        $this->coordinatorTaskOne->coordinator = $this->ownCoordinator_p2;
        
$this->disableExceptionHandling();
        $modifiedTimeFrom = (new DateTime('-1 months'))->format('Y-m-d H:i:s');
        $modifiedTimeTo = (new DateTime())->format('Y-m-d H:i:s');
        $dueDateFrom = (new DateTime('-2 months'))->format('Y-m-d');
        $dueDateTo = (new DateTime('+2 months'))->format('Y-m-d');
        $this->viewAllTaskInCoordinatedProgramUri .= ""
                . "?cancelled=false"
                . "&completed=false"
                . "&modifiedTimeFrom=$modifiedTimeFrom"
                . "&modifiedTimeTo=$modifiedTimeTo"
                . "&dueDateFrom=$dueDateFrom"
                . "&dueDateTo=$dueDateTo"
                . "&keyword=ask"
                . "&taskSource=COORDINATOR"
                . "&programId={$this->teamParticipantOne->participant->program->id}"
                . "&participantId={$this->teamParticipantOne->participant->id}"
                . "&onlyShowOwnedTask=true"
                . "&order=due-date-desc";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_onlyShowOwnedTaskFilter_onlyReturnOwnedCoordinatorTask()
    {
        $this->coordinatorTaskOne->coordinator = $this->ownCoordinator_p2;
        
        $this->viewAllTaskInCoordinatedProgramUri .= ""
                . "?onlyShowOwnedTask=true";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_programIdIdFilter_returnAllTaskForSpesificParticipant()
    {
        $this->viewAllTaskInCoordinatedProgramUri .= ""
                . "?programId={$this->teamParticipantOne->participant->program->id}";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_applyParticipantIdFilter_returnAllTaskForSpesificParticipant()
    {
        $this->viewAllTaskInCoordinatedProgramUri .= ""
                . "?participantId={$this->teamParticipantOne->participant->id}";
                
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
        $this->taskReport->reviewStatus = TaskReportReviewStatus::APPROVED;
        $this->taskReport->insert($this->connection);
        
        $this->viewAllTaskInCoordinatedProgramUri .= "?completed=true";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonDoesntContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonContains([
            'coordinatorTaskId' => $this->coordinatorTaskOne->id,
            'reviewStatus' => 'approved'
        ]);
    }
    public function test_viewAllTaskInCoordinatedProgram_fromFilter()
    {
        $this->consultantTaskOne->task->modifiedTime = (new \DateTime('-1 days'))->format('Y-m-d H:i:s');
        
        $modifiedTimeFrom = (new \DateTime('-2 days'))->format('Y-m-d H:i:s');
        $this->viewAllTaskInCoordinatedProgramUri .= "?modifiedTimeFrom=$modifiedTimeFrom";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAllTaskInCoordinatedProgram_toFilter()
    {
        $this->consultantTaskOne->task->modifiedTime = (new \DateTime('-100 days'))->format('Y-m-d H:i:s');
        
        $modifiedTimeTo = (new \DateTime('-99 days'))->format('Y-m-d H:i:s');
        $this->viewAllTaskInCoordinatedProgramUri .= "?modifiedTimeTo=$modifiedTimeTo";
                
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
    public function test_coordinator_fromCoordinatorDashboard_200()
    {
        $this->taskReport->reviewStatus = TaskReportReviewStatus::APPROVED;
        $this->taskReport->insert($this->connection);
        
        $this->viewAllTaskInCoordinatedProgramUri .= 
                "?cancelled=false"
                . "&completed=false";
                
        $this->viewAllTaskInCoordinatedProgram();
        $this->seeStatusCode(200);
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }

}
