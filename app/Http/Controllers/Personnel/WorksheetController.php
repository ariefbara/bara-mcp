<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetListFilter;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetListFilterForConsultant;
use Query\Domain\Task\Dependency\Firm\Program\Participant\WorksheetListFilterForCoordinator;
use Query\Domain\Task\Personnel\ViewWorksheetListInCoordinatedProgram;
use Query\Domain\Task\Personnel\ViewWorksheetListInMentoredPrograms;
use Resources\PaginationFilter;

class WorksheetController extends PersonnelBaseController
{

    public function viewListInCoordinatedProgram()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $task = new ViewWorksheetListInCoordinatedProgram($worksheetRepository);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        
        $missionId = $this->stripTagQueryRequest('missionId');
        $reviewedStatus = $this->filterBooleanOfQueryRequest('reviewedStatus');
        $order = $this->stripTagQueryRequest('order');
        $worksheetListFilter = (new WorksheetListFilter($paginationFilter))
                ->setMissionId($missionId)
                ->setReviewedStatus($reviewedStatus)
                ->setOrder($order);
        
        $programId = $this->stripTagQueryRequest('programId');
        $participantId = $this->stripTagQueryRequest('participantId');
        $filter = (new WorksheetListFilterForCoordinator($worksheetListFilter))
                ->setProgramId($programId)
                ->setParticipantId($participantId);
        
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }

    public function viewListInConsultedProgram()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $task = new ViewWorksheetListInMentoredPrograms($worksheetRepository);
        
        $paginationFilter = new PaginationFilter($this->getPage(), $this->getPageSize());
        
        $missionId = $this->stripTagQueryRequest('missionId');
        $reviewedStatus = $this->filterBooleanOfQueryRequest('reviewedStatus');
        $order = $this->stripTagQueryRequest('order');
        $worksheetListFilter = (new WorksheetListFilter($paginationFilter))
                ->setMissionId($missionId)
                ->setReviewedStatus($reviewedStatus)
                ->setOrder($order);
        
        $programId = $this->stripTagQueryRequest('programId');
        $participantId = $this->stripTagQueryRequest('participantId');
        $filter = (new WorksheetListFilterForConsultant($worksheetListFilter))
                ->setProgramId($programId)
                ->setParticipantId($participantId);
        $onlyDedicatedMenteeFilter = $this->filterBooleanOfQueryRequest('onlyDedicatedMentee');
        if ($onlyDedicatedMenteeFilter) {
            $filter->setOnlyDedicatedMenteeWorksheets();
        }
        
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }

}
