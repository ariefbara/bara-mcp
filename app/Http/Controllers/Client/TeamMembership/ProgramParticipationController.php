<?php

namespace App\Http\Controllers\Client\TeamMembership;

use Participant\ {
    Application\Service\Firm\Client\TeamMembership\QuitProgramParticipation,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\TeamProgramParticipation as TeamProgramParticipation2
};
use Query\ {
    Application\Service\Firm\Client\TeamMembership\ViewTeamProgramParticipation,
    Domain\Model\Firm\Team\TeamProgramParticipation
};

class ProgramParticipationController extends TeamProgramParticipationBaseController
{

    public function quit($teamMembershipId, $teamProgramParticipationId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId);
        
        return $this->commandOkResponse();
    }

    public function show($teamMembershipId, $teamProgramParticipationId)
    {
        $service = $this->buildViewService();
        $teamProgramParticipation = $service->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfTeamProgramParticipation($teamProgramParticipation));
    }

    public function showAll($teamMembershipId)
    {
        $service = $this->buildViewService();
        $teamProgramParticipations = $service->showAll(
                $this->firmId(), $this->clientId(), $teamMembershipId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($teamProgramParticipations);
        foreach ($teamProgramParticipations as $teamProgramParticipation) {
            $result["list"][] = $this->arrayDataOfTeamProgramParticipation($teamProgramParticipation);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfTeamProgramParticipation(TeamProgramParticipation $teamProgramParticipation): array
    {
        return [
            "id" => $teamProgramParticipation->getId(),
            "enrolledTime" => $teamProgramParticipation->getEnrolledTimeString(),
            "note" => $teamProgramParticipation->getNote(),
            "active" => $teamProgramParticipation->isActive(),
            "program" => [
                "id" => $teamProgramParticipation->getProgram()->getId(),
                "name" => $teamProgramParticipation->getProgram()->getName(),
                "removed" => $teamProgramParticipation->getProgram()->isRemoved(),
            ],
        ];
    }
    protected function buildViewService()
    {
        return new ViewTeamProgramParticipation($this->teamMembershipRepository(), $this->teamProgramParticipationFinder());
    }
    protected function buildQuitService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        return new QuitProgramParticipation($teamMembershipRepository, $teamProgramParticipationRepository);
    }

}
