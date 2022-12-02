<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Personnel\Domain\Model\Firm\Personnel\Coordinator\CoordinatorTask as CoordinatorTask2;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Coordinator\ApproveTaskReport;
use Personnel\Domain\Task\Coordinator\AskForTaskReportRevision;
use Personnel\Domain\Task\Coordinator\CancelTask;
use Personnel\Domain\Task\Coordinator\SubmitTask;
use Personnel\Domain\Task\Coordinator\SubmitTaskPayload;
use Personnel\Domain\Task\Coordinator\UpdateTask;
use Personnel\Domain\Task\Coordinator\UpdateTaskPayload;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantTask;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorTask;
use Query\Domain\Model\Firm\Program\Participant\Task;
use Query\Domain\Model\Firm\Program\Participant\Task\TaskReport;
use Query\Domain\Model\Firm\Program\Participant\Task\TaskReport\TaskReportAttachment;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\User\UserParticipant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilter;
use Query\Domain\Task\InProgram\ViewAllTask;
use Query\Domain\Task\InProgram\ViewConsultantTask;
use Query\Domain\Task\InProgram\ViewCoordinatorTask;
use Resources\PaginationFilter;
use Resources\QueryOrder;
use SharedContext\Domain\ValueObject\LabelData;

class TaskController extends PersonnelBaseController
{
    public function submitTask($coordinatorId)
    {
        $coordinatorTaskRepository = $this->em->getRepository(CoordinatorTask2::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        
        $task = new SubmitTask($coordinatorTaskRepository, $participantRepository);
        
        $participantId = $this->stripTagsInputRequest('participantId');
        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $payload = new SubmitTaskPayload($participantId, $labelData);
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $payload);
        
        $coordinatorTaskQueryRepository = $this->em->getRepository(CoordinatorTask::class);
        $queryTask = new ViewCoordinatorTask($coordinatorTaskQueryRepository);
        $queryPayload = new CommonViewDetailPayload($payload->submittedTaskId);
        
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $queryTask, $queryPayload);
        return $this->commandCreatedResponse($this->arrayDataOfCoordinatorTask($queryPayload->result));
    }
    
    public function updateTask($coordinatorId, $id)
    {
        $coordinatorTaskRepository = $this->em->getRepository(CoordinatorTask2::class);
        $task = new UpdateTask($coordinatorTaskRepository);
        
        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $payload = new UpdateTaskPayload($id, $labelData);
        
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $payload);
        
        return $this->viewCoordinatorTaskDetail($coordinatorId, $id);
    }
    
    public function approveTaskReport($coordinatorId, $id)
    {
        $coordinatorTaskRepository = $this->em->getRepository(CoordinatorTask2::class);
        $task = new ApproveTaskReport($coordinatorTaskRepository);
        
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);
        
        return $this->viewCoordinatorTaskDetail($coordinatorId, $id);
    }
    
    public function askForTaskReportRevision($coordinatorId, $id)
    {
        $coordinatorTaskRepository = $this->em->getRepository(CoordinatorTask2::class);
        $task = new AskForTaskReportRevision($coordinatorTaskRepository);
        
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);
        
        return $this->viewCoordinatorTaskDetail($coordinatorId, $id);
    }
    
    public function cancelTask($coordinatorId, $id)
    {
        $coordinatorTaskRepository = $this->em->getRepository(CoordinatorTask2::class);
        $task = new CancelTask($coordinatorTaskRepository);
        
        $this->executeCoordinatorTaskInPersonnelBC($coordinatorId, $task, $id);
        
        return $this->viewCoordinatorTaskDetail($coordinatorId, $id);
    }
    
    public function viewCoordinatorTaskDetail($coordinatorId, $id)
    {
        $coordinatorTaskRepository = $this->em->getRepository(CoordinatorTask::class);
        $task = new ViewCoordinatorTask($coordinatorTaskRepository);
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinatorTask($payload->result));
    }
    
    public function viewConsultantTaskDetail($coordinatorId, $id)
    {
        $consultantTaskRepository = $this->em->getRepository(ConsultantTask::class);
        $task = new ViewConsultantTask($consultantTaskRepository);
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultantTask($payload->result));
    }
    
    public function viewAllTaskInProgram($coordinatorId)
    {
        $taskRepository = $this->em->getRepository(Task::class);
        $task = new ViewAllTask($taskRepository);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelled');
        $completedStatus = $this->filterBooleanOfQueryRequest('completed');
        $modifiedTimeOrder = $this->stripTagQueryRequest('modifiedTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('modifiedTimeOrder')) : null;
        $createdTimeOrder = $this->stripTagQueryRequest('createdTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('createdTimeOrder')) : null;
        $participantId = $this->stripTagQueryRequest('participantId');
        
        $filter = (new TaskListFilter($paginationFilter))
                ->setCancelledStatus($cancelledStatus)
                ->setCompletedStatus($completedStatus)
                ->setModifiedTimeOrder($modifiedTimeOrder)
                ->setCreatedTimeOrder($createdTimeOrder)
                ->setParticipantId($participantId);
        $payload = new CommonViewListPayload($filter);
        
        $this->executeProgramQueryTaskAsCoordinator($coordinatorId, $task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    protected function arrayDataOfCoordinatorTask(CoordinatorTask $coordinatorTask): array
    {
        $data = $this->arrayDataOfTask($coordinatorTask->getTask());
        $data['coordinator'] = [
            'id' => $coordinatorTask->getCoordinator()->getId(),
            'personnel' => [
                'id' => $coordinatorTask->getCoordinator()->getPersonnel()->getId(),
                'name' => $coordinatorTask->getCoordinator()->getPersonnel()->getName(),
            ],
        ];
        return $data;
    }
    protected function arrayDataOfConsultantTask(ConsultantTask $consultantTask): array
    {
        $data = $this->arrayDataOfTask($consultantTask->getTask());
        $data['consultant'] = [
            'id' => $consultantTask->getConsultant()->getId(),
            'personnel' => [
                'id' => $consultantTask->getConsultant()->getPersonnel()->getId(),
                'name' => $consultantTask->getConsultant()->getPersonnel()->getName(),
            ],
        ];
        return $data;
    }
    protected function arrayDataOfTask(Task $task): array
    {
        return [
            'id' => $task->getId(),
            'cancelled' => $task->isCancelled(),
            'name' => $task->getLabel()->getName(),
            'description' => $task->getLabel()->getDescription(),
            'createdTime' => $task->getCreatedTime()->format('Y-m-d H:i:s'),
            'modifiedTime' => $task->getModifiedTime()->format('Y-m-d H:i:s'),
            'participant' => [
                'id' => $task->getParticipant()->getId(),
                'client' => $this->arrayDataOfClient($task->getParticipant()->getClientParticipant()),
                'team' => $this->arrayDataOfClient($task->getParticipant()->getTeamParticipant()),
                'user' => $this->arrayDataOfClient($task->getParticipant()->getUserParticipant()),
            ],
            'taskReport' => $this->arrayDataOfTaskReport($task->getTaskReport())
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }
    protected function arrayDataOfTaskReport(?TaskReport $taskReport): ?array
    {
        if (empty($taskReport)) {
            return null;
        }
        $attachments = [];
        foreach ($taskReport->iterateActiveAttachments() as $attachment) {
            $attachments[] = $this->arrayDataOfTaskReportAttachment($attachment);
        }
        return [
            'content' => $taskReport->getContent(),
            'reviewStatus' => $taskReport->getReviewStatus()->getDisplayValue(),
            'attachments' => $attachments,
        ];
    }
    protected function arrayDataOfTaskReportAttachment(TaskReportAttachment $attachment): array
    {
        return [
            'id' => $attachment->getId(),
            'filePath' => $attachment->getParticipantFileInfo()->getFullyQualifiedFileName(),
        ];
    }
}
