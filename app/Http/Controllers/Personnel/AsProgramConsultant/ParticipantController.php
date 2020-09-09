<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant;

use Query\ {
    Application\Service\Firm\Program\ViewParticipant,
    Domain\Model\Firm\Program\Participant
};

class ParticipantController extends AsProgramConsultantBaseController
{

    public function show($programId, $participantId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $participant = $service->showById($this->firmId(), $programId, $participantId);
        
        return $this->singleQueryResponse($this->arrayDataOfParticipant($participant));
    }

    public function showAll($programId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $participants = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
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
            "enrolledTime" => $participant->getEnrolledTimeString(),
            "active" => $participant->isActive(),
            "note" => $participant->getNote(),
            "user" => $this->arrayDataOfUser($participant->getUserParticipant()),
            "client" => $this->arrayDataOfClient($participant->getClientParticipant()),
        ];
    }
    protected function arrayDataOfUser(?\Query\Domain\Model\User\UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant)? null:[
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }
    protected function arrayDataOfClient(?\Query\Domain\Model\Firm\Client\ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant)? null: [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }

    protected function buildViewService()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        return new ViewParticipant($participantRepository);
    }

}
