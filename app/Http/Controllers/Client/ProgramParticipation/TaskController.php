<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Participant\Domain\Model\Participant\ParticipantFileInfo;
use Participant\Domain\Model\Participant\Task as Task2;
use Participant\Domain\Task\Participant\SubmitTaskReport;
use Participant\Domain\Task\Participant\SubmitTaskReportPayload;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantTask;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorTask;
use Query\Domain\Model\Firm\Program\Participant\Task;
use Query\Domain\Model\Firm\Program\Participant\Task\TaskReport;
use Query\Domain\Model\Firm\Program\Participant\Task\TaskReport\TaskReportAttachment;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilter;
use Query\Domain\Task\Participant\ViewAllTask;
use Query\Domain\Task\Participant\ViewConsultantTaskDetail;
use Query\Domain\Task\Participant\ViewCoordinatorTaskDetail;
use Query\Domain\Task\Participant\ViewTaskDetail;
use Resources\PaginationFilter;
use Resources\QueryOrder;

class TaskController extends ClientParticipantBaseController
{
    public function viewAllTasks($programParticipationId)
    {
        $taskRepository = $this->em->getRepository(Task::class);
        $task = new ViewAllTask($taskRepository);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelled');
        $completedStatus = $this->filterBooleanOfQueryRequest('completed');
        $from = $this->dateTimeImmutableOfQueryRequest('from');
        $to = $this->dateTimeImmutableOfQueryRequest('to');
        $keyword = $this->stripTagQueryRequest('keyword');
        $taskSource = $this->stripTagQueryRequest('taskSource');
        $modifiedTimeOrder = $this->stripTagQueryRequest('modifiedTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('modifiedTimeOrder')) : null;
        $createdTimeOrder = $this->stripTagQueryRequest('createdTimeOrder') ?
                new QueryOrder($this->stripTagQueryRequest('createdTimeOrder')) : null;
        $filter = (new TaskListFilter($paginationFilter))
                ->setCancelledStatus($cancelledStatus)
                ->setCompletedStatus($completedStatus)
                ->setFrom($from)
                ->setTo($to)
                ->setKeyword($keyword)
                ->setTaskSource($taskSource)
                ->setModifiedTimeOrder($modifiedTimeOrder)
                ->setCreatedTimeOrder($createdTimeOrder);
        
        $payload = new CommonViewListPayload($filter);
        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);
        
        return $payload->result;
    }
    
    public function viewConsultantTaskDetail($programParticipationId, $id)
    {
        $consultantTaskRepository = $this->em->getRepository(ConsultantTask::class);
        $task = new ViewConsultantTaskDetail($consultantTaskRepository);
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfConsultantTask($payload->result));
    }
    
    public function viewCoordinatorTaskDetail($programParticipationId, $id)
    {
        $coordinatorTaskRepository = $this->em->getRepository(CoordinatorTask::class);
        $task = new ViewCoordinatorTaskDetail($coordinatorTaskRepository);
        $payload = new CommonViewDetailPayload($id);
        
        $this->executeParticipantQueryTask($programParticipationId, $task, $payload);
        
        return $this->singleQueryResponse($this->arrayDataOfCoordinatorTask($payload->result));
    }
    
    public function submitReport($programParticipationId, $id)
    {
        $taskRepository = $this->em->getRepository(Task2::class);
        $participantFileInfoRepository = $this->em->getRepository(ParticipantFileInfo::class);
        $task = new SubmitTaskReport($taskRepository, $participantFileInfoRepository);
        
        $content = $this->stripTagsInputRequest('content');
        $payload = new SubmitTaskReportPayload($id, $content);
        $attachments = $this->request->input(['attachments']);
        if (isset($attachments)) {
            foreach ($attachments as $participantFileInfoId) {
                $payload->attachParticipantFileInfoId($participantFileInfoId);
            }
        }
        
        $this->executeClientParticipantTask($programParticipationId, $task, $payload);

        $queryTaskRepository = $this->em->getRepository(Task::class);
        $queryTask = new ViewTaskDetail($queryTaskRepository);
        $queryPayload = new CommonViewDetailPayload($id);
        $this->executeParticipantQueryTask($programParticipationId, $queryTask, $queryPayload);
        
        return $this->singleQueryResponse($this->arrayDataOfTask($queryPayload->result));
    }
    
    //
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
            'taskReport' => $this->arrayDataOfTaskReport($task->getTaskReport())
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
