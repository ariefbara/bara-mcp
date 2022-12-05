<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantListFilter;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantSummaryListFilter;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantSummaryListFilterForCoordinator;
use Query\Domain\Task\Personnel\ViewDedicatedMenteeList;
use Query\Domain\Task\Personnel\ViewParticipantListInCoordinatedProgram;
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
    
    public function ListInCoordinatedProgram()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ViewParticipantListInCoordinatedProgram($participantRepository);
        
        $programId = $this->stripTagQueryRequest('programId');
        $name = $this->stripTagQueryRequest('name');
        $filter = (new ParticipantListFilter())
                ->setProgramId($programId)
                ->setName($name);
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    public function dedicatedMenteeList()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        $task = new ViewDedicatedMenteeList($participantRepository);
        
        $programId = $this->stripTagQueryRequest('programId');
        $name = $this->stripTagQueryRequest('name');
        $filter = (new ParticipantListFilter())
                ->setProgramId($programId)
                ->setName($name);
        $payload = new CommonViewListPayload($filter);
        
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
}
