<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use ActivityInvitee\{
    Application\Service\ClientParticipant\SubmitReport,
    Domain\Model\ParticipantInvitee as ParticipantInvitee2
};
use App\Http\Controllers\{
    Client\ClientBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    FormToArrayDataConverter
};
use Query\{
    Application\Service\Firm\Client\ProgramParticipation\ViewInvitationForClientParticipant,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Program\Activity\Invitee\InviteeReport,
    Domain\Model\Firm\Program\ActivityType,
    Domain\Model\Firm\Program\Participant\ParticipantInvitee
};
use SharedContext\Domain\{
    Model\SharedEntity\FileInfo,
    Service\FileInfoBelongsToClientFinder
};

class InvitationController extends ClientBaseController
{

    public function submitReport($programParticipationId, $invitationId)
    {
        $service = $this->buildSubmitReportService();
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToClientFinder($fileInfoRepository, $this->firmId(), $this->clientId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();

        $service->execute($this->firmId(), $this->clientId(), $invitationId, $formRecordData);

        return $this->show($programParticipationId, $invitationId);
    }

    public function show($programParticipationId, $invitationId)
    {
        $service = $this->buildViewService();
        $invitation = $service->showById($this->firmId(), $this->clientId(), $invitationId);

        return $this->singleQueryResponse($this->arrayDataOfInvitation($invitation));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $invitations = $service->showAll(
                $this->firmId(), $this->clientId(), $programParticipationId, $this->getPage(), $this->getPageSize(),
                $this->getTimeIntervalFilter());

        $result = [];
        $result["total"] = count($invitations);
        foreach ($invitations as $invitation) {
            $result["list"][] = [
                "id" => $invitation->getId(),
                "willAttend" => $invitation->isWillAttend(),
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
            "willAttend" => $invitation->isWillAttend(),
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
        return new ViewInvitationForClientParticipant($participantInvitationRepository);
    }

    protected function buildSubmitReportService()
    {
        $activityInvitationRepository = $this->em->getRepository(ParticipantInvitee2::class);
        return new SubmitReport($activityInvitationRepository);
    }

}
