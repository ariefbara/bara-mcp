<?php

namespace App\Http\Controllers\Personnel\Coordinator;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\ {
    Application\Service\Firm\Personnel\ProgramCoordinator\ViewInvitationForCoordinator,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Manager\ManagerActivity,
    Domain\Model\Firm\Program\Consultant\ConsultantActivity,
    Domain\Model\Firm\Program\Coordinator\CoordinatorActivity,
    Domain\Model\Firm\Program\Coordinator\CoordinatorInvitation,
    Domain\Model\Firm\Program\Participant\ParticipantActivity,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class InvitationController extends PersonnelBaseController
{

    public function show($coordinatorId, $invitationId)
    {
        $service = $this->buildViewService();
        $invitation = $service->showById($this->firmId(), $this->personnelId(), $invitationId);

        return $this->singleQueryResponse($this->arrayDataOfInvitation($invitation));
    }

    public function showAll($coordinatorId)
    {
        $service = $this->buildViewService();
        $invitations = $service->showAll(
                $this->firmId(), $this->personnelId(), $coordinatorId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($invitations);
        foreach ($invitations as $invitation) {
            $result["list"][] = $this->arrayDataOfInvitation($invitation);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfInvitation(CoordinatorInvitation $coordinatorInvitation): array
    {
        return [
            "id" => $coordinatorInvitation->getId(),
            "willAttend" => $coordinatorInvitation->willAttend(),
            "attended" => $coordinatorInvitation->isAttended(),
            "activity" => [
                "id" => $coordinatorInvitation->getActivity()->getId(),
                "name" => $coordinatorInvitation->getActivity()->getName(),
                "description" => $coordinatorInvitation->getActivity()->getDescription(),
                "location" => $coordinatorInvitation->getActivity()->getLocation(),
                "note" => $coordinatorInvitation->getActivity()->getNote(),
                "startTime" => $coordinatorInvitation->getActivity()->getStartTimeString(),
                "endTime" => $coordinatorInvitation->getActivity()->getEndTimeString(),
                "cancelled" => $coordinatorInvitation->getActivity()->isCancelled(),
                "program" => [
                    "id" => $coordinatorInvitation->getActivity()->getProgram()->getId(),
                    "name" => $coordinatorInvitation->getActivity()->getProgram()->getName(),
                ],
                "activityType" => [
                    "id" => $coordinatorInvitation->getActivity()->getActivityType()->getId(),
                    "name" => $coordinatorInvitation->getActivity()->getActivityType()->getName(),
                ],
                "manager" => $this->arrayDataOfManager($coordinatorInvitation->getActivity()->getManagerActivity()),
                "coordinator" => $this->arrayDataOfCoordinator($coordinatorInvitation->getActivity()->getCoordinatorActivity()),
                "consultant" => $this->arrayDataOfConsultant($coordinatorInvitation->getActivity()->getConsultantActivity()),
                "participant" => $this->arrayDataOfParticipant($coordinatorInvitation->getActivity()->getParticipantActivity()),
            ],
        ];
    }

    protected function arrayDataOfManager(?ManagerActivity $managerActivity): ?array
    {
        return empty($managerActivity) ? null : [
            "id" => $managerActivity->getManager()->getId(),
            "name" => $managerActivity->getManager()->getName(),
        ];
    }

    protected function arrayDataOfCoordinator(?CoordinatorActivity $coordinatorActivity): ?array
    {
        return empty($coordinatorActivity) ? null : [
            "id" => $coordinatorActivity->getCoordinator()->getId(),
            "personnel" => [
                "id" => $coordinatorActivity->getCoordinator()->getPersonnel()->getId(),
                "name" => $coordinatorActivity->getCoordinator()->getPersonnel()->getName(),
            ],
        ];
    }

    protected function arrayDataOfConsultant(?ConsultantActivity $consultantActivity): ?array
    {
        return empty($consultantActivity) ? null : [
            "id" => $consultantActivity->getConsultant()->getId(),
            "personnel" => [
                "id" => $consultantActivity->getConsultant()->getPersonnel()->getId(),
                "name" => $consultantActivity->getConsultant()->getPersonnel()->getName(),
            ],
        ];
    }

    protected function arrayDataOfParticipant(?ParticipantActivity $participantActivity): ?array
    {
        return empty($participantActivity) ? null : [
            "id" => $participantActivity->getParticipant()->getId(),
            "user" => $this->arrayDataOfUser($participantActivity->getParticipant()->getUserParticipant()),
            "client" => $this->arrayDataOfClient($participantActivity->getParticipant()->getClientParticipant()),
            "team" => $this->arrayDataOfTeam($participantActivity->getParticipant()->getTeamParticipant()),
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
        $coordinatorInvitationRepository = $this->em->getRepository(CoordinatorInvitation::class);
        return new ViewInvitationForCoordinator($coordinatorInvitationRepository);
    }

}
