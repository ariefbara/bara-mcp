<?php

namespace App\Http\Controllers\Guest\Firm;

use App\Http\Controllers\Controller;
use Query\ {
    Application\Service\Firm\ProgramView,
    Domain\Model\Firm\Program
};

class ProgramController extends Controller
{
    public function show($firmId, $programId)
    {
        $service = $this->buildViewService();
        $program = $service->showById($firmId, $programId);
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }
    public function showAll($firmId)
    {
        $service = $this->buildViewService();
        $programs = $service->showAll($firmId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($programs);
        foreach ($programs as $program) {
            $result['list'][] = $this->arrayDataOfProgram($program);
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
        ];
    }
    
    protected function buildViewService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ProgramView($programRepository);
    }
}
