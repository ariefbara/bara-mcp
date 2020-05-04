<?php

namespace App\Http\Controllers\Client;

use Client\ {
    Application\Service\Client\ProgramParticipationQuit,
    Domain\Model\Client\ProgramParticipation
};
use Query\ {
    Application\Service\Client\ProgramParticipationView,
    Domain\Model\Firm\Program\Participant
};

class ProgramParticipationController extends ClientBaseController
{

    public function quit($programParticipationId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->clientId(), $programParticipationId);
        return $this->commandOkResponse();
    }

    public function show($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipation = $service->showById($this->clientId(), $programParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfParticipant($programParticipation));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $programParticipations = $service->showAll($this->clientId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($programParticipations);
        foreach ($programParticipations as $programParticipation) {
            $result['list'][] = [
                "id" => $programParticipation->getId(),
                "note" => $programParticipation->getNote(),
                "active" => $programParticipation->isActive(),
                "program" => [
                    "id" => $programParticipation->getProgram()->getId(),
                    "name" => $programParticipation->getProgram()->getName(),
                    "removed" => $programParticipation->getProgram()->isRemoved(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            "id" => $participant->getId(),
            "program" => [
                "id" => $participant->getProgram()->getId(),
                "name" => $participant->getProgram()->getName(),
                "removed" => $participant->getProgram()->isRemoved(),
                "firm" => [
                    "id" => $participant->getProgram()->getFirm()->getId(),
                    "name" => $participant->getProgram()->getFirm()->getName(),
                ],
            ],
            "acceptedTime" => $participant->getAcceptedTimeString(),
            "active" => $participant->isActive(),
            "note" => $participant->getNote(),
        ];
    }
    
    protected function buildQuitService()
    {
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        return new ProgramParticipationQuit($programParticipationRepository);
    }
    protected function buildViewService()
    {
        $programParticipationRepository = $this->em->getRepository(Participant::class);
        return new ProgramParticipationView($programParticipationRepository);
    }

}
