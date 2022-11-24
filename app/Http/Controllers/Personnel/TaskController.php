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
    
    public function viewAllTaskInCoordinatedProgram()
    {
        $taskRepository = $this->em->getRepository(Task::class);
        $task = new ViewAllTaskInCoordinatoredProgram($taskRepository);
        
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
        
        $taskListFilter = (new TaskListFilter($paginationFilter))
                ->setCancelledStatus($cancelledStatus)
                ->setCompletedStatus($completedStatus)
                ->setFrom($from)
                ->setTo($to)
                ->setKeyword($keyword)
                ->setTaskSource($taskSource)
                ->setModifiedTimeOrder($modifiedTimeOrder)
                ->setCreatedTimeOrder($createdTimeOrder);
        $participantId = $this->stripTagQueryRequest('participantId');
        
        $filter = (new TaskListFilterForCoordinator($taskListFilter))
                ->setParticipantId($participantId);
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    public function viewAllRelevanTaskAsProgramConsultant()
    {
        $taskRepository = $this->em->getRepository(Task::class);
        $task = new ViewAllRelevantTaskAsProgramConsultant($taskRepository);
        
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
        
        $taskListFilter = (new TaskListFilter($paginationFilter))
                ->setCancelledStatus($cancelledStatus)
                ->setCompletedStatus($completedStatus)
                ->setFrom($from)
                ->setTo($to)
                ->setKeyword($keyword)
                ->setTaskSource($taskSource)
                ->setModifiedTimeOrder($modifiedTimeOrder)
                ->setCreatedTimeOrder($createdTimeOrder);
        $participantId = $this->stripTagQueryRequest('participantId');
        
        $filter = (new TaskListFilterForConsultant($taskListFilter))
                ->setParticipantId($participantId);
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
}
