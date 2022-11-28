<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantSummaryListFilter;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantSummaryListFilterForCoordinator;
use Query\Domain\Task\Personnel\ViewParticipantSummaryListInCoordinatedProgram;

class ParticipantController extends PersonnelBaseController
{
    public function viewSummaryListInCoordinatedProgram()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ViewParticipantSummaryListInCoordinatedProgram($participantRepository);

        $name = $this->stripTagQueryRequest('name');
        $missionCompletionFrom = $this->integerOfQueryRequest('missionCompletionFrom');
        $missionCompletionTo = $this->integerOfQueryRequest('missionCompletionTo');
        $metricAchievementFrom = $this->integerOfQueryRequest('metricAchievementFrom');
        $metricAchievementTo = $this->integerOfQueryRequest('metricAchievementTo');
        $order = $this->stripTagQueryRequest('order');
        $participantSummaryListFilter = (new ParticipantSummaryListFilter($this->getPaginationFilter()))
                ->setName($name)
                ->setMissionCompletionFrom($missionCompletionFrom)
                ->setMissionCompletionTo($missionCompletionTo)
                ->setMetricAchievementFrom($metricAchievementFrom)
                ->setMetricAchievementTo($metricAchievementTo)
                ->setOrder($order);
        
        $programId = $this->stripTagQueryRequest('programId');
        $filter = (new ParticipantSummaryListFilterForCoordinator($participantSummaryListFilter))
                ->setProgramId($programId);
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
}
