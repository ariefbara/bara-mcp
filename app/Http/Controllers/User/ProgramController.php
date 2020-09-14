<?php

namespace App\Http\Controllers\User;

use Query\ {
    Application\Service\User\ViewProgram,
    Domain\Model\Firm\Program
};

class ProgramController extends UserBaseController
{
    public function show($firmId, $programId)
    {
        $service = $this->buildViewService();
        $program = $service->showById($firmId, $programId);
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }
    
    public function showAll()
    {
        $service = $this->buildViewService();
        $programs = $service->showAll($this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($programs);
        foreach ($programs as $program) {
            $result["list"][] = [
                "id" => $program->getId(),
                "name" => $program->getName(),
                "published" => $program->isPublished(),
                "removed" => $program->isRemoved(),
                "firm" => [
                    "id" => $program->getFirm()->getId(),
                    "name" => $program->getFirm()->getName(),
                ],
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
            "participantTypes" => $program->getParticipantTypeValues(),
            "published" => $program->isPublished(),
            "removed" => $program->isRemoved(),
            "firm" => [
                "id" => $program->getFirm()->getId(),
                "name" => $program->getFirm()->getName(),
            ],
        ];
    }
    protected function buildViewService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ViewProgram($programRepository);
    }
}
