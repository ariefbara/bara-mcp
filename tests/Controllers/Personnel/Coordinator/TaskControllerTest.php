<?php

namespace Tests\Controllers\Personnel\Coordinator;

use Tests\Controllers\RecordPreparation\Firm\Client\RecordOfClientParticipant;
use Tests\Controllers\RecordPreparation\Firm\Program\Consultant\RecordOfConsultantTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Coordinator\RecordOfCoordinatorTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfParticipantFileInfo;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\RecordOfTask;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\RecordOfTaskReport;
use Tests\Controllers\RecordPreparation\Firm\Program\Participant\Task\TaskReport\RecordOfTaskReportAttachment;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfConsultant;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfCoordinator;
use Tests\Controllers\RecordPreparation\Firm\Program\RecordOfParticipant;
use Tests\Controllers\RecordPreparation\Firm\RecordOfClient;
use Tests\Controllers\RecordPreparation\Firm\RecordOfPersonnel;
use Tests\Controllers\RecordPreparation\Firm\RecordOfProgram;
use Tests\Controllers\RecordPreparation\Shared\RecordOfFileInfo;

class TaskControllerTest extends ExtendedCoordinatorTestCase
{

    protected $clientParticipantOne;
    protected $coordinatorTaskOne;
    protected $consultantTaskOne;
    protected $taskReportOne;
    protected $taskReportAttachmentOne;
    protected $taskReportAttachmentTwo;
    //
    protected $submitTaskRequest;
    protected $updateTaskRequest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();

        $this->connection->table('Consultant')->truncate();

        $this->connection->table('Task')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        $this->connection->table('ConsultantTask')->truncate();

        $this->connection->table('TaskReport')->truncate();

        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('ParticipantFileInfo')->truncate();
        $this->connection->table('TaskReportAttachment')->truncate();

        $program = $this->coordinator->program;
        $firm = $program->firm;

        $clientOne = new RecordOfClient($firm, 1);

        $personnelOne = new RecordOfPersonnel($firm, 1);

        $participantOne = new RecordOfParticipant($program, 1);

        $this->clientParticipantOne = new RecordOfClientParticipant($clientOne, $participantOne);

        $consultantOne = new RecordOfConsultant($program, $personnelOne, 1);

        $taskOne = new RecordOfTask($participantOne, 1);
        $taskTwo = new RecordOfTask($participantOne, 2);

        $this->coordinatorTaskOne = new RecordOfCoordinatorTask($this->coordinator, $taskOne);

        $this->consultantTaskOne = new RecordOfConsultantTask($consultantOne, $taskTwo);

        $this->taskReportOne = new RecordOfTaskReport($this->coordinatorTaskOne->task, 1);

        $fileInfoOne = new RecordOfFileInfo(1);
        $fileInfoTwo = new RecordOfFileInfo(2);

        $participantFileInfoOne = new RecordOfParticipantFileInfo($this->clientParticipantOne->participant, $fileInfoOne);
        $participantFileInfoTwo = new RecordOfParticipantFileInfo($this->clientParticipantOne->participant, $fileInfoTwo);

        $this->taskReportAttachmentOne = new RecordOfTaskReportAttachment($this->taskReportOne, $participantFileInfoOne,
                1);
        $this->taskReportAttachmentTwo = new RecordOfTaskReportAttachment($this->taskReportOne, $participantFileInfoTwo,
                2);

        $this->submitTaskRequest = [
            'participantId' => $this->clientParticipantOne->participant->id,
            'name' => 'new task name',
            'description' => 'new task description',
        ];

        $this->updateTaskRequest = [
            'name' => 'updated task name',
            'description' => 'updated task description',
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->connection->table('Client')->truncate();
        $this->connection->table('Participant')->truncate();
        $this->connection->table('ClientParticipant')->truncate();

        $this->connection->table('Consultant')->truncate();

        $this->connection->table('Task')->truncate();
        $this->connection->table('CoordinatorTask')->truncate();
        $this->connection->table('ConsultantTask')->truncate();

        $this->connection->table('TaskReport')->truncate();

        $this->connection->table('FileInfo')->truncate();
        $this->connection->table('ParticipantFileInfo')->truncate();
        $this->connection->table('TaskReportAttachment')->truncate();
    }

