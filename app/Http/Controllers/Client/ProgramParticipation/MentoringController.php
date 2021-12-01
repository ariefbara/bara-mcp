<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use Query\Domain\Task\Dependency\MentoringFilter;
use Query\Domain\Task\Dependency\MentoringFilter\MentoringRequestFilter;
use Query\Domain\Task\Dependency\MentoringFilter\MentoringSlotFilter;
use Query\Domain\Task\Participant\ShowAllMentoringPayload;
use Query\Domain\Task\Participant\ShowAllMentoringTask;
use Query\Infrastructure\Persistence\Doctrine\Repository\CustomDoctrineMentoringRepository;

class MentoringController extends ClientParticipantBaseController
{
    public function showAll($programParticipationId)
    {
        $mentoringRepository = new CustomDoctrineMentoringRepository($this->em);
        
        $mentoringSlotFilter = new MentoringSlotFilter();
        $mentoringSlotFilterQuery = $this->request->query('mentoringSlotFilter');
        if (isset($mentoringSlotFilterQuery)) {
            if (isset($mentoringSlotFilterQuery['cancelledStatus'])) {
                    $mentoringSlotFilter->setCancelledStatus($this->filterBooleanOfVariable($mentoringSlotFilterQuery['cancelledStatus']));
            }
            if (isset($mentoringSlotFilterQuery['reportCompletedStatus'])) {
                    $mentoringSlotFilter->setReportCompletedStatus($this->filterBooleanOfVariable($mentoringSlotFilterQuery['reportCompletedStatus']));
            }
        }
        
        $mentoringRequestFilter = new MentoringRequestFilter();
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
        
        $filter = (new MentoringFilter($mentoringSlotFilter, $mentoringRequestFilter))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'))
                ->setOrderDirection($this->stripTagQueryRequest('order'));
        
        $payload = new ShowAllMentoringPayload($this->getPage(), $this->getPageSize(), $filter);
        $task = new ShowAllMentoringTask($mentoringRepository, $payload);
        $this->executeQueryParticipantTask($programParticipationId, $task);
        
        return $this->listQueryResponse($task->results);
    }
}
