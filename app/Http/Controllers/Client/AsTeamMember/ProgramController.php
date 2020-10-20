<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Query\{
    Application\Service\Firm\ProgramView,
    Domain\Model\Firm\ParticipantTypes,
    Domain\Model\Firm\Program
};

class ProgramController extends AsTeamMemberBaseController
{

    public function show($teamId, $programId)
    {

        $this->authorizeClientIsActiveTeamMember($teamId);

        $service = $this->buildViewService();
        $program = $service->showById($this->firmId(), $programId);
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }

    public function showAll($teamId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $programs = $service->showAll(
                $this->firmId(), $this->getPage(), $this->getPageSize(), ParticipantTypes::TEAM_TYPE);

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
        return new ProgramView($programRepository);
    }

}
