<?php

namespace App\Http\Controllers\Manager;

use Firm\ {
    Application\Service\Firm\ProgramAdd,
    Application\Service\Firm\ProgramPublish,
    Application\Service\Firm\ProgramRemove,
    Application\Service\Firm\ProgramUpdate,
    Application\Service\Firm\ProgramView,
    Domain\Model\Firm,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\ProgramData
};

class ProgramController extends ManagerBaseController
{

    public function add()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildAddService();
        $program = $service->execute($this->firmId(), $this->getProgramData());
        
        return $this->commandCreatedResponse($this->arrayDataOfProgram($program));
    }

    public function update($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildUpdateService();
        $program = $service->execute($this->firmId(), $programId, $this->getProgramData());
        
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }

    public function publish($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildPublishService();
        $program = $service->execute($this->firmId(), $programId);
        
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }

    public function remove($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildRemoveService();
        $service->execute($this->firmId(), $programId);
        
        return $this->commandOkResponse();
    }

    public function show($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildViewService();
        $program = $service->showById($this->firmId(), $programId);
        
        return $this->singleQueryResponse($this->arrayDataOfProgram($program));
    }

    public function showAll()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildViewService();
        $programs = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($programs);
        foreach ($programs as $program) {
            $result['list'][] = [
                "id" => $program->getId(),
                "name" => $program->getName(),
                "published" => $program->isPublished(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function getProgramData()
    {
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        
        return new ProgramData($name, $description);
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

    protected function buildAddService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        return new ProgramAdd($programRepository, $firmRepository);
    }
    
    protected function buildUpdateService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ProgramUpdate($programRepository);
    }
    
    protected function buildPublishService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ProgramPublish($programRepository);
    }
    
    protected function buildRemoveService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ProgramRemove($programRepository);
    }

    protected function buildViewService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ProgramView($programRepository);
    }

}
