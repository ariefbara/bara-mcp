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
        
        $mentoringFilter = (new MentoringFilter())
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        $mentoringSlotFilterRequest = $this->request->query('mentoringSlotFilter');
        if (isset($mentoringSlotFilterRequest)) {
            $mentoringSlotFilter = new MentoringFilter\MentoringSlotFilter();
            if (isset($mentoringSlotFilterRequest['bookingAvailableStatus'])) {
                    $mentoringSlotFilter->setBookingAvailableStatus($this->filterBooleanOfVariable($mentoringSlotFilterRequest['bookingAvailableStatus']));
            }
            if (isset($mentoringSlotFilterRequest['cancelledStatus'])) {
                    $mentoringSlotFilter->setBookingAvailableStatus($this->filterBooleanOfVariable($mentoringSlotFilterRequest['cancelledStatus']));
            }
            if (isset($mentoringSlotFilterRequest['reportCompletedStatus'])) {
                    $mentoringSlotFilter->setBookingAvailableStatus($this->filterBooleanOfVariable($mentoringSlotFilterRequest['reportCompletedStatus']));
            }
            $mentoringFilter->setMentoringSlotFilter($mentoringSlotFilter);
        }
        
        $payload = new ViewAllMentoringPayload($this->getPage(), $this->getPageSize(), $mentoringFilter);
        $task = new ViewAllMentoringTask($mentoringRepository, $payload);
        $this->executePersonnelQueryTask($task);
        
        return $this->listQueryResponse($task->results);
    }
}
