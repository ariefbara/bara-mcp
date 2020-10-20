<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Participant\ {
    Application\Service\Firm\Client\TeamMembership\QuitProgramParticipation,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\TeamProgramParticipation as TeamProgramParticipation2
};
use Query\ {
    Application\Service\Firm\Team\ViewTeamProgramParticipation,
    Domain\Model\Firm\Team\TeamProgramParticipation
};

class ProgramParticipationController extends AsTeamMemberBaseController
{

    public function quit($teamId, $teamProgramParticipationId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId);
        
        return $this->commandOkResponse();
    }

    public function show($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $teamProgramParticipation = $service->showById($teamId, $teamProgramParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfTeamProgramParticipation($teamProgramParticipation));
    }

    public function showAll($teamId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $teamProgramParticipations = $service->showAll($teamId, $this->getPage(), $this->getPageSize());
        
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
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        return new ViewTeamProgramParticipation($teamProgramParticipationRepository);
    }
    protected function buildQuitService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        return new QuitProgramParticipation($teamMembershipRepository, $teamProgramParticipationRepository);
    }

}
