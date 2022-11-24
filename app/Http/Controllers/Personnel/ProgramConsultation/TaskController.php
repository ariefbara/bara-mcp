<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantTask as ConsultantTask2;
use Personnel\Domain\Model\Firm\Program\Participant;
use Personnel\Domain\Task\Mentor\CancelTask;
use Personnel\Domain\Task\Mentor\SubmitTask;
use Personnel\Domain\Task\Mentor\SubmitTaskPayload;
use Personnel\Domain\Task\Mentor\UpdateTask;
use Personnel\Domain\Task\Mentor\UpdateTaskPayload;
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
    public function submitTask($consultantId)
    {
        $consultantTaskRepository = $this->em->getRepository(ConsultantTask2::class);
        $participantRepository = $this->em->getRepository(Participant::class);
        
        $task = new SubmitTask($consultantTaskRepository, $participantRepository);
        
        $participantId = $this->stripTagsInputRequest('participantId');
        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $payload = new SubmitTaskPayload($participantId, $labelData);
        $this->executeExtendedMentorTaskInPersonnelContext($consultantId, $task, $payload);
        
        $consultantTaskRepository = $this->em->getRepository(ConsultantTask::class);
        $queryTask = new ViewConsultantTask($consultantTaskRepository);
        $queryPayload = new CommonViewDetailPayload($payload->submittedTaskId);
        $this->executeProgramQueryTaskAsConsultant($consultantId, $queryTask, $queryPayload);
        
        return $this->commandCreatedResponse($this->arrayDataOfConsultantTask($queryPayload->result));
    }
    
    public function updateTask($consultantId, $id)
    {
        $consultantTaskRepository = $this->em->getRepository(ConsultantTask2::class);
        $task = new UpdateTask($consultantTaskRepository);
        
        $labelData = new LabelData($this->stripTagsInputRequest('name'), $this->stripTagsInputRequest('description'));
        $payload = new UpdateTaskPayload($id, $labelData);
        
        $this->executeExtendedMentorTaskInPersonnelContext($consultantId, $task, $payload);
        
        return $this->viewConsultantTaskDetail($consultantId, $id);
    }
    
    public function cancelTask($consultantId, $id)
    {
        $consultantTaskRepository = $this->em->getRepository(ConsultantTask2::class);
        $task = new CancelTask($consultantTaskRepository);
        
        $this->executeExtendedMentorTaskInPersonnelContext($consultantId, $task, $id);
        
        return $this->viewConsultantTaskDetail($consultantId, $id);
    }
    
    public function viewCoordinatorTaskDetail($consultantId, $id)
    {
        $coordinatorTaskRepository = $this->em->getRepository(CoordinatorTask::class);
        $task = new ViewCoordinatorTask($coordinatorTaskRepository);
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeProgramQueryTaskAsConsultant($consultantId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinatorTask($payload->result));
    }
    
    public function viewConsultantTaskDetail($consultantId, $id)
    {
        $consultantTaskRepository = $this->em->getRepository(ConsultantTask::class);
        $task = new ViewConsultantTask($consultantTaskRepository);
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeProgramQueryTaskAsConsultant($consultantId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultantTask($payload->result));
    }
    
    public function viewAllTaskInProgram($consultantId)
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
        
        $this->executeProgramQueryTaskAsConsultant($consultantId, $task, $payload);
        
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
