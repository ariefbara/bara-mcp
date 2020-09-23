<?php

namespace App\Http\Controllers\Client;

use Client\ {
    Application\Service\Client\QuitTeamMembership,
    Domain\Model\Client\TeamMembership
};
use Query\ {
    Application\Service\Firm\Client\ViewTeamMembership,
    Domain\Model\Firm\Team\Member
};

class TeamMembershipController extends ClientBaseController
{

    public function quit($teamMembershipId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->firmId(), $this->clientId(), $teamMembershipId);
        return $this->commandOkResponse();
    }

    public function show($teamMembershipId)
    {
        $service = $this->buildViewService();
        $teamMembership = $service->showById($this->firmId(), $this->clientId(), $teamMembershipId);
        return $this->singleQueryResponse($this->arrayDataOfTeamMembership($teamMembership));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $teamMemberships = $service->showAll($this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($teamMemberships);
        foreach ($teamMemberships as $teamMembership) {
            $result["list"][] = $this->arrayDataOfTeamMembership($teamMembership);
        }
        
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfTeamMembership(Member $teamMembership): array
    {
        return [
            "id" => $teamMembership->getId(),
            "position" => $teamMembership->getPosition(),
            "anAdmin" => $teamMembership->isAnAdmin(),
            "active" => $teamMembership->isActive(),
            "joinTime" => $teamMembership->getJoinTimeString(),
            "team" => [
                "id" => $teamMembership->getTeam()->getId(),
                "name" => $teamMembership->getTeam()->getName(),
            ],
        ];
    }
    protected function buildViewService()
    {
        $teamMembershipRepository = $this->em->getRepository(Member::class);
        return new ViewTeamMembership($teamMembershipRepository);
    }
    protected function buildQuitService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        return new QuitTeamMembership($teamMembershipRepository);
    }

}
