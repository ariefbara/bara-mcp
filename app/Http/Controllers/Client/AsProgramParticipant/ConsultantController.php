<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use Query\Application\Service\Client\AsProgramParticipant\ViewConsultant;
use Query\Domain\Model\Firm\Program\Consultant;

class ConsultantController extends AsProgramParticipantBaseController
{
    public function showAll($programId)
    {
        $result = $this->buildViewService()
                ->showAll($this->firmId(), $this->clientId(), $programId, $this->getPage(), $this->getPageSize());
        return $this->listQueryResponse($result);
    }
    
    public function show($programId, $consultantId)
    {
        $consultant = $this->buildViewService()
                ->showById($this->firmId(), $this->clientId(), $programId, $consultantId);
        return $this->singleQueryResponse($this->arrayDataOfConsultant($consultant));
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
    protected function buildViewService()
    {
        $mentorRepository = $this->em->getRepository(Consultant::class);
        return new ViewConsultant($this->clientParticipantQueryRepository(), $mentorRepository);
    }
    
/*
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
        
    }

    protected function buildQueryService()
    {
        $consultantRepository = $this->em->getRepository(Consultant::class);
        return new ConsultantView($consultantRepository);
    }
 * 
 */

}
