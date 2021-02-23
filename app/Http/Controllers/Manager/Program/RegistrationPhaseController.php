<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use DateTimeImmutable;
use Firm\ {
    Application\Service\Firm\Program\ProgramCompositionId,
    Application\Service\Firm\Program\RegistrationPhaseAdd,
    Application\Service\Firm\Program\RegistrationPhaseRemove,
    Application\Service\Firm\Program\RegistrationPhaseUpdate,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\RegistrationPhase,
    Domain\Model\Firm\Program\RegistrationPhaseData
};
use Query\ {
    Application\Service\Firm\Program\RegistrationPhaseView,
    Domain\Model\Firm\Program\RegistrationPhase as RegistrationPhase2
};

class RegistrationPhaseController extends ManagerBaseController
{

    public function add($programId)
    {
        $service = $this->buildAddService();
        $registrationPhaseId = $service->execute($this->firmId(), $programId, $this->getRegistrationPhaseData());
        
        $viewService = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $registrationPhase = $viewService->showById($programCompositionId, $registrationPhaseId);
        return $this->commandCreatedResponse($this->arrayDataOfRegistrationPhase($registrationPhase));
    }

    public function update($programId, $registrationPhaseId)
    {
        $service = $this->buildUpdateService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $service->execute($programCompositionId, $registrationPhaseId, $this->getRegistrationPhaseData());
        
        return $this->show($programId, $registrationPhaseId);
    }

    public function remove($programId, $registrationPhaseId)
    {
        $service = $this->buildRemoveService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $service->execute($programCompositionId, $registrationPhaseId);
        
        return $this->commandOkResponse();
    }

    public function show($programId, $registrationPhaseId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $registrationPhase = $service->showById($programCompositionId, $registrationPhaseId);
        
        return $this->singleQueryResponse($this->arrayDataOfRegistrationPhase($registrationPhase));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $registrationPhases = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($registrationPhases);
        foreach ($registrationPhases as $registrationPhase) {
            $result['list'][] = $this->arrayDataOfRegistrationPhase($registrationPhase);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function getRegistrationPhaseData()
    {
        $name = $this->stripTagsInputRequest('name');
        $startDate = ($this->stripTagsInputRequest('startDate') !== null)?
                new DateTimeImmutable($this->stripTagsInputRequest('startDate')): null;
        $endDate = ($this->stripTagsInputRequest('endDate') !== null)?
                new DateTimeImmutable($this->stripTagsInputRequest('endDate')): null;
        return new RegistrationPhaseData($name, $startDate, $endDate);
    }
    
    protected function arrayDataOfRegistrationPhase(RegistrationPhase2 $registrationPhase): array
    {
        return [
            "id" => $registrationPhase->getId(),
            "name" => $registrationPhase->getName(),
            "startDate" => $registrationPhase->getStartDate(),
            "endDate" => $registrationPhase->getEndDate(),
        ];
    }
    
    protected function buildAddService()
    {
        $registrationPhaseRepository = $this->em->getRepository(RegistrationPhase::class);
        $programRepository = $this->em->getRepository(Program::class);
        return new RegistrationPhaseAdd($registrationPhaseRepository, $programRepository);
    }
    protected function buildUpdateService()
    {
        $registrationPhaseRepository = $this->em->getRepository(RegistrationPhase::class);
        return new RegistrationPhaseUpdate($registrationPhaseRepository);
    }
    protected function buildRemoveService()
    {
        $registrationPhaseRepository = $this->em->getRepository(RegistrationPhase::class);
        return new RegistrationPhaseRemove($registrationPhaseRepository);
    }
    protected function buildViewService()
    {
        $registrationPhaseRepository = $this->em->getRepository(RegistrationPhase2::class);
        return new RegistrationPhaseView($registrationPhaseRepository);
    }

}
