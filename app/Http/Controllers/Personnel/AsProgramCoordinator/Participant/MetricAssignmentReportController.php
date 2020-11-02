<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator\Participant;

use App\Http\Controllers\Personnel\AsProgramCoordinator\AsProgramCoordinatorBaseController;
use Query\ {
    Application\Service\Firm\Program\Participant\ViewMetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue
};

class MetricAssignmentReportController extends AsProgramCoordinatorBaseController
{
    public function show($programId, $participantId, $metricAssignmentReportId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $metricAssignmentReport = $service->showById($programId, $metricAssignmentReportId);
        return $this->singleQueryResponse($this->arrayDataOfMetricAssignmentReport($metricAssignmentReport));
    }

    public function showAll($programId, $participantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $metricAssignmentReports = $service->showAll($programId, $participantId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($metricAssignmentReports);
        foreach ($metricAssignmentReports as $metricAssignmentReport) {
            $result["list"][] = [
                "id" => $metricAssignmentReport->getId(),
                "observationTime" => $metricAssignmentReport->getObservationTimeString(),
                "submitTime" => $metricAssignmentReport->getSubmitTimeString(),
                "removed" => $metricAssignmentReport->isRemoved(),
            ];
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
            "removed" => $metricAssignmentReport->isRemoved(),
            "assignmentFieldValues" => $assignmentFieldValues,
        ];
    }

    protected function arrayDataOfAssignmentFieldValue(AssignmentFieldValue $assignmentFieldValue): array
    {
        return [
            "id" => $assignmentFieldValue->getId(),
            "value" => $assignmentFieldValue->getValue(),
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

    protected function buildViewService()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport::class);
        return new ViewMetricAssignmentReport($metricAssignmentReportRepository);
    }
}
