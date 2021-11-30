<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Task\Dependency\MentoringFilter;
use Query\Domain\Task\Personnel\ViewAllMentoringPayload;
use Query\Domain\Task\Personnel\ViewAllMentoringTask;
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
        
        $mentoringFilter = (new MentoringFilter($mentoringSlotFilter, $mentoringRequestFilter))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        
        $payload = new ViewAllMentoringPayload($this->getPage(), $this->getPageSize(), $mentoringFilter);
        $task = new ViewAllMentoringTask($mentoringRepository, $payload);
        $this->executePersonnelQueryTask($task);
        
        return $this->listQueryResponse($task->results);
    }
}
