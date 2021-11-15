<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Query\Domain\Task\Dependency\MentoringFilter;
use Query\Domain\Task\Dependency\MentoringFilter\MentoringSlotFilter;
use Query\Domain\Task\Participant\ShowAllMentoringPayload;
use Query\Domain\Task\Participant\ShowAllMentoringTask;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMentoringRepository;

class MentoringController extends ClientParticipantBaseController
{
    public function showAll($programParticipationId)
    {
        $mentoringRepository = new CustomDoctrineMentoringRepository($this->em);
        
        $filter = (new MentoringFilter())
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        $mentoringSlotFilterRequest = $this->request->query('mentoringSlotFilter');
        if (isset($mentoringSlotFilterRequest)) {
            $mentoringSlotFilter = new MentoringSlotFilter();
            if (isset($mentoringSlotFilterRequest['cancelledStatus'])) {
                    $mentoringSlotFilter->setCancelledStatus($this->filterBooleanOfVariable($mentoringSlotFilterRequest['cancelledStatus']));
            }
            if (isset($mentoringSlotFilterRequest['reportCompletedStatus'])) {
                    $mentoringSlotFilter->setReportCompletedStatus($this->filterBooleanOfVariable($mentoringSlotFilterRequest['reportCompletedStatus']));
            }
            $filter->setMentoringSlotFilter($mentoringSlotFilter);
        }
        
        $payload = new ShowAllMentoringPayload($this->getPage(), $this->getPageSize(), $filter);
        $task = new ShowAllMentoringTask($mentoringRepository, $payload);
        $this->executeQueryParticipantTask($programParticipationId, $task);
        
        return $this->listQueryResponse($task->results);
    }
}
