<?php

namespace App\Http\Controllers\Manager;

use Firm\ {
    Application\Service\Firm\ProgramAdd,
    Application\Service\Firm\ProgramPublish,
    Application\Service\Firm\ProgramRemove,
    Application\Service\Firm\ProgramUpdate,
    Domain\Model\Firm,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\ProgramData
};
use Query\ {
    Application\Service\Firm\ProgramView,
    Domain\Model\Firm\Program as Program2
};

class ProgramController extends ManagerBaseController
{

    public function add()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildAddService();
        $programId = $service->execute($this->firmId(), $this->getProgramData());
        
        $viewService = $this->buildViewService();
        $program = $viewService->showById($this->firmId(), $programId);
        return $this->commandCreatedResponse($this->arrayDataOfProgram($program));
    }

    public function update($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildUpdateService();
        $service->execute($this->firmId(), $programId, $this->getProgramData());
        
        return $this->show($programId);
    }

    public function publish($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildPublishService();
        $service->execute($this->firmId(), $programId);
        
        return $this->show($programId);
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
        $programs = $service->showAll($this->firmId(), $this->getPage(), $this->getPageSize(), null, false);
        
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
        $programData = new ProgramData($name, $description);
        
        foreach ($this->request->input('participantTypes') as $participantType) {
            $programData->addParticipantType($this->stripTagsVariable($participantType));
        }
        return $programData;
    }

    protected function arrayDataOfProgram(Program2 $program): array
    {
        return [
            "id" => $program->getId(),
            "name" => $program->getName(),
            "description" => $program->getDescription(),
            "participantTypes" => $program->getParticipantTypeValues(),
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
        $programRepository = $this->em->getRepository(Program2::class);
        return new ProgramView($programRepository);
    }

}
