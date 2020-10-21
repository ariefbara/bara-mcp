<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Query\{
    Application\Service\Firm\Program\ViewParticipant,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class ParticipantController extends AsProgramCoordinatorBaseController
{

    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");

        $participants = $service->showAll(
                $this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $activeStatus);
        
        $result = [];
        $result["total"] = count($participants);
        foreach ($participants as $participant) {
            $result["list"][] = $this->arrayDataOfParticipant($participant);
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $participantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $participant = $service->showById($this->firmId(), $programId, $participantId);
        return $this->singleQueryResponse($this->arrayDataOfParticipant($participant));
    }

    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            "id" => $participant->getId(),
            "enrolledTime" => $participant->getEnrolledTimeString(),
            "active" => $participant->isActive(),
            "note" => $participant->getNote(),
            "client" => $this->arrayDataOfClient($participant->getClientParticipant()),
            "user" => $this->arrayDataOfUser($participant->getUserParticipant()),
            "team" => $this->arrayDataOfUser($participant->getTeamParticipant()),
        ];
    }

    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }

    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }

    protected function buildViewService()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        return new ViewParticipant($participantRepository);
    }

}
