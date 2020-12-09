<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Firm\Program\ViewActivityReport;
use Query\Domain\Model\Firm\Manager\ManagerInvitee;
use Query\Domain\Model\Firm\Program\Activity\Invitee;
use Query\Domain\Model\Firm\Program\Activity\Invitee\InviteeReport;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantInvitee;
use Query\Domain\Model\Firm\Program\Coordinator\CoordinatorInvitee;
use Query\Domain\Model\Firm\Program\Participant\ParticipantInvitee;

class ActivityReportController extends AsProgramCoordinatorBaseController
{

    public function showAllReportsInActivity($programId, $activityId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $activityReports = $this->buildViewService()->showAllReportInActivity(
                $this->firmId(), $programId, $activityId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($activityReports);
        foreach ($activityReports as $activityReport) {
            $result["list"][] = [
                "id" => $activityReport->getId(),
                "submitTime" => $activityReport->getSubmitTimeString(),
                "invitee" => $this->arrayDataOfInvitee($activityReport->getInvitee()),
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $activityReportId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $activityReport = $this->buildViewService()->showById($this->firmId(), $programId, $activityReportId);

        return $this->singleQueryResponse($this->arrayDataOfActivityReport($activityReport));
    }

    protected function arrayDataOfActivityReport(InviteeReport $activityReport): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($activityReport);
        $result["id"] = $activityReport->getId();
        $result["invitee"] = $this->arrayDataOfInvitee($activityReport->getInvitee());
        return $result;
    }
    protected function arrayDataOfInvitee(Invitee $invitee): array
    {
        return [
            "id" => $invitee->getId(),
            "anInitiator" => $invitee->isAnInitiator(),
            "manager" => $this->arrayDataOfManager($invitee->getManagerInvitee()),
            "coordinator" => $this->arrayDataOfCoordinator($invitee->getCoordinatorInvitee()),
            "consultant" => $this->arrayDataOfConsultant($invitee->getConsultantInvitee()),
            "participant" => $this->arrayDataOfParticipant($invitee->getParticipantInvitee()),
        ];
    }
    protected function arrayDataOfManager(?ManagerInvitee $managerInvitee): ?array
    {
        return empty($managerInvitee)? null: [
            "id" => $managerInvitee->getManager()->getId(),
            "name" => $managerInvitee->getManager()->getName(),
        ];
    }
    protected function arrayDataOfCoordinator(?CoordinatorInvitee $coordinatorInvitee): ?array
    {
        return empty($coordinatorInvitee)? null: [
            "id" => $coordinatorInvitee->getCoordinator()->getId(),
            "name" => $coordinatorInvitee->getCoordinator()->getPersonnel()->getName(),
        ];
    }
    protected function arrayDataOfConsultant(?ConsultantInvitee $consultantInvitee): ?array
    {
        return empty($consultantInvitee)? null: [
            "id" => $consultantInvitee->getConsultant()->getId(),
            "name" => $consultantInvitee->getConsultant()->getPersonnel()->getName(),
        ];
    }
    protected function arrayDataOfParticipant(?ParticipantInvitee $participantInvitee): ?array
    {
        
        return empty($participantInvitee)? null: [
            "id" => $participantInvitee->getParticipant()->getId(),
            "name" => $participantInvitee->getParticipant()->getName(), 
        ];
    }

    protected function buildViewService()
    {
        $activityReportRepository = $this->em->getRepository(InviteeReport::class);
        return new ViewActivityReport($activityReportRepository);
    }

}
