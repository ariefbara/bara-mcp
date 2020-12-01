<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\User\UserBaseController;
use Participant\ {
    Application\Service\UserParticipant\SubmitMetricAssignmentReport,
    Application\Service\UserParticipant\UpdateMetricAssignmentReport,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport as MetricAssignmentReport2,
    Domain\Model\UserParticipant,
    Domain\Service\MetricAssignmentReportDataProvider,
    Domain\SharedModel\FileInfo as FileInfo2
};
use Query\ {
    Application\Service\User\ProgramParticipation\ViewMetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue,
    Domain\Model\Shared\FileInfo
};

class MetricAssignmentReportController extends UserBaseController
{

    public function submit($programParticipationId)
    {
        $service = $this->buildSubmitService();
        $observationTime = $this->dateTimeImmutableOfInputRequest("observationTime");

        $metricAssignmentReportId = $service->execute(
                $this->userId(), $programParticipationId, $observationTime,
                $this->getMetricAssignmentReportDataProvider());

        $viewService = $this->buildViewService();
        $metricAssignmentReport = $viewService->showById($this->userId(), $metricAssignmentReportId);
        return $this->commandCreatedResponse($this->arrayDataOfMetricAssignmentReport($metricAssignmentReport));
    }

    public function update($programParticipationId, $metricAssignmentReportId)
    {
        $service = $this->buildUpdateService();
        $service->execute($this->userId(), $metricAssignmentReportId, $this->getMetricAssignmentReportDataProvider());

        return $this->show($programParticipationId, $metricAssignmentReportId);
    }

    protected function getMetricAssignmentReportDataProvider()
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo2::class);
        $metricAssignmentReportDataProvider = new MetricAssignmentReportDataProvider($fileInfoRepository);
        foreach ($this->request->input("assignmentFieldValues") as $assignmentFieldValue) {
            $assignmentFieldId = $this->stripTagsVariable($assignmentFieldValue["assignmentFieldId"]);
            $value = $this->floatOfVariable($assignmentFieldValue["value"]);
            $note = $this->stripTagsVariable($assignmentFieldValue["note"]);
            $fileInfoId = $this->stripTagsVariable($assignmentFieldValue["fileInfoId"]);
            $metricAssignmentReportDataProvider->addAssignmentFieldValueData($assignmentFieldId, $value, $note,
                    $fileInfoId);
        }
        return $metricAssignmentReportDataProvider;
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

    protected function buildSubmitService()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport2::class);
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        return new SubmitMetricAssignmentReport($metricAssignmentReportRepository, $userParticipantRepository);
    }

    protected function buildUpdateService()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport2::class);
        return new UpdateMetricAssignmentReport($metricAssignmentReportRepository);
    }

}