    //
    protected function submit()
    {
        $this->persistCoordinatorDependency();

        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);

        $uri = $this->coordinatorUri . "/tasks";
// echo $uri;
// echo json_encode($this->submitTaskRequest);
        $this->post($uri, $this->submitTaskRequest, $this->coordinator->personnel->token);
    }
    public function test_submit_201()
    {
        $this->disableExceptionHandling();
        $this->submit();
        $this->seeStatusCode(201);
// $this->seeJsonContains(['print']);

        $response = [
            'name' => $this->submitTaskRequest['name'],
            'description' => $this->submitTaskRequest['description'],
            'cancelled' => false,
            'createdTime' => $this->currentTimeString(),
            'modifiedTime' => $this->currentTimeString(),
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'taskReport' => null,
        ];
        $this->seeJsonContains($response);

        $taskEntry = [
            'name' => $this->submitTaskRequest['name'],
            'description' => $this->submitTaskRequest['description'],
            'cancelled' => false,
            'createdTime' => $this->currentTimeString(),
            'modifiedTime' => $this->currentTimeString(),
            'Participant_id' => $this->clientParticipantOne->participant->id,
        ];
        $this->seeInDatabase('Task', $taskEntry);
    }
    public function test_submit_emptyName_400()
    {
        $this->submitTaskRequest['name'] = '';

        $this->submit();
        $this->seeStatusCode(400);
    }
    public function test_submit_unusableParticipant_inactive_403()
    {
        $this->clientParticipantOne->participant->active = false;

        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_unusableParticipant_belongsToOtherProgram_403()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);

        $this->clientParticipantOne->participant->program = $otherProgram;

        $this->submit();
        $this->seeStatusCode(403);
    }
    public function test_submit_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;

        $this->submit();
        $this->seeStatusCode(403);
    }

    //
    protected function updateTask()
    {
        $this->persistCoordinatorDependency();

        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->coordinatorTaskOne->insert($this->connection);

        $uri = $this->coordinatorUri . "/tasks/{$this->coordinatorTaskOne->id}";
// echo $uri;
// echo json_encode($this->updateTaskRequest);
        $this->patch($uri, $this->updateTaskRequest, $this->coordinator->personnel->token);
    }
    public function test_updateTask_updateTask_200()
    {
        $this->disableExceptionHandling();
        $this->updateTask();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['print']);

        $response = [
            'id' => $this->coordinatorTaskOne->id,
            'name' => $this->updateTaskRequest['name'],
            'description' => $this->updateTaskRequest['description'],
            'cancelled' => false,
            'createdTime' => $this->coordinatorTaskOne->task->createdTime,
            'modifiedTime' => $this->currentTimeString(),
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'taskReport' => null,
        ];
        $this->seeJsonContains($response);

        $taskEntry = [
            'id' => $this->coordinatorTaskOne->id,
            'name' => $this->updateTaskRequest['name'],
            'description' => $this->updateTaskRequest['description'],
            'cancelled' => false,
            'createdTime' => $this->coordinatorTaskOne->task->createdTime,
            'modifiedTime' => $this->currentTimeString(),
            'Participant_id' => $this->clientParticipantOne->participant->id,
        ];
        $this->seeInDatabase('Task', $taskEntry);
    }
    public function test_updateTask_noChanges_dontUpdateModifiedTime()
    {
        $this->updateTaskRequest['name'] = $this->coordinatorTaskOne->task->name;
        $this->updateTaskRequest['description'] = $this->coordinatorTaskOne->task->description;

        $this->updateTask();
        $this->seeStatusCode(200);

        $this->seeJsonContains(['modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime]);

        $this->seeInDatabase('Task', ['modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime]);
    }
    public function test_updateTask_emptyName_400()
    {
        $this->updateTaskRequest['name'] = '';

        $this->updateTask();
        $this->seeStatusCode(400);
    }
    public function test_updateTask_unmanageTask_notOwned_403()
    {
        $program = $this->coordinator->program;

        $otherPersonnel = new RecordOfPersonnel($program->firm, 'other');
        $otherPersonnel->insert($this->connection);

        $otherCoordinator = new RecordOfCoordinator($program, $otherPersonnel, 'other');
        $otherCoordinator->insert($this->connection);

        $this->coordinatorTaskOne->coordinator = $otherCoordinator;

        $this->updateTask();
        $this->seeStatusCode(403);
    }
    public function test_updateTask_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;

        $this->updateTask();
        $this->seeStatusCode(403);
    }

    //
    protected function cancel()
    {
        $this->persistCoordinatorDependency();
        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->coordinatorTaskOne->insert($this->connection);

        $uri = $this->coordinatorUri . "/tasks/{$this->coordinatorTaskOne->task->id}";
// echo $uri;
        $this->delete($uri, [], $this->coordinator->personnel->token);
    }
    public function test_cancel_200()
    {
        $this->disableExceptionHandling();
        $this->cancel();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['print']);

        $response = [
            'id' => $this->coordinatorTaskOne->id,
            'name' => $this->coordinatorTaskOne->task->name,
            'description' => $this->coordinatorTaskOne->task->description,
            'cancelled' => true,
            'createdTime' => $this->coordinatorTaskOne->task->createdTime,
            'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'taskReport' => null,
        ];
        $this->seeJsonContains($response);

        $taskEntry = [
            'id' => $this->coordinatorTaskOne->id,
            'name' => $this->coordinatorTaskOne->task->name,
            'description' => $this->coordinatorTaskOne->task->description,
            'cancelled' => true,
            'createdTime' => $this->coordinatorTaskOne->task->createdTime,
            'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
            'Participant_id' => $this->clientParticipantOne->participant->id,
        ];
        $this->seeInDatabase('Task', $taskEntry);
    }
    public function test_cancelTask_unmanageTask_notOwned_403()
    {
        $program = $this->coordinator->program;

        $otherPersonnel = new RecordOfPersonnel($program->firm, 'other');
        $otherPersonnel->insert($this->connection);

        $otherCoordinator = new RecordOfCoordinator($program, $otherPersonnel, 'other');
        $otherCoordinator->insert($this->connection);

        $this->coordinatorTaskOne->coordinator = $otherCoordinator;

        $this->cancel();
        $this->seeStatusCode(403);
    }
    public function test_cancelTask_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;

        $this->cancel();
        $this->seeStatusCode(403);
    }

    //
    protected function viewCoordinatorTaskDetail()
    {
        $this->persistCoordinatorDependency();

        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        $this->coordinatorTaskOne->insert($this->connection);

        $uri = $this->coordinatorUri . "/coordinator-tasks/{$this->coordinatorTaskOne->id}";
// echo $uri;
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_viewCoordinatorTask_200()
    {
        $this->viewCoordinatorTaskDetail();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['print']);

        $response = [
            'id' => $this->coordinatorTaskOne->id,
            'name' => $this->coordinatorTaskOne->task->name,
            'description' => $this->coordinatorTaskOne->task->description,
            'cancelled' => $this->coordinatorTaskOne->task->cancelled,
            'createdTime' => $this->coordinatorTaskOne->task->createdTime,
            'modifiedTime' => $this->coordinatorTaskOne->task->modifiedTime,
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'coordinator' => [
                'id' => $this->coordinatorTaskOne->coordinator->id,
                'personnel' => [
                    'id' => $this->coordinatorTaskOne->coordinator->personnel->id,
                    'name' => $this->coordinatorTaskOne->coordinator->personnel->getFullName(),
                ],
            ],
            'taskReport' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewCoordinatorTaskDetail_taskContainReport_includeReportDetailInResult()
    {
        $this->taskReportOne->insert($this->connection);

        $this->taskReportAttachmentOne->participantFileInfo->insert($this->connection);
        $this->taskReportAttachmentTwo->participantFileInfo->insert($this->connection);

        $this->taskReportAttachmentOne->insert($this->connection);
        $this->taskReportAttachmentTwo->insert($this->connection);

        $this->viewCoordinatorTaskDetail();
        $this->seeStatusCode(200);

        $response = [
            'id' => $this->coordinatorTaskOne->id,
            'taskReport' => [
                'content' => $this->taskReportOne->content,
                'attachments' => [
                    [
                        'id' => $this->taskReportAttachmentOne->id,
                        'filePath' => $this->taskReportAttachmentOne->participantFileInfo->fileInfo->getFullyPath(),
                    ],
                    [
                        'id' => $this->taskReportAttachmentTwo->id,
                        'filePath' => $this->taskReportAttachmentTwo->participantFileInfo->fileInfo->getFullyPath(),
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewCoordinatorTaskDetail_unmanageParticipant_belongsToOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);

        $this->clientParticipantOne->participant->program = $otherProgram;

        $this->viewCoordinatorTaskDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewCoordinatorTaskDetail_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;

        $this->viewCoordinatorTaskDetail();
        $this->seeStatusCode(403);
    }
    
    //
    protected function viewConsultantTaskDetail()
    {
        $this->persistCoordinatorDependency();

        $this->clientParticipantOne->client->insert($this->connection);
        $this->clientParticipantOne->insert($this->connection);
        
        $this->consultantTaskOne->consultant->personnel->insert($this->connection);
        $this->consultantTaskOne->consultant->insert($this->connection);
        $this->consultantTaskOne->insert($this->connection);

        $uri = $this->coordinatorUri . "/consultant-tasks/{$this->consultantTaskOne->id}";
// echo $uri;
        $this->get($uri, $this->coordinator->personnel->token);
    }
    public function test_viewConsultantTask_200()
    {
$this->disableExceptionHandling();
        $this->viewConsultantTaskDetail();
        $this->seeStatusCode(200);
// $this->seeJsonContains(['print']);

        $response = [
            'id' => $this->consultantTaskOne->id,
            'name' => $this->consultantTaskOne->task->name,
            'description' => $this->consultantTaskOne->task->description,
            'cancelled' => $this->consultantTaskOne->task->cancelled,
            'createdTime' => $this->consultantTaskOne->task->createdTime,
            'modifiedTime' => $this->consultantTaskOne->task->modifiedTime,
            'participant' => [
                'id' => $this->clientParticipantOne->participant->id,
                'client' => [
                    'id' => $this->clientParticipantOne->client->id,
                    'name' => $this->clientParticipantOne->client->getFullName(),
                ],
                'team' => null,
                'user' => null,
            ],
            'consultant' => [
                'id' => $this->consultantTaskOne->consultant->id,
                'personnel' => [
                    'id' => $this->consultantTaskOne->consultant->personnel->id,
                    'name' => $this->consultantTaskOne->consultant->personnel->getFullName(),
                ],
            ],
            'taskReport' => null,
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewConsultantTaskDetail_taskContainReport_includeReportDetailInResult()
    {
        $this->taskReportOne->task = $this->consultantTaskOne->task;
        $this->taskReportOne->insert($this->connection);

        $this->taskReportAttachmentOne->participantFileInfo->insert($this->connection);
        $this->taskReportAttachmentTwo->participantFileInfo->insert($this->connection);

        $this->taskReportAttachmentOne->insert($this->connection);
        $this->taskReportAttachmentTwo->insert($this->connection);

        $this->viewConsultantTaskDetail();
        $this->seeStatusCode(200);

        $response = [
            'id' => $this->consultantTaskOne->id,
            'taskReport' => [
                'content' => $this->taskReportOne->content,
                'attachments' => [
                    [
                        'id' => $this->taskReportAttachmentOne->id,
                        'filePath' => $this->taskReportAttachmentOne->participantFileInfo->fileInfo->getFullyPath(),
                    ],
                    [
                        'id' => $this->taskReportAttachmentTwo->id,
                        'filePath' => $this->taskReportAttachmentTwo->participantFileInfo->fileInfo->getFullyPath(),
                    ],
                ],
            ],
        ];
        $this->seeJsonContains($response);
    }
    public function test_viewConsultantTaskDetail_unmanageParticipant_belongsToOtherProgram_404()
    {
        $otherProgram = new RecordOfProgram($this->personnel->firm, 'other');
        $otherProgram->insert($this->connection);

        $this->clientParticipantOne->participant->program = $otherProgram;

        $this->viewConsultantTaskDetail();
        $this->seeStatusCode(404);
    }
    public function test_viewConsultantTaskDetail_inactiveCoordinator_403()
    {
        $this->coordinator->active = false;

        $this->viewConsultantTaskDetail();
        $this->seeStatusCode(403);
    }

}
