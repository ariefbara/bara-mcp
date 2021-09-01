<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use ActivityInvitee\Application\Service\TeamMember\SubmitReport;
use ActivityInvitee\Domain\DependencyModel\Firm\Client\TeamMembership;
use ActivityInvitee\Domain\Model\ParticipantInvitee as ParticipantInvitee2;
use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Query\Application\Service\Firm\Team\ProgramParticipation\ViewInvitationForTeamParticipant;
use Query\Domain\Model\Firm\FeedbackForm;
use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;
use Query\Domain\Model\Firm\Program\ActivityType;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;
use Query\Infrastructure\QueryFilter\InviteeFilter;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToTeamFinder;

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
        
        $inviteeFilter = (new InviteeFilter())
                ->setCancelledStatus($this->filterBooleanOfQueryRequest('cancelledStatus'))
                ->setFrom($this->dateTimeImmutableOfQueryRequest('from'))
                ->setTo($this->dateTimeImmutableOfQueryRequest('to'));
        
        $invitations = $service->showAll(
                $this->firmId(), $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize(),
                $inviteeFilter);

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
        return new ViewInvitationForTeamParticipant($participantInvitationRepository);
    }

    protected function buildSubmitReportService()
    {
        $activityInvitationRepository = $this->em->getRepository(ParticipantInvitee2::class);
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        return new SubmitReport($activityInvitationRepository, $teamMemberRepository);
    }

}
