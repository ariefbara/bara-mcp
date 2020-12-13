<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant;

use Query\Application\Service\Firm\Program\Participant\ViewMetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;
use Query\Domain\Model\Shared\FileInfo;

class MetricAssignmentReportController extends AsProgramConsultantBaseController
{
    public function show($programId, $metricAssignmentReportId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $metricAssignmentReport = $service->showById($programId, $metricAssignmentReportId);
        return $this->singleQueryResponse($this->arrayDataOfMetricAssignmentReport($metricAssignmentReport));
    }

    public function showAll($programId, $participantId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $metricAssignmentReports = $service->showAll($programId, $participantId, $this->getPage(), $this->getPageSize());
        
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

    protected function buildViewService()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport::class);
        return new ViewMetricAssignmentReport($metricAssignmentReportRepository);
    }
    
}
