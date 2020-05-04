<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\ {
    Application\Service\Firm\Program\ConsultantAssign,
    Application\Service\Firm\Program\ConsultantRemove,
    Application\Service\Firm\Program\ProgramCompositionId,
    Domain\Model\Firm\Personnel,
    Domain\Model\Firm\Program,
    Domain\Model\Firm\Program\Consultant
};
use Query\ {
    Application\Service\Firm\Program\ConsultantView,
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
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $consultant = $viewService->showById($programCompositionId, $consultantId);
        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
    }

    public function remove($programId, $consultantId)
    {
        $service = $this->buildRemoveService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $service->execute($programCompositionId, $consultantId);
        return $this->commandOkResponse();
    }

    public function show($programId, $consultantId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $consultant = $service->showById($programCompositionId, $consultantId);

        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);

        $consultants = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
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

    protected function buildRemoveService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        return new ConsultantRemove($consultantRepository);
    }

    protected function buildViewService()
    {
        $consultantRepository = $this->em->getRepository(Consultant2::class);
        return new ConsultantView($consultantRepository);
    }

}
