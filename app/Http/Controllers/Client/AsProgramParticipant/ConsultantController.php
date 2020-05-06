<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\{
    Application\Service\Firm\Program\ConsultantView,
    Domain\Model\Firm\Program\Consultant
};

class ConsultantController extends AsProgramParticipantBaseController
{

    public function show($firmId, $programId, $consultantId)
    {
        $this->authorizedClientIsActiveProgramParticipant($firmId, $programId);
        $service = $this->buildQueryService();
        $programCompositionId = new ProgramCompositionId($firmId, $programId);
        $consultant = $service->showById($programCompositionId, $consultantId);
        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
    }

    public function showAll($firmId, $programId)
    {
        $this->authorizedClientIsActiveProgramParticipant($firmId, $programId);
        $service = $this->buildQueryService();
        $programCompositionId = new ProgramCompositionId($firmId, $programId);
        $consultants = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result['total'] = count($consultants);
        foreach ($consultants as $consultant) {
            $result['list'][] = $this->arrayDataOfConsultant($consultant);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfConsultant(Consultant $consultant): array
    {
        return [
            "id" => $consultant->getId(),
            "personnel" => [
                "id" => $consultant->getPersonnel()->getId(),
                "name" => $consultant->getPersonnel()->getName(),
            ],
        ];
    }

    protected function buildQueryService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        return new ConsultantView($consultantRepository);
    }

}
