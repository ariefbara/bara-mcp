<?php

namespace App\Http\Controllers\Client;

use Query\Application\Service\Firm\ProgramView;
use Query\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Model\Firm\ParticipantTypes;
use Query\Domain\Model\Firm\Program;
use Query\Domain\Task\Client\ViewAllAvailableProgramsTask;
use Query\Domain\Task\PaginationPayload;

class ProgramController extends ClientBaseController
{
    public function show($programId)
    {
        $service = $this->buildViewService();
        $program = $service->showById($this->firmId(), $programId);
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }
    public function showAll()
    {
        $service = $this->buildViewService();
        $programs = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($programs);
        foreach ($programs as $program) {
            $result["list"][] = $this->arrayDataOfProgram($program);
        }
        return $this->listQueryResponse($result);
    }
    public function showAllProgramForTeam()
    {
        $service = $this->buildViewService();
        $programs = $service->showAll(
                $this->firmId(), $this->getPage(), $this->getPageSize(), ParticipantTypes::TEAM_TYPE);
        
        $result = [];
        $result["total"] = count($programs);
        foreach ($programs as $program) {
            $result["list"][] = $this->arrayDataOfProgram($program);
        }
        return $this->listQueryResponse($result);
    }
    
    public function showAllAvailablePrograms()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $payload = new PaginationPayload($this->getPage(), $this->getPageSize());
        $task = new ViewAllAvailableProgramsTask($programRepository, $payload);
        $this->executeQueryTask($task);
        return $task->result;
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
            "illustration" => $this->arrayDataOfIllustration($program->getIllustration()),
        ];
    }
    protected function arrayDataOfIllustration(?FirmFileInfo $illustration): ?array
    {
        return empty($illustration) ? null: [
            "id" => $illustration->getId(),
            "url" => $illustration->getFullyQualifiedFileName(),
        ];
    }
    protected function buildViewService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ProgramView($programRepository);
    }
}
