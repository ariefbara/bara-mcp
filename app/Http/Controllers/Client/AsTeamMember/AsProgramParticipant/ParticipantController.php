<?php

namespace App\Http\Controllers\Client\AsTeamMember\AsProgramParticipant;

use Query\ {
    Application\Service\Firm\Program\ViewParticipant,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class ParticipantController extends AsProgramParticipantBaseController
{

    public function show($teamId, $programId, $participantId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $this->authorizedTeamIsActiveParticipantOfProgram($teamId, $programId);

        $service = $this->buildViewService();
        $participant = $service->showById($this->firmId(), $programId, $participantId);

        return $this->singleQueryResponse($this->arrayDataOfParticipant($participant));
    }

    public function showAll($teamId, $programId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $this->authorizedTeamIsActiveParticipantOfProgram($teamId, $programId);

        $service = $this->buildViewService();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");
        $note = $this->stripTagQueryRequest("note");
        $participants = $service->showAll(
                $this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $activeStatus, $note);

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
            "team" => $this->arrayDataOfTeam($participant->getTeamParticipant()),
        ];
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }

    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
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
