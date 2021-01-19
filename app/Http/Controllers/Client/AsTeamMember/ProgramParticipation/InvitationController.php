<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use ActivityInvitee\{
    Application\Service\TeamMember\SubmitReport,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\ParticipantInvitee as ParticipantInvitee2
};
use App\Http\Controllers\{
    Client\AsTeamMember\AsTeamMemberBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    FormToArrayDataConverter
};
use Query\{
    Application\Service\Firm\Team\ProgramParticipation\ViewInvitationForTeamParticipant,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\Activity\Invitee\InviteeReport,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\Participant\ParticipantInvitee
};
use SharedContext\Domain\{
    Model\SharedEntity\FileInfo,
    Service\FileInfoBelongsToTeamFinder
};

class InvitationController extends AsTeamMemberBaseController
{

    public function submitReport($teamId, $teamProgramParticipationId, $invitationId)
    {
        $service = $this->buildSubmitReportService();
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToTeamFinder($fileInfoRepository, $this->firmId(), $teamId);
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();

        $service->execute($this->firmId(), $this->clientId(), $teamId, $invitationId, $formRecordData);

        return $this->show($teamId, $teamProgramParticipationId, $invitationId);
    }

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
                $this->firmId(), $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize(),
                $this->getTimeIntervalFilter());

        $result = [];
        $result["total"] = count($invitations);
        foreach ($invitations as $invitation) {
            $result["list"][] = [
                "id" => $invitation->getId(),
                "willAttend" => $invitation->willAttend(),
                "attended" => $invitation->isAttended(),
                "anInitiator" => $invitation->isAnInitiator(),
                "activity" => [
                    "id" => $invitation->getActivity()->getId(),
                    "name" => $invitation->getActivity()->getName(),
                    "location" => $invitation->getActivity()->getLocation(),
                    "startTime" => $invitation->getActivity()->getStartTimeString(),
                    "endTime" => $invitation->getActivity()->getEndTimeString(),
                    "cancelled" => $invitation->getActivity()->isCancelled(),
                    "activityType" => $this->arrayDataOfActivityType($invitation->getActivity()->getActivityType()),
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
            "anInitiator" => $invitation->isAnInitiator(),
            "report" => $this->arrayDataOfReport($invitation->getReport()),
            "activityParticipant" => [
                "id" => $invitation->getActivityParticipant()->getId(),
                "reportForm" => $this->arrayDataOfReportForm($invitation->getActivityParticipant()->getReportForm()),
            ],
            "activity" => [
                "id" => $invitation->getActivity()->getId(),
                "name" => $invitation->getActivity()->getName(),
                "description" => $invitation->getActivity()->getDescription(),
                "location" => $invitation->getActivity()->getLocation(),
                "note" => $invitation->getActivity()->getNote(),
                "startTime" => $invitation->getActivity()->getStartTimeString(),
                "endTime" => $invitation->getActivity()->getEndTimeString(),
                "cancelled" => $invitation->getActivity()->isCancelled(),
                "activityType" => $this->arrayDataOfActivityType($invitation->getActivity()->getActivityType()),
            ],
        ];
    }

    protected function arrayDataOfActivityType(ActivityType $activityType): array
    {
        return [
            "id" => $activityType->getId(),
            "name" => $activityType->getName(),
            "program" => [
                "id" => $activityType->getProgram()->getId(),
                "name" => $activityType->getProgram()->getName(),
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
        return isset($report) ? (new FormRecordToArrayDataConverter())->convert($report) : null;
    }

    protected function buildViewService()
    {
        $participantInvitationRepository = $this->em->getRepository(ParticipantInvitee::class);
        return new ViewInvitationForTeamParticipant($participantInvitationRepository);
    }

    protected function buildSubmitReportService()
    {
        $activityInvitationRepository = $this->em->getRepository(ParticipantInvitee2::class);
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        return new SubmitReport($activityInvitationRepository, $teamMemberRepository);
    }

}
