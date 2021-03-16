<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Query\Application\Service\ViewProgram;
use Query\Domain\Model\Firm\Program;

class ProgramController extends Controller
{
    
    public function showAll()
    {
        $programs = $this->buildViewService()->showAll($this->getPage(), $this->getPageSize());
        $result = [];
        $result['total'] = count($programs);
        foreach ($programs as $program) {
            $result['list'][] = $this->arrayDataOfProgram($program);
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($id)
    {
        $program = $this->buildViewService()->showById($id);
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }
    
    protected function arrayDataOfProgram(Program $program): array
    {
        return [
            'id' => $program->getId(),
            'name' => $program->getName(),
            'description' => $program->getDescription(),
            "participantTypes" => $program->getParticipantTypeValues(),
            'firm' => [
                'id' => $program->getFirm()->getId(),
                'name' => $program->getFirm()->getName(),
            ],
        ];
    }
    protected function buildViewService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ViewProgram($programRepository);
    }
    
}
