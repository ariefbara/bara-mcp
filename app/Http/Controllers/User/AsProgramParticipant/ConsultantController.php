<?php

namespace App\Http\Controllers\User\AsProgramParticipant;

use Query\Application\Service\User\AsProgramParticipant\ViewMentor;
use Query\Domain\Model\Firm\Program\Consultant;

class ConsultantController extends AsProgramParticipantBaseController
{
    public function showAll($firmId, $programId)
    {
        $result = $this->buildViewService()->showAll($this->userId(), $programId, $this->getPage(), $this->getPageSize());
        return $this->listQueryResponse($result);
        
    }
    public function show($firmId, $programId, $consultantId)
    {
        $consultant = $this->buildViewService()
                ->showById($this->userId(), $programId, $consultantId);
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
        return new ViewMentor($this->userParticipantQueryRepository(), $mentorRepository);
    }
    
}
