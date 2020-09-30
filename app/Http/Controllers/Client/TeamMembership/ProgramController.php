<?php

namespace App\Http\Controllers\Client\TeamMembership;

use Query\{
    Application\Service\Firm\Client\TeamMembership\ViewAvailableProgram,
    Domain\Model\Firm\Program,
    Domain\Service\Firm\ProgramFinder
};

class ProgramController extends TeamMembershipBaseController
{

    public function show($teamMembershipId, $programId)
    {
        $service = $this->buildViewService();
        $program = $service->showById($this->firmId(), $this->clientId(), $teamMembershipId, $programId);
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }

    public function showAll($teamMembershipId)
    {
        $service = $this->buildViewService();
        $programs = $service->showAll(
                $this->firmId(), $this->clientId(), $teamMembershipId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($programs);
        foreach ($programs as $program) {
            $result["list"][] = [
                "id" => $program->getId(),
                "name" => $program->getName(),
                "published" => $program->isPublished(),
                "removed" => $program->isRemoved(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfProgram(Program $program): array
    {
        return [
            "id" => $program->getId(),
            "name" => $program->getName(),
            "description" => $program->getDescription(),
            "published" => $program->isPublished(),
            "participantTypes" => $program->getParticipantTypeValues(),
            "removed" => $program->isRemoved(),
        ];
    }

    protected function buildViewService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $programFinder = new ProgramFinder($programRepository);
        return new ViewAvailableProgram($this->teamMembershipRepository(), $programFinder);
    }

}
