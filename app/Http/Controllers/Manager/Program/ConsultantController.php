<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\ConsultantAssign,
    Application\Service\Firm\Program\ProgramCompositionId,
    Application\Service\Manager\DisableConsultant,
    Domain\Model\Firm\Manager,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Consultant
};
use Query\ {
    Application\Service\Firm\Program\ViewConsultant,
    Domain\Model\Firm\Program\Consultant as Consultant2
};

class ConsultantController extends ManagerBaseController
{

    public function assign($programId)
    {
        $service = $this->buildAssignService();
        $personnelId = $this->stripTagsInputRequest('personnelId');
        $consultantId = $service->execute($this->firmId(), $programId, $personnelId);
        
        $viewService = $this->buildViewService();
        $consultant = $viewService->showById($this->firmId(), $programId, $consultantId);
        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
    }

    public function disable($programId, $consultantId)
    {
        $service = $this->buildDisableService();
        $service->execute($this->firmId(), $this->managerId(), $consultantId);
        return $this->commandOkResponse();
    }

    public function show($programId, $consultantId)
    {
        $service = $this->buildViewService();

        $consultant = $service->showById($this->firmId(), $programId, $consultantId);

        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $consultants = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        $result = [];
        $result['total'] = count($consultants);
        foreach ($consultants as $consultant) {
            $result['list'][] = $this->arrayDataOfConsultant($consultant);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultant(Consultant2 $consultant)
    {
        return [
            "id" => $consultant->getId(),
            "personnel" => [
                "id" => $consultant->getPersonnel()->getId(),
                "name" => $consultant->getPersonnel()->getName(),
            ],
        ];
    }

    protected function buildAssignService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $personnelRepository = $this->em->getRepository(Personnel::class);

        return new ConsultantAssign($programRepository, $personnelRepository);
    }

    protected function buildDisableService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        
        return new DisableConsultant($consultantRepository, $managerRepository);
    }

    protected function buildViewService()
    {
        $consultantRepository = $this->em->getRepository(Consultant2::class);
        return new ViewConsultant($consultantRepository);
    }

}
