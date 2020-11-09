<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ViewInvitationForConsultant,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Manager\ManagerActivity,
    Domain\Model\Firm\Program\Consultant\ConsultantActivity,
    Domain\Model\Firm\Program\Consultant\ConsultantInvitation,
    Domain\Model\Firm\Program\Coordinator\CoordinatorActivity,
    Domain\Model\Firm\Program\Participant\ParticipantActivity,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class InvitationController extends PersonnelBaseController
{

    public function show($programConsultationId, $invitationId)
    {
        $service = $this->buildViewService();
        $invitation = $service->showById($this->firmId(), $this->personnelId(), $invitationId);

        return $this->singleQueryResponse($this->arrayDataOfInvitation($invitation));
    }

    public function showAll($programConsultationId)
    {
        $service = $this->buildViewService();
        $invitations = $service->showAll(
                $this->firmId(), $this->personnelId(), $programConsultationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($invitations);
        foreach ($invitations as $invitation) {
            $result["list"][] = $this->arrayDataOfInvitation($invitation);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfInvitation(ConsultantInvitation $consultantInvitation): array
    {
        return [
            "id" => $consultantInvitation->getId(),
            "willAttend" => $consultantInvitation->willAttend(),
            "attended" => $consultantInvitation->isAttended(),
            "activity" => [
                "id" => $consultantInvitation->getActivity()->getId(),
                "name" => $consultantInvitation->getActivity()->getName(),
                "description" => $consultantInvitation->getActivity()->getDescription(),
                "location" => $consultantInvitation->getActivity()->getLocation(),
                "note" => $consultantInvitation->getActivity()->getNote(),
                "startTime" => $consultantInvitation->getActivity()->getStartTimeString(),
                "endTime" => $consultantInvitation->getActivity()->getEndTimeString(),
                "cancelled" => $consultantInvitation->getActivity()->isCancelled(),
                "program" => [
                    "id" => $consultantInvitation->getActivity()->getProgram()->getId(),
                    "name" => $consultantInvitation->getActivity()->getProgram()->getName(),
                ],
                "activityType" => [
                    "id" => $consultantInvitation->getActivity()->getActivityType()->getId(),
                    "name" => $consultantInvitation->getActivity()->getActivityType()->getName(),
                ],
                "manager" => $this->arrayDataOfManager($consultantInvitation->getActivity()->getManagerActivity()),
                "coordinator" => $this->arrayDataOfCoordinator($consultantInvitation->getActivity()->getCoordinatorActivity()),
                "consultant" => $this->arrayDataOfConsultant($consultantInvitation->getActivity()->getConsultantActivity()),
                "participant" => $this->arrayDataOfParticipant($consultantInvitation->getActivity()->getParticipantActivity()),
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
        $consultantInvitataionRepository = $this->em->getRepository(ConsultantInvitation::class);
        
        return new ViewInvitationForConsultant($consultantInvitataionRepository);
    }

}
