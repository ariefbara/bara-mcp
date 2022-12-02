<?php

namespace App\Http\Controllers\Personnel;

use Query\Domain\Model\Firm\Program\Mission;
use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Personnel\MissionListInConsultedProgram;
use Query\Domain\Task\Personnel\MissionListInCoordinatedProgram;
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
    
    public function missionListInCoordinatedProgram()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $task = new MissionListInCoordinatedProgram($missionRepository);
        $payload = new CommonViewListPayload();
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
    
    public function missionListInConsultedProgram()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $task = new MissionListInConsultedProgram($missionRepository);
        $payload = new CommonViewListPayload();
        $this->executePersonalQueryTask($task, $payload);
        
        return $this->listQueryResponse($payload->result);
    }
}
