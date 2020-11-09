<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Query\ {
    Application\Service\Firm\Team\ProgramParticipation\ViewInvitationForTeamParticipant,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Manager\ManagerActivity,
    Domain\Model\Firm\Program\Consultant\ConsultantActivity,
    Domain\Model\Firm\Program\Coordinator\CoordinatorActivity,
    Domain\Model\Firm\Program\Participant\ParticipantActivity,
    Domain\Model\Firm\Program\Participant\ParticipantInvitation,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class InvitationController extends AsTeamMemberBaseController
{

    public function show($teamId, $teamProgramParticipationId, $invitationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $service = $this->buildViewService();
        $invitation = $service->showById($this->firmId(), $teamId, $invitationId);

        return $this->singleQueryResponse($this->arrayDataOfInvitation($invitation));
    }

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $service = $this->buildViewService();
        $invitations = $service->showAll(
                $this->firmId(), $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($invitations);
        foreach ($invitations as $invitation) {
            $result["list"][] = $this->arrayDataOfInvitation($invitation);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfInvitation(ParticipantInvitation $participantInvitation): array
    {
        return [
            "id" => $participantInvitation->getId(),
            "willAttend" => $participantInvitation->willAttend(),
            "attended" => $participantInvitation->isAttended(),
            "activity" => [
                "id" => $participantInvitation->getActivity()->getId(),
                "name" => $participantInvitation->getActivity()->getName(),
                "description" => $participantInvitation->getActivity()->getDescription(),
                "location" => $participantInvitation->getActivity()->getLocation(),
                "note" => $participantInvitation->getActivity()->getNote(),
                "startTime" => $participantInvitation->getActivity()->getStartTimeString(),
                "endTime" => $participantInvitation->getActivity()->getEndTimeString(),
                "cancelled" => $participantInvitation->getActivity()->isCancelled(),
                "program" => [
                    "id" => $participantInvitation->getActivity()->getProgram()->getId(),
                    "name" => $participantInvitation->getActivity()->getProgram()->getName(),
                ],
                "activityType" => [
                    "id" => $participantInvitation->getActivity()->getActivityType()->getId(),
                    "name" => $participantInvitation->getActivity()->getActivityType()->getName(),
                ],
                "manager" => $this->arrayDataOfManager($participantInvitation->getActivity()->getManagerActivity()),
                "coordinator" => $this->arrayDataOfCoordinator($participantInvitation->getActivity()->getCoordinatorActivity()),
                "consultant" => $this->arrayDataOfConsultant($participantInvitation->getActivity()->getConsultantActivity()),
                "participant" => $this->arrayDataOfParticipant($participantInvitation->getActivity()->getParticipantActivity()),
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
        $participantInvitationRepository = $this->em->getRepository(ParticipantInvitation::class);
        return new ViewInvitationForTeamParticipant($participantInvitationRepository);
    }

}
