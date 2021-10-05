<?php

namespace App\Http\Controllers\Manager;

use Firm\Application\Service\Firm\ProgramAdd;
use Firm\Application\Service\Firm\ProgramPublish;
use Firm\Application\Service\Firm\ProgramRequest;
use Firm\Application\Service\Firm\ProgramUpdate;
use Firm\Application\Service\Manager\RemoveProgram;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\ProgramData;
use Query\Application\Service\Firm\ProgramView;
use Query\Domain\Model\Firm\Program as Program2;

class ProgramController extends ManagerBaseController
{
    
    protected function buildProgramRequest()
    {
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $strictMissionOrder = $this->filterBooleanOfInputRequest('strictMissionOrder');
        $firmFileInfoIdOfIllustration = $this->stripTagsInputRequest("firmFileInfoIdOfIllustration");
        $programRequest = new ProgramRequest($name, $description, $strictMissionOrder, $firmFileInfoIdOfIllustration);
        
        foreach ($this->request->input('participantTypes') as $participantType) {
            $programRequest->addParticipantType($participantType);
        }

        return $programRequest;
    }

    public function add()
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildAddService();
        $programId = $service->execute($this->firmId(), $this->buildProgramRequest());
        
        $viewService = $this->buildViewService();
        $program = $viewService->showById($this->firmId(), $programId);
        return $this->commandCreatedResponse($this->arrayDataOfProgram($program));
    }

    public function update($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $service = $this->buildUpdateService();
        $service->execute($this->firmId(), $programId, $this->buildProgramRequest());
        
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
        $this->buildRemoveService()->execute($this->firmId(), $this->managerId(), $programId);
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

    protected function arrayDataOfProgram(Program2 $program): array
    {
        $illustration = empty($program->getIllustration())? null : [
            "id" => $program->getIllustration()->getId(),
            "url" => $program->getIllustration()->getFullyQualifiedFileName(),
        ];
        return [
            "id" => $program->getId(),
            "name" => $program->getName(),
            "description" => $program->getDescription(),
            "participantTypes" => $program->getParticipantTypeValues(),
            "strictMissionOrder" => $program->isStrictMissionOrder(),
            "published" => $program->isPublished(),
            "illustration" => $illustration,
        ];
    }

    protected function buildAddService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $firmRepository = $this->em->getRepository(Firm::class);
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        return new ProgramAdd($programRepository, $firmRepository, $firmFileInfoRepository);
    }
    
    protected function buildUpdateService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        return new ProgramUpdate($programRepository, $firmFileInfoRepository);
    }
    
    protected function buildPublishService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        return new ProgramPublish($programRepository);
    }
    
    protected function buildRemoveService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        
        return new RemoveProgram($programRepository, $managerRepository);
    }

    protected function buildViewService()
    {
        $programRepository = $this->em->getRepository(Program2::class);
        return new ProgramView($programRepository);
    }

}
