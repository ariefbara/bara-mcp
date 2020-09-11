<?php

namespace App\Http\Controllers\Client;

use Participant\ {
    Application\Service\ClientQuitParticipation,
    Domain\Model\ClientParticipant as ClientParticipant2
};
use Query\ {
    Application\Service\Firm\Client\ViewProgramParticipation,
    Domain\Model\Firm\Client\ClientParticipant
};

class ProgramParticipationController extends ClientBaseController
{
    public function quit($programParticipationId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId);
        return $this->commandOkResponse();
    }

    public function show($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipation = $service->showById($this->firmId(), $this->clientId(), $programParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramParticipation($programParticipation));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $programParticipations = $service->showAll($this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($programParticipations);
        foreach ($programParticipations as $programParticipation) {
            $result['list'][] = $this->arrayDataOfProgramParticipation($programParticipation);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfProgramParticipation(ClientParticipant $programParticipation): array
    {
        return [
            "id" => $programParticipation->getId(),
            "program" => [
                "id" => $programParticipation->getProgram()->getId(),
                "name" => $programParticipation->getProgram()->getName(),
                "removed" => $programParticipation->getProgram()->isRemoved(),
            ],
            "enrolledTime" => $programParticipation->getEnrolledTimeString(),
            "active" => $programParticipation->isActive(),
            "note" => $programParticipation->getNote(),
        ];
    }

    protected function buildQuitService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        return new ClientQuitParticipation($clientParticipantRepository);
    }

    protected function buildViewService()
    {
        $programParticipationRepository = $this->em->getRepository(ClientParticipant::class);
        return new ViewProgramParticipation($programParticipationRepository);
    }

}
