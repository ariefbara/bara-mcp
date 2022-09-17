<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Mission;
use Query\Domain\Task\Personnel\ViewAllMissionWithDiscussionOverview;
use Query\Domain\Task\Personnel\ViewAllMissionWithDiscussionOverviewPayload;

class MissionController extends PersonnelBaseController
{
    public function viewDiscussionOverview()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $payload = new ViewAllMissionWithDiscussionOverviewPayload($this->getPage(), $this->getPageSize());
        $task = new ViewAllMissionWithDiscussionOverview($missionRepository, $payload);
        $this->executePersonnelQueryTask($task);
        
        return $this->listQueryResponse($payload->result);
    }
}
