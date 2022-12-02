<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\MentoringFilter;
use Query\Domain\Task\Dependency\MentoringFilter\DeclaredMentoringFilter;
use Query\Domain\Task\Dependency\MentoringListFilter;
use Query\Domain\Task\GenericQueryPayload;
use Query\Domain\Task\Personnel\MentoringListFilterForCoordinator;
use Query\Domain\Task\Personnel\ViewAllMentoringPayload;
use Query\Domain\Task\Personnel\ViewAllMentoringTask;
use Query\Domain\Task\Personnel\ViewMentoringListInCoordinatedPrograms;
use Query\Domain\Task\Personnel\ViewSummaryOfMentoringBelongsToPersonnel;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMentoringRepository;

class MentoringController extends PersonnelBaseController
{
    public function showAll()
    {
        $mentoringRepository = new CustomDoctrineMentoringRepository($this->em);
        
        $mentoringSlotFilter = new MentoringFilter\MentoringSlotFilter();
        $mentoringSlotFilterQuery = $this->request->query('mentoringSlotFilter');
        if (isset($mentoringSlotFilterQuery)) {
            if (isset($mentoringSlotFilterQuery['bookingAvailableStatus'])) {
                    $mentoringSlotFilter->setBookingAvailableStatus($this->filterBooleanOfVariable($mentoringSlotFilterQuery['bookingAvailableStatus']));
            }
            if (isset($mentoringSlotFilterQuery['cancelledStatus'])) {
                    $mentoringSlotFilter->setCancelledStatus($this->filterBooleanOfVariable($mentoringSlotFilterQuery['cancelledStatus']));
            }
            if (isset($mentoringSlotFilterQuery['reportCompletedStatus'])) {
                    $mentoringSlotFilter->setReportCompletedStatus($this->filterBooleanOfVariable($mentoringSlotFilterQuery['reportCompletedStatus']));
            }
        }
        
        $mentoringRequestFilter = new MentoringFilter\MentoringRequestFilter();
        $mentoringRequestFilterQuery = $this->request->query('mentoringRequestFilter');
        if (isset($mentoringRequestFilterQuery)) {
            if (isset($mentoringRequestFilterQuery['reportCompletedStatus'])) {
                $mentoringRequestFilter->setReportCompletedStatus($this->filterBooleanOfVariable($mentoringRequestFilterQuery['reportCompletedStatus']));
            }
            if (isset($mentoringRequestFilterQuery['requestStatusList'])) {
                foreach ($mentoringRequestFilterQuery['requestStatusList'] as $requestStatus) {
                    $mentoringRequestFilter->addRequestStatus($this->integerOfVariable($requestStatus));
                }
            }
        }
        
        $declaredMentoringFilter = new DeclaredMentoringFilter();
        $declaredMentoringFilterQuery = $this->request->query('declaredMentoringFilter');
        if (isset($declaredMentoringFilterQuery)) {
            if (isset($declaredMentoringFilterQuery['reportCompletedStatus'])) {
                $declaredMentoringFilter->setReportCompletedStatus($this->filterBooleanOfVariable($declaredMentoringFilterQuery['reportCompletedStatus']));
            }
            if (isset($declaredMentoringFilterQuery['declaredStatusList'])) {
                foreach ($declaredMentoringFilterQuery['declaredStatusList'] as $declaredStatus) {
                    $declaredMentoringFilter->addDeclaredStatus($this->integerOfVariable($declaredStatus));
                }
            }
        }
        
        $mentoringFilter = (new MentoringFilter($mentoringSlotFilter, $mentoringRequestFilter, $declaredMentoringFilter))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        
        $payload = new ViewAllMentoringPayload($this->getPage(), $this->getPageSize(), $mentoringFilter);
        $task = new ViewAllMentoringTask($mentoringRepository, $payload);
        $this->executePersonnelQueryTask($task);
        
        return $this->listQueryResponse($task->results);
    }
    
    public function mentoringListInCoordinatedPrograms()
    {
        $mentoringRepository = new CustomDoctrineMentoringRepository($this->em);
        $task = new ViewMentoringListInCoordinatedPrograms($mentoringRepository);
        
        $from = $this->dateTimeImmutableOfQueryRequest('from');
        $to = $this->dateTimeImmutableOfQueryRequest('to');
        $order = $this->stripTagQueryRequest('orders');
        $mentoringListFilter = (new MentoringListFilter($this->getPaginationFilter()))
                ->setFrom($from)
                ->setTo($to)
                ->setOrder($order);
        
        $programId = $this->stripTagQueryRequest('programId');
        $participantId = $this->stripTagQueryRequest('participantId');
        $reportSubmitted = $this->filterBooleanOfQueryRequest('reportSubmitted');
        $status = $this->stripTagQueryRequest('status');
        $filter = (new MentoringListFilterForCoordinator($mentoringListFilter))
                ->setProgramId($programId)
                ->setParticipantId($participantId)
                ->setStatus($status)
                ->setReportSubmitted($reportSubmitted);
        
        $typeList = $this->request->query('typeList') ?? [];
        foreach ($typeList as $type) {
            $filter->addType($type);
        }
        
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    public function summaryOfOwnedMentoring()
    {
        $mentoringRepository = new CustomDoctrineMentoringRepository($this->em);
        $task = new ViewSummaryOfMentoringBelongsToPersonnel($mentoringRepository);
        $payload = new GenericQueryPayload();
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->singleQueryResponse($payload->result);
    }
}
