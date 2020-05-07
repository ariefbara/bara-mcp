<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant;

use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\ {
    Application\Service\Firm\Program\ParticipantView,
    Domain\Model\Firm\Program\Participant
};

class ParticipantController extends AsProgramConsultantBaseController
{

    public function show($programId, $participantId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $participant = $service->showById($programCompositionId, $participantId);
        
        return $this->singleQueryResponse($this->arrayDataOfParticipant($participant));
    }

    public function showAll($programId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($this->firmId(), $programId);
        $participants = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($participants);
        foreach ($participants as $participant) {
            $result['list'][] = $this->arrayDataOfParticipant($participant);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            "id" => $participant->getId(),
            "acceptedTime" => $participant->getAcceptedTimeString(),
            "active" => $participant->isActive(),
            "note" => $participant->getNote(),
            "client" => [
                "id" => $participant->getClient()->getId(),
                "name" => $participant->getClient()->getName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        return new ParticipantView($participantRepository);
    }

}
