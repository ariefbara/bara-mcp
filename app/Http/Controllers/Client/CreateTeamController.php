<?php

namespace App\Http\Controllers\Client;

use Query\ {
    Application\Service\Firm\ViewTeam,
    Domain\Model\Firm\Team
};
use Team\ {
    Application\Service\CreateTeam,
    Domain\DependencyModel\Firm\Client,
    Domain\Model\Team as Team2
};

class CreateTeamController extends ClientBaseController
{
    public function create()
    {
        $service = $this->buildCreateService();
        $teamName = $this->stripTagsInputRequest("name");
        $memberPosition = $this->stripTagsInputRequest("memberPosition");
        $teamId = $service->execute($this->firmId(), $this->clientId(), $teamName, $memberPosition);
        
        $viewService = $this->buildViewService();
        $team = $viewService->showById($this->firmId(), $teamId);
        return $this->commandCreatedResponse($this->arrayDataOfTeam($team));
    }
    
    protected function arrayDataOfTeam(Team $team): array
    {
        return [
            "id" => $team->getId(),
            "name" => $team->getName(),
            "createdTime" => $team->getCreatedTimeString(),
            "creator" => [
                "id" => $team->getCreator()->getId(),
                "name" => $team->getCreator()->getFullName(),
            ],
        ];
    }
    protected function buildViewService()
    {
        $teamRepository = $this->em->getRepository(Team::class);
        return new ViewTeam($teamRepository);
    }
    
    protected function buildCreateService()
    {
        $teamRepository = $this->em->getRepository(Team2::class);
        $clientRepository = $this->em->getRepository(Client::class);
        
        return new CreateTeam($teamRepository, $clientRepository);
    }
}
