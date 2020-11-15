<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use ActivityInvitee\ {
    Application\Service\UserParticipant\SubmitReport,
    Domain\Model\ParticipantInvitee as ParticipantInvitee2
};
use App\Http\Controllers\ {
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    FormToArrayDataConverter,
    User\UserBaseController
};
use Query\ {
    Application\Service\User\ProgramParticipation\ViewInvitationForUserParticipant,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Manager\ManagerActivity,
    Domain\Model\Firm\Program\Activity\Invitee\InviteeReport,
    Domain\Model\Firm\Program\Consultant\ConsultantActivity,
    Domain\Model\Firm\Program\Coordinator\CoordinatorActivity,
    Domain\Model\Firm\Program\Participant\ParticipantActivity,
    Domain\Model\Firm\Program\Participant\ParticipantInvitee,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};
use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Service\FileInfoBelongsToUserFinder
};

class InvitationController extends UserBaseController
{
    
    public function submitReport($programParticipationId, $invitationId)
    {
        $service = $this->buildSubmitReportService();
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToUserFinder($fileInfoRepository, $this->userId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        
        $service->execute($this->userId(), $invitationId, $formRecordData);
        
        return $this->show($programParticipationId, $invitationId);
    }

    public function show($programParticipationId, $invitationId)
    {
        $service = $this->buildViewService();
        $invitation = $service->showById($this->userId(), $invitationId);

        return $this->singleQueryResponse($this->arrayDataOfInvitation($invitation));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $invitations = $service->showAll(
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($invitations);
        foreach ($invitations as $invitation) {
            $result["list"][] = [
                "id" => $invitation->getId(),
                "willAttend" => $invitation->willAttend(),
                "attended" => $invitation->isAttended(),
                "activity" => [
                    "id" => $invitation->getActivity()->getId(),
                    "name" => $invitation->getActivity()->getName(),
                    "location" => $invitation->getActivity()->getLocation(),
                    "startTime" => $invitation->getActivity()->getStartTimeString(),
                    "endTime" => $invitation->getActivity()->getEndTimeString(),
                    "cancelled" => $invitation->getActivity()->isCancelled(),
                    "program" => [
                        "id" => $invitation->getActivity()->getProgram()->getId(),
                        "name" => $invitation->getActivity()->getProgram()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfInvitation(ParticipantInvitee $invitation): array
    {
        return [
            "id" => $invitation->getId(),
            "willAttend" => $invitation->willAttend(),
            "attended" => $invitation->isAttended(),
            "activityParticipant" => [
                "id" => $invitation->getActivityParticipant()->getId(),
                "reportForm" => $this->arrayDataOfReportForm($invitation->getActivityParticipant()->getReportForm()),
            ],
            "report" => $this->arrayDataOfReport($invitation->getReport()),
            "activity" => [
                "id" => $invitation->getActivity()->getId(),
                "name" => $invitation->getActivity()->getName(),
                "description" => $invitation->getActivity()->getDescription(),
                "location" => $invitation->getActivity()->getLocation(),
                "note" => $invitation->getActivity()->getNote(),
                "startTime" => $invitation->getActivity()->getStartTimeString(),
                "endTime" => $invitation->getActivity()->getEndTimeString(),
                "cancelled" => $invitation->getActivity()->isCancelled(),
                "program" => [
                    "id" => $invitation->getActivity()->getProgram()->getId(),
                    "name" => $invitation->getActivity()->getProgram()->getName(),
                ],
                "activityType" => [
                    "id" => $invitation->getActivity()->getActivityType()->getId(),
                    "name" => $invitation->getActivity()->getActivityType()->getName(),
                ],
                "manager" => $this->arrayDataOfManager($invitation->getActivity()->getManagerActivity()),
                "coordinator" => $this->arrayDataOfCoordinator($invitation->getActivity()->getCoordinatorActivity()),
                "consultant" => $this->arrayDataOfConsultant($invitation->getActivity()->getConsultantActivity()),
                "participant" => $this->arrayDataOfParticipant($invitation->getActivity()->getParticipantActivity()),
            ],
        ];
    }
    protected function arrayDataOfReportForm(?FeedbackForm $reportForm): ?array
    {
        if (!isset($reportForm)) {
            return null;
        }
        $reportFormData = (new FormToArrayDataConverter())->convert($reportForm);
        $reportFormData["id"] = $reportForm->getId();
        return $reportFormData;
    }
    protected function arrayDataOfReport(?InviteeReport $report): ?array
    {
        return isset($report)? (new FormRecordToArrayDataConverter())->convert($report): null;
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
        $participantInvitationRepository = $this->em->getRepository(ParticipantInvitee::class);
        return new ViewInvitationForUserParticipant($participantInvitationRepository);
    }
    
    protected function buildSubmitReportService()
    {
        $activityInvitationRepository = $this->em->getRepository(ParticipantInvitee2::class);
        return new SubmitReport($activityInvitationRepository);
    }

}
