<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\User\UserBaseController;
use Participant\ {
    Application\Service\UserParticipant\MetricAssignment\SubmitMetricAssignmentReport,
    Application\Service\UserParticipant\MetricAssignment\UpdateMetricAssignmentReport,
    Domain\Model\Participant\MetricAssignment,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport as MetricAssignmentReport2,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReportData
};
use Query\ {
    Application\Service\User\ProgramParticipation\ViewMetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue
};

class MetricAssignmentReportController extends UserBaseController
{

    public function submit($programParticipationId)
    {
        $service = $this->buildSubmitService();
        $metricAssignmentId = $this->stripTagsInputRequest("metricAssignmentId");
        $observeTime = $this->dateTimeImmutableOfInputRequest("observeTime");

        $metricAssignmentReportId = $service->execute(
                $this->userId(), $metricAssignmentId, $observeTime, $this->getMetricAssignmentReportData());

        $viewService = $this->buildViewService();
        $metricAssignmentReport = $viewService->showById($this->userId(), $metricAssignmentReportId);
        return $this->commandCreatedResponse($this->arrayDataOfMetricAssignmentReport($metricAssignmentReport));
    }

    public function update($programParticipationId, $metricAssignmentReportId)
    {
        $service = $this->buildUpdateService();
        $service->execute($this->userId(), $metricAssignmentReportId, $this->getMetricAssignmentReportData());

        return $this->show($programParticipationId, $metricAssignmentReportId);
    }

    protected function getMetricAssignmentReportData()
    {
        $metricAssignmentReportData = new MetricAssignmentReportData();
        foreach ($this->request->input("assignmentFieldValues") as $assignmentFieldValue) {
            $assignmentFieldId = $this->stripTagsVariable($assignmentFieldValue["assignmentFieldId"]);
            $value = $this->floatOfVariable($assignmentFieldValue["value"]);
            $metricAssignmentReportData->addValueCorrespondWithAssignmentField($assignmentFieldId, $value);
        }
        return $metricAssignmentReportData;
    }

    public function show($programParticipationId, $metricAssignmentReportId)
    {
        $service = $this->buildViewService();
        $metricAssignmentReport = $service->showById($this->userId(), $metricAssignmentReportId);
        return $this->singleQueryResponse($this->arrayDataOfMetricAssignmentReport($metricAssignmentReport));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $metricAssignmentReports = $service
                ->showAll($this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($metricAssignmentReports);
        foreach ($metricAssignmentReports as $metricAssignmentReport) {
            $result["list"][] = [
                "id" => $metricAssignmentReport->getId(),
                "observeTime" => $metricAssignmentReport->getObserveTimeString(),
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
            "observeTime" => $metricAssignmentReport->getObserveTimeString(),
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

    protected function buildSubmitService()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport2::class);
        $metricAssignmentRepository = $this->em->getRepository(MetricAssignment::class);
        return new SubmitMetricAssignmentReport($metricAssignmentReportRepository, $metricAssignmentRepository);
    }

    protected function buildUpdateService()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport2::class);
        return new UpdateMetricAssignmentReport($metricAssignmentReportRepository);
    }

}
