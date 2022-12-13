<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant\Task;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilterForConsultant;
use Query\Domain\Task\Dependency\Firm\Program\Participant\TaskListFilterForCoordinator;
use Query\Domain\Task\Personnel\ViewAllRelevantTaskAsProgramConsultant;
use Query\Domain\Task\Personnel\ViewAllTaskInCoordinatoredProgram;
use Resources\PaginationFilter;
use Resources\QueryOrder;

class TaskController extends PersonnelBaseController
{
    
    public function viewTaskListInAllCoordinatedProgram()
    {
        $taskRepository = $this->em->getRepository(Task::class);
        $task = new ViewAllTaskInCoordinatoredProgram($taskRepository);
        
        $programId = $this->stripTagQueryRequest('programId');
        $participantId = $this->stripTagQueryRequest('participantId');
        $filter = (new TaskListFilterForCoordinator($this->getTaskListFilter()))
                ->setParticipantId($participantId)
                ->setProgramId($programId);
        if ($this->filterBooleanOfQueryRequest('onlyShowOwnedTask')) {
            $filter->setOnlyShowOwnedTask();
        }
        
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    public function viewTaskListInAllConsultedProgram()
    {
        $taskRepository = $this->em->getRepository(Task::class);
        $task = new ViewAllRelevantTaskAsProgramConsultant($taskRepository);
        
        $programId = $this->stripTagQueryRequest('programId');
        $participantId = $this->stripTagQueryRequest('participantId');
        $filter = (new TaskListFilterForConsultant($this->getTaskListFilter()))
                ->setProgramId($programId)
                ->setParticipantId($participantId);
        
//force to show owned task or task for dedicated mentee only
        $filter->setOnlyShowRelevantTask();
//        if ($this->filterBooleanOfQueryRequest('onlyShowRelevantTask')) {
//            $filter->setOnlyShowRelevantTask();
//        }
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    protected function getTaskListFilter()
    {
        $cancelledStatus = $this->filterBooleanOfQueryRequest('cancelled');
        $completedStatus = $this->filterBooleanOfQueryRequest('completed');
        $modifiedTimeFrom = $this->dateTimeImmutableOfQueryRequest('modifiedTimeFrom');
        $modifiedTimeTo = $this->dateTimeImmutableOfQueryRequest('modifiedTimeTo');
        $dueDateFrom = $this->dateTimeImmutableOfQueryRequest('dueDateFrom');
        $dueDateTo = $this->dateTimeImmutableOfQueryRequest('dueDateTo');
        $keyword = $this->stripTagQueryRequest('keyword');
        $taskSource = $this->stripTagQueryRequest('taskSource');
        $order = $this->stripTagQueryRequest('order');
        
        return (new TaskListFilter($this->getPaginationFilter()))
                ->setCancelledStatus($cancelledStatus)
                ->setCompletedStatus($completedStatus)
                ->setModifiedTimeFrom($modifiedTimeFrom)
                ->setModifiedTimeTo($modifiedTimeTo)
                ->setDueDateFrom($dueDateFrom)
                ->setDueDateTo($dueDateTo)
                ->setKeyword($keyword)
                ->setTaskSource($taskSource)
                ->setOrder($order);
    }
}
