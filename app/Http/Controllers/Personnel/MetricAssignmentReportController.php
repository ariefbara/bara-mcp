<?php

namespace App\Http\Controllers\Personnel;

use Query\Application\Service\Personnel\ViewMetricAssignmentReport;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;
use Query\Domain\Model\Shared\FileInfo;
use Query\Domain\Model\User\UserParticipant;

class MetricAssignmentReportController extends PersonnelBaseController 
{
    public function showAll()
    {
        $metricAssignmentReportRepository= $this->em->getRepository(MetricAssignmentReport::class);
        
        $service = new ViewMetricAssignmentReport($this->personnelQueryRepository(), $metricAssignmentReportRepository);
        $approvedStatus = $this->filterBooleanOfQueryRequest('approvedStatus');
        $metricAssignmentReports = $service->showAll(
                $this->firmId(), $this->personnelId(), $this->getPage(), $this->getPageSize(), $approvedStatus);
        
        $result = [];
        $result["total"] = count($metricAssignmentReports);
        foreach ($metricAssignmentReports as $metricAssignmentReport) {
            $result["list"][] = $this->arrayDataOfMetricAssignmentReport($metricAssignmentReport);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfMetricAssignmentReport(MetricAssignmentReport $metricAssignmentReport): array
    {
        $assignmentFieldValues = [];
        foreach ($metricAssignmentReport->iterateNonremovedAssignmentFieldValues() as $assignmentFieldValue) {
            $assignmentFieldValues[] = $this->arrayDataOfAssignmentFieldValue($assignmentFieldValue);
        }
        return [
            "id" => $metricAssignmentReport->getId(),
            "observationTime" => $metricAssignmentReport->getObservationTimeString(),
            "submitTime" => $metricAssignmentReport->getSubmitTimeString(),
            "approved" => $metricAssignmentReport->isApproved(),
            "note" => $metricAssignmentReport->getNote(),
            "removed" => $metricAssignmentReport->isRemoved(),
            "assignmentFieldValues" => $assignmentFieldValues,
            "metricAssignment" => [
                "id" => $metricAssignmentReport->getMetricAssignment()->getId(),
                'participant' => [
                    "id" => $metricAssignmentReport->getMetricAssignment()->getParticipant()->getId(),
                    'program' => [
                        "id" => $metricAssignmentReport->getMetricAssignment()->getParticipant()->getProgram()->getId(),
                        "name" => $metricAssignmentReport->getMetricAssignment()->getParticipant()->getProgram()->getName(),
                    ],
                    "user" => $this->arrayDataOfUser($metricAssignmentReport->getMetricAssignment()->getParticipant()->getUserParticipant()),
                    "client" => $this->arrayDataOfClient($metricAssignmentReport->getMetricAssignment()->getParticipant()->getClientParticipant()),
                    "team" => $this->arrayDataOfClient($metricAssignmentReport->getMetricAssignment()->getParticipant()->getTeamParticipant()),
                ],
            ],
        ];
    }
    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            'id' => $userParticipant->getUser()->getId(),
            'name' => $userParticipant->getUser()->getFullName(),
        ];
    }
    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            'id' => $clientParticipant->getClient()->getId(),
            'name' => $clientParticipant->getClient()->getFullName(),
        ];
    }
    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            'id' => $teamParticipant->getTeam()->getId(),
            'name' => $teamParticipant->getTeam()->getName(),
        ];
    }
    protected function arrayDataOfAssignmentFieldValue(AssignmentFieldValue $assignmentFieldValue): array
    {
        return [
            "id" => $assignmentFieldValue->getId(),
            "value" => $assignmentFieldValue->getValue(),
            "note" => $assignmentFieldValue->getNote(),
            "fileInfo" => $this->arrayDataOfFileInfo($assignmentFieldValue->getAttachedFileInfo()),
            "assignmentField" => [
                "id" => $assignmentFieldValue->getAssignmentField()->getId(),
                "target" => $assignmentFieldValue->getAssignmentField()->getTarget(),
                "metric" => [
                    "id" => $assignmentFieldValue->getAssignmentField()->getMetric()->getId(),
                    "name" => $assignmentFieldValue->getAssignmentField()->getMetric()->getName(),
                    "minValue" => $assignmentFieldValue->getAssignmentField()->getMetric()->getMinValue(),
                    "maxValue" => $assignmentFieldValue->getAssignmentField()->getMetric()->getMaxValue(),
                ],
            ],
        ];
    }

    protected function arrayDataOfFileInfo(?FileInfo $attachedFileInfo): ?array
    {
        return empty($attachedFileInfo) ? null : [
            "id" => $attachedFileInfo->getId(),
            "path" => $attachedFileInfo->getFullyQualifiedFileName(),
        ];
    }
}
