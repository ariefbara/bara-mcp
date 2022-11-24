<?php

namespace Tests\Controllers\Client\AsTeamMember\ProgramParticipation;

use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantFileInfo;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\RecordOfTaskReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\TaskReport\RecordOfTaskReportAttachment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class TaskControllerTest extends ExtendedTeamParticipantTestCase
{
    protected $participantFileInfoOne;
    protected $participantFileInfoTwo;
    
    protected $taskOne;
    protected $taskTwo;
    
    protected $consultantTaskOne;
    protected $coordinatorTaskOne;

    protected $taskReportOne;
    
    protected $taskReportAttachmentOne;

    protected $submitReportRequest;
    protected $viewAllUri;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Personnel')->truncate();
        
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        
        $this->connection->table('Task')->truncate();
        $this->connection->table('ConsultantTask')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        
        $this->connection->table('TaskReport')->truncate();
        
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('ParticipantFileInfo')->truncate();
        $this->connection->table('TaskReportAttachment')->truncate();
        
        $partipant = $this->teamParticipant->participant;
        $program = $partipant->program;
        $firm = $program->firm;
        
        $fileInfoOne = new RecordOfFileInfo(1);
        $fileInfoTwo = new RecordOfFileInfo(2);
        
        $this->participantFileInfoOne = new RecordOfParticipantFileInfo($partipant, $fileInfoOne);
        $this->participantFileInfoTwo = new RecordOfParticipantFileInfo($partipant, $fileInfoTwo);
        
        $personnelOne = new RecordOfPersonnel($firm, 1);
        $personnelTwo = new RecordOfPersonnel($firm, 2);
        
        $consultantOne = new RecordOfConsultant($program, $personnelOne, 1);
        
        $coordinatorOne = new RecordOfCoordinator($program, $personnelTwo, 1);
        
        $this->taskOne = new RecordOfTask($this->teamParticipant->participant, 1);
        $this->taskTwo = new RecordOfTask($this->teamParticipant->participant, 2);
        
        $this->consultantTaskOne = new RecordOfConsultantTask($consultantOne, $this->taskOne);
        
        $this->coordinatorTaskOne = new RecordOfCoordinatorTask($coordinatorOne, $this->taskTwo);
        
        $this->taskReportOne = new RecordOfTaskReport($this->taskOne, 1);
        
        $this->taskReportAttachmentOne = new RecordOfTaskReportAttachment($this->taskReportOne, $this->participantFileInfoOne, 1);
        
        $this->submitReportRequest = [
            'content' => 'new task report content',
            'attachments' => [
                $this->participantFileInfoOne->id,
                $this->participantFileInfoTwo->id,
            ],
        ];
        $this->viewAllUri = $this->teamParticipantUri . "/tasks";
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Personnel')->truncate();
        
        $this->connection->table('Consultant')->truncate();
        $this->connection->table('Coordinator')->truncate();
        
        $this->connection->table('Task')->truncate();
        $this->connection->table('ConsultantTask')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        
        $this->connection->table('TaskReport')->truncate();
        
        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('ParticipantFileInfo')->truncate();
        $this->connection->table('TaskReportAttachment')->truncate();
    }
    
    //
    protected function submitReport()
    {
        $this->prepareRecord();
        
        $this->taskOne->insert($this->connection);
        
        $this->participantFileInfoOne->insert($this->connection);
        $this->participantFileInfoTwo->insert($this->connection);
        
        $uri = $this->teamParticipantUri . "/tasks/{$this->taskOne->id}";
        $this->put($uri, $this->submitReportRequest, $this->teamMember->client->token);
    }
    public function test_submitReport_200()
    {
$this->disableExceptionHandling();
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['content' => $this->submitReportRequest['content']]);
        $this->seeJsonContains(['filePath' => $this->participantFileInfoOne->fileInfo->getFullyPath()]);
        $this->seeJsonContains(['filePath' => $this->participantFileInfoTwo->fileInfo->getFullyPath()]);
        
        $taskReportEntry = [
            'Task_id' => $this->taskOne->id,
            'content' => $this->submitReportRequest['content'],
            'createdTime' => $this->currentTimeString(),
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeInDatabase('TaskReport', $taskReportEntry);
        
        $taskReportAttachmentEntry = [
            'removed' => false,
            'ParticipantFileInfo_id' => $this->participantFileInfoOne->id,
        ];
        $this->seeInDatabase('TaskReportAttachment', $taskReportAttachmentEntry);
        
        $taskReportAttachmentEntry = [
            'removed' => false,
            'ParticipantFileInfo_id' => $this->participantFileInfoTwo->id,
        ];
        $this->seeInDatabase('TaskReportAttachment', $taskReportAttachmentEntry);
    }
    public function test_submitReport_reportAlreadyExist_updateExistingReport()
    {
        $this->taskReportOne->insert($this->connection);
        $this->taskReportAttachmentOne->insert($this->connection);
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['content' => $this->submitReportRequest['content']]);
        
        $this->seeJsonContains(['filePath' => $this->participantFileInfoOne->fileInfo->getFullyPath()]);
        $this->seeJsonContains(['filePath' => $this->participantFileInfoTwo->fileInfo->getFullyPath()]);
        
        $taskReportEntry = [
            'id' => $this->taskReportOne->id,
            'Task_id' => $this->taskOne->id,
            'content' => $this->submitReportRequest['content'],
            'createdTime' => $this->taskReportOne->createdTime,
            'modifiedTime' => $this->currentTimeString(),
        ];
        $this->seeInDatabase('TaskReport', $taskReportEntry);
    }
    public function test_submitReport_reportAlreadyExist_noChanges_keepModifiedTime()
    {
        $this->taskReportOne->insert($this->connection);
        $this->taskReportAttachmentOne->insert($this->connection);
        
        $this->submitReportRequest['content'] = $this->taskReportOne->content;
        $this->submitReportRequest['attachments'] = [
            $this->participantFileInfoOne->id,
        ];
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['content' => $this->submitReportRequest['content']]);
        
        $this->seeJsonContains(['filePath' => $this->participantFileInfoOne->fileInfo->getFullyPath()]);
        $this->seeJsonDoesntContains(['filePath' => $this->participantFileInfoTwo->fileInfo->getFullyPath()]);
        
        $taskReportEntry = [
            'id' => $this->taskReportOne->id,
            'Task_id' => $this->taskOne->id,
            'content' => $this->submitReportRequest['content'],
            'createdTime' => $this->taskReportOne->createdTime,
            'modifiedTime' => $this->taskReportOne->modifiedTime,
        ];
        $this->seeInDatabase('TaskReport', $taskReportEntry);
    }
    public function test_submitReport_reportAlreadyExist_addNewAttachemnt()
    {
        $this->taskReportOne->insert($this->connection);
        
        $this->submitReportRequest['attachments'] = [
            $this->participantFileInfoOne->id,
        ];
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['filePath' => $this->participantFileInfoOne->fileInfo->getFullyPath()]);
        
        $taskReportAttachmentEntry = [
            'removed' => false,
            'ParticipantFileInfo_id' => $this->participantFileInfoOne->id,
        ];
        $this->seeInDatabase('TaskReportAttachment', $taskReportAttachmentEntry);
    }
    public function test_submitReport_reportAlreadyExist_removeObsoleteAttachemnt()
    {
        $this->taskReportOne->insert($this->connection);
        
        $this->taskReportAttachmentOne->insert($this->connection);
        
        $this->submitReportRequest['attachments'] = [];
        
        $this->submitReport();
        $this->seeStatusCode(200);
        
        $this->seeJsonDoesntContains(['filePath' => $this->participantFileInfoOne->fileInfo->getFullyPath()]);
        
        $taskReportAttachmentEntry = [
            'id' => $this->taskReportAttachmentOne->id,
            'removed' => true,
            'ParticipantFileInfo_id' => $this->participantFileInfoOne->id,
        ];
        $this->seeInDatabase('TaskReportAttachment', $taskReportAttachmentEntry);
    }
    
    //
    protected function viewAllTask()
    {
        $this->prepareRecord();
        
        $this->consultantTaskOne->consultant->personnel->insert($this->connection);
        $this->coordinatorTaskOne->coordinator->personnel->insert($this->connection);
        
        $this->consultantTaskOne->consultant->insert($this->connection);
        $this->coordinatorTaskOne->coordinator->insert($this->connection);
        
        $this->consultantTaskOne->insert($this->connection);
        $this->coordinatorTaskOne->insert($this->connection);
        
// echo $this->viewAllUri;
        $this->get($this->viewAllUri, $this->teamMember->client->token);
    }
    public function test_viewAll_200()
    {
$this->disableExceptionHandling();
        $this->viewAllTask();
        $this->seeStatusCode(200);
        
        $response = [
            'total' => '2',
            'list' => [
                [
                    'name' => $this->consultantTaskOne->task->name,
                    'description' => $this->consultantTaskOne->task->description,
                    'createdTime' => $this->consultantTaskOne->task->createdTime,
                    'modifiedTime' => $this->consultantTaskOne->task->modifiedTime,
                    'cancelled' => strval(intval($this->consultantTaskOne->task->cancelled)),
                    //
                    'consultantTaskId' => $this->consultantTaskOne->id,
                    'consultantId' => $this->consultantTaskOne->consultant->id,
                    'consultantPersonnelId' => $this->consultantTaskOne->consultant->personnel->id,
                    'consultantName' => $this->consultantTaskOne->consultant->personnel->getFullName(),
                    //
                    'coordinatorTaskId' => null,
                    'coordinatorId' => null,
                    'coordinatorPersonnelId' => null,
                    'coordinatorName' => null,
                ],
                [
                    'name' => $this->coordinatorTaskOne->task->name,
                    'description' => $this->coordinatorTaskOne->task->description,
                    'createdTime' => $this->coordinatorTaskOne->task->createdTime,
                    'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
                    'cancelled' => strval(intval($this->coordinatorTaskOne->task->cancelled)),
//                    //
                    'consultantTaskId' => null,
                    'consultantId' => null,
                    'consultantPersonnelId' => null,
                    'consultantName' => null,
//                    //
                    'coordinatorTaskId' => $this->coordinatorTaskOne->id,
                    'coordinatorId' => $this->coordinatorTaskOne->coordinator->id,
                    'coordinatorPersonnelId' => $this->coordinatorTaskOne->coordinator->personnel->id,
                    'coordinatorName' => $this->coordinatorTaskOne->coordinator->personnel->getFullName(),
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewAll_excludeUnmanagedTask_taskBelongsToOtherParticipant()
    {
        $program = $this->teamParticipant->participant->program;
        $otherParticipant = new \Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant($program, 'other');
        $otherParticipant->insert($this->connection);
        
        $this->coordinatorTaskOne->task->participant = $otherParticipant;
        
        $this->viewAllTask();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    public function test_viewAll_excludeUnmanagedTask_useAllFilter()
    {
$this->disableExceptionHandling();
        $from = (new \DateTime('-2 months'))->format('Y-m-d H:i:s');
        $to = (new \DateTime())->format('Y-m-d H:i:s');
        $this->viewAllUri .= "?cancelled=false"
                . "&completed=false"
                . "&from=$from"
                . "&to=$to"
                . "&keyword=task"
                . "&taskSource=CONSULTANT"
                . "&modifiedTimeOrder=DESC"
                . "&createdTimeOrder=ASC";
        
        $this->viewAllTask();
        $this->seeStatusCode(200);
        
        $this->seeJsonContains(['total' => '1']);
        $this->seeJsonContains(['consultantTaskId' => $this->consultantTaskOne->id]);
        $this->seeJsonDoesntContains(['coordinatorTaskId' => $this->coordinatorTaskOne->id]);
    }
    
    protected function viewConsultantTaskDetail()
    {
        $this->prepareRecord();
        
        $this->consultantTaskOne->consultant->personnel->insert($this->connection);
        $this->consultantTaskOne->consultant->insert($this->connection);
        $this->consultantTaskOne->insert($this->connection);
        
        $uri = $this->teamParticipantUri . "/consultant-tasks/{$this->consultantTaskOne->id}";
        $this->get($uri, $this->teamMember->client->token);
    }
    public function test_viewConsultantTaskDetail_200()
    {
        $this->viewConsultantTaskDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'consultant' => [
                'id' => $this->consultantTaskOne->consultant->id,
                'personnel' => [
                    'id' => $this->consultantTaskOne->consultant->personnel->id,
                    'name' => $this->consultantTaskOne->consultant->personnel->getFullName(),
                ],
            ],
            'id' => $this->consultantTaskOne->id,
            'cancelled' => $this->consultantTaskOne->task->cancelled,
            'name' => $this->consultantTaskOne->task->name,
            'description' => $this->consultantTaskOne->task->description,
            'createdTime' => $this->consultantTaskOne->task->createdTime,
            'modifiedTime' => $this->consultantTaskOne->task->modifiedTime,
            'taskReport' => null,
        ];
        $this->seeJsonContains($response);
    }
    
    protected function viewCoordinatorTaskDetail()
    {
        $this->prepareRecord();
        
        $this->coordinatorTaskOne->coordinator->personnel->insert($this->connection);
        $this->coordinatorTaskOne->coordinator->insert($this->connection);
        $this->coordinatorTaskOne->insert($this->connection);
        
        $uri = $this->teamParticipantUri . "/coordinator-tasks/{$this->coordinatorTaskOne->id}";
        $this->get($uri, $this->teamMember->client->token);
    }
    public function test_viewCoordinatorTaskDetail_200()
    {
        $this->viewCoordinatorTaskDetail();
        $this->seeStatusCode(200);
        
        $response = [
            'coordinator' => [
                'id' => $this->coordinatorTaskOne->coordinator->id,
                'personnel' => [
                    'id' => $this->coordinatorTaskOne->coordinator->personnel->id,
                    'name' => $this->coordinatorTaskOne->coordinator->personnel->getFullName(),
                ],
            ],
            'id' => $this->coordinatorTaskOne->id,
            'cancelled' => $this->coordinatorTaskOne->task->cancelled,
            'name' => $this->coordinatorTaskOne->task->name,
            'description' => $this->coordinatorTaskOne->task->description,
            'createdTime' => $this->coordinatorTaskOne->task->createdTime,
            'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
            'taskReport' => null,
        ];
        $this->seeJsonContains($response);
    }
}
