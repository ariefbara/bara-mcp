<?php

namespace App\Http\Controllers\Manager;

use Query\ {
    Application\Service\Firm\Manager\ViewInvitationForManager,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Manager\ManagerActivity,
    Domain\Model\Firm\Manager\ManagerInvitation,
    Domain\Model\Firm\Program\Consultant\ConsultantActivity,
    Domain\Model\Firm\Program\Coordinator\CoordinatorActivity,
    Domain\Model\Firm\Program\Participant\ParticipantActivity,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class InvitationController extends ManagerBaseController
{
    public function show($invitationId)
    {
        $service = $this->buildViewService();
        $managerInvitation = $service->showById($this->firmId(), $this->managerId(), $invitationId);
        
        return $this->singleQueryResponse($this->arrayDataOfManagerInvitation($managerInvitation));
    }
    public function showAll()
    {
        $service = $this->buildViewService();
        $managerInvitations = $service->showAll($this->firmId(), $this->managerId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($managerInvitations);
        foreach ($managerInvitations as $managerInvitation) {
            $result["list"][] = $this->arrayDataOfManagerInvitation($managerInvitation);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfManagerInvitation(ManagerInvitation $managerInvitation): array
    {
        return [
            "id" => $managerInvitation->getId(),
            "willAttend" => $managerInvitation->willAttend(),
            "attended" => $managerInvitation->isAttended(),
            "activity" => [
                "id" => $managerInvitation->getActivity()->getId(),
                "name" => $managerInvitation->getActivity()->getName(),
                "description" => $managerInvitation->getActivity()->getDescription(),
                "location" => $managerInvitation->getActivity()->getLocation(),
                "note" => $managerInvitation->getActivity()->getNote(),
                "startTime" => $managerInvitation->getActivity()->getStartTimeString(),
                "endTime" => $managerInvitation->getActivity()->getEndTimeString(),
                "cancelled" => $managerInvitation->getActivity()->isCancelled(),
                "program" => [
                    "id" => $managerInvitation->getActivity()->getProgram()->getId(),
                    "name" => $managerInvitation->getActivity()->getProgram()->getName(),
                ],
                "activityType" => [
                    "id" => $managerInvitation->getActivity()->getActivityType()->getId(),
                    "name" => $managerInvitation->getActivity()->getActivityType()->getName(),
                ],
                "manager" => $this->arrayDataOfManager($managerInvitation->getActivity()->getManagerActivity()),
                "coordinator" => $this->arrayDataOfCoordinator($managerInvitation->getActivity()->getCoordinatorActivity()),
                "consultant" => $this->arrayDataOfConsultant($managerInvitation->getActivity()->getConsultantActivity()),
                "participant" => $this->arrayDataOfParticipant($managerInvitation->getActivity()->getParticipantActivity()),
            ],
        ];
    }
    protected function arrayDataOfManager(?ManagerActivity $managerActivity): ?array
    {
        return empty($managerActivity)? null: [
            "id" => $managerActivity->getManager()->getId(),
            "name" => $managerActivity->getManager()->getName(),
        ];
    }
    protected function arrayDataOfCoordinator(?CoordinatorActivity $coordinatorActivity): ?array
    {
        return empty($coordinatorActivity)? null: [
            "id" => $coordinatorActivity->getCoordinator()->getId(),
            "personnel" => [
                "id" => $coordinatorActivity->getCoordinator()->getPersonnel()->getId(),
                "name" => $coordinatorActivity->getCoordinator()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function arrayDataOfConsultant(?ConsultantActivity $consultantActivity): ?array
    {
        return empty($consultantActivity)? null: [
            "id" => $consultantActivity->getConsultant()->getId(),
            "personnel" => [
                "id" => $consultantActivity->getConsultant()->getPersonnel()->getId(),
                "name" => $consultantActivity->getConsultant()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function arrayDataOfParticipant(?ParticipantActivity $participantActivity): ?array
    {
        return empty($participantActivity)? null: [
            "id" => $participantActivity->getParticipant()->getId(),
            "user" => $this->arrayDataOfUser($participantActivity->getParticipant()->getUserParticipant()),
            "client" => $this->arrayDataOfClient($participantActivity->getParticipant()->getClientParticipant()),
            "team" => $this->arrayDataOfTeam($participantActivity->getParticipant()->getTeamParticipant()),
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
        $managerInvitationRepository = $this->em->getRepository(ManagerInvitation::class);
        return new ViewInvitationForManager($managerInvitationRepository);
    }
    
    
}
