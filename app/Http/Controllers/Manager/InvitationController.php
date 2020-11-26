<?php

namespace App\Http\Controllers\Manager;

use ActivityInvitee\ {
    Application\Service\Manager\SubmitReport,
    Domain\Model\ManagerInvitee as ManagerInvitee2
};
use App\Http\Controllers\ {
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    FormToArrayDataConverter
};
use Query\ {
    Application\Service\Firm\Manager\ViewInvitationForManager,
    Domain\Model\Firm\FeedbackForm,
    Domain\Model\Firm\Manager\ManagerInvitee,
    Domain\Model\Firm\Program\Activity\Invitee\InviteeReport,
    Domain\Model\Firm\Program\ActivityType
};
use SharedContext\Domain\ {
    Model\SharedEntity\FileInfo,
    Service\FileInfoBelongsToManagerFinder
};

class InvitationController extends ManagerBaseController
{
    
    public function submitReport($invitationId)
    {
        $service = $this->buildSubmitReportService();
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToManagerFinder($fileInfoRepository, $this->firmId(), $this->managerId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        
        $service->execute($this->firmId(), $this->managerId(), $invitationId, $formRecordData);
        
        return $this->show($invitationId);
    }
    
    public function show($invitationId)
    {
        $service = $this->buildViewService();
        $invitation = $service->showById($this->firmId(), $this->managerId(), $invitationId);
        
        return $this->singleQueryResponse($this->arrayDataOfActivityInvitation($invitation));
    }
    
    public function showAll()
    {
        $service = $this->buildViewService();
        $invitations = $service->showAll($this->firmId(), $this->managerId(), $this->getPage(), $this->getPageSize());
        
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
    
    protected function arrayDataOfActivityInvitation(ManagerInvitee $invitation): array
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
        return isset($report)? (new FormRecordToArrayDataConverter())->convert($report): null;
    }
    
    protected function buildViewService()
    {
        $managerInvitationRepository = $this->em->getRepository(ManagerInvitee::class);
        return new ViewInvitationForManager($managerInvitationRepository);
    }
    
    protected function buildSubmitReportService()
    {
        $activityInvitationRepository = $this->em->getRepository(ManagerInvitee2::class);
        return new SubmitReport($activityInvitationRepository);
    }
    
    
}
