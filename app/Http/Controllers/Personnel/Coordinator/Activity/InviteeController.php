<?php

namespace App\Http\Controllers\Manager\Activity;

use App\Http\Controllers\Manager\ManagerBaseController;
use Query\ {
    Application\Service\Firm\Manager\Activity\ViewInvitation,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Manager\ManagerInvitation,
    Domain\Model\Firm\Program\Activity\Invitation,
    Domain\Model\Firm\Program\Consultant\ConsultantInvitation,
    Domain\Model\Firm\Program\Coordinator\CoordinatorInvitation,
    Domain\Model\Firm\Program\Participant\ParticipantInvitation,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class InviteeController extends ManagerBaseController
{
    public function show($inviteeId)
    {
        $service = $this->buildViewService();
        $invitation = $service->showById($this->firmId(), $this->managerId(), $inviteeId);
        return $this->singleQueryResponse($this->arrayDataOfInvitation($invitation));
    }
    public function showAll($activityId)
    {
        $service = $this->buildViewService();
        $invitations = $service->showAll(
                $this->firmId(), $this->managerId(), $activityId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($invitations);
        foreach ($invitations as $invitation) {
            $result["list"][] = $this->arrayDataOfInvitation($invitation);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfInvitation(Invitation $invitation): array
    {
        return [
            "id" => $invitation->getId(),
            "willAttend" => $invitation->willAttend(),
            "attended" => $invitation->isAttended(),
            "manager" => $this->arrayDataOfManager($invitation->getManagerInvitation()),
            "coordinator" => $this->arrayDataOfCoordinator($invitation->getCoordinatorInvitation()),
            "consultant" => $this->arrayDataOfConsultant($invitation->getConsultantInvitation()),
            "participant" => $this->arrayDataOfParticipant($invitation->getParticipantInvitation()),
        ];
    }
    protected function arrayDataOfManager(?ManagerInvitation $managerInvitation): ?array
    {
        return empty($managerInvitation)? null: [
            "id" => $managerInvitation->getManager()->getId(),
            "name" => $managerInvitation->getManager()->getName(),
        ];
    }
    protected function arrayDataOfCoordinator(?CoordinatorInvitation $coordinatorInvitation): ?array
    {
        return empty($coordinatorInvitation)? null: [
            "id" => $coordinatorInvitation->getCoordinator()->getId(),
            "personnel" => [
                "id" => $coordinatorInvitation->getCoordinator()->getPersonnel()->getId(),
                "name" => $coordinatorInvitation->getCoordinator()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function arrayDataOfConsultant(?ConsultantInvitation $consultantInvitation): ?array
    {
        return empty($consultantInvitation)? null: [
            "id" => $consultantInvitation->getConsultant()->getId(),
            "personnel" => [
                "id" => $consultantInvitation->getConsultant()->getPersonnel()->getId(),
                "name" => $consultantInvitation->getConsultant()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function arrayDataOfParticipant(?ParticipantInvitation $participantInvitation): ?array
    {
        return empty($participantInvitation)? null: [
            "id" => $participantInvitation->getParticipant()->getId(),
            "user" => $this->arrayDataOfUser($participantInvitation->getParticipant()->getUserParticipant()),
            "client" => $this->arrayDataOfClient($participantInvitation->getParticipant()->getClientParticipant()),
            "team" => $this->arrayDataOfTeam($participantInvitation->getParticipant()->getTeamParticipant()),
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant)? null: [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant)? null: [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant)? null: [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
        ];
    }
    
    protected function buildViewService()
    {
        $invitationRepository = $this->em->getRepository(Invitation::class);
        return new ViewInvitation($invitationRepository);
    }
}
