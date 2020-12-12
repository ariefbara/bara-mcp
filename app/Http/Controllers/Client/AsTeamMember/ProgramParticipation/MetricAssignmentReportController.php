<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Participant\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\SubmitMetricAssignmentReport,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\UpdateMetricAssignmentReport,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport as MetricAssignmentReport2,
    Domain\Model\TeamProgramParticipation,
    Domain\Service\MetricAssignmentReportDataProvider,
    Domain\SharedModel\FileInfo
};
use Query\ {
    Application\Service\Firm\Team\ProgramParticipation\ViewMetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue,
    Domain\Model\Shared\FileInfo as FileInfo2
};

class MetricAssignmentReportController extends AsTeamMemberBaseController
{

    public function submit($teamId, $teamProgramParticipationId)
    {
        $service = $this->buildSubmitService();
        $observationTime = $this->dateTimeImmutableOfInputRequest("observationTime");

        $metricAssignmentReportId = $service->execute(
                $this->firmId(), $teamId, $this->clientId(), $teamProgramParticipationId, $observationTime,
                $this->getMetricAssignmentReportDataProvider());

        $viewService = $this->buildViewService();
        $metricAssignmentReport = $viewService->showById($teamId, $metricAssignmentReportId);
        return $this->commandCreatedResponse($this->arrayDataOfMetricAssignmentReport($metricAssignmentReport));
    }

    public function update($teamId, $teamProgramParticipationId, $metricAssignmentReportId)
    {
        $service = $this->buildUpdateService();
        $service->execute(
                $this->firmId(), $teamId, $this->clientId(), $metricAssignmentReportId,
                $this->getMetricAssignmentReportDataProvider());

        return $this->show($teamId, $teamProgramParticipationId, $metricAssignmentReportId);
    }

    protected function getMetricAssignmentReportDataProvider()
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
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

    public function show($teamId, $teamProgramParticipationId, $metricAssignmentReportId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $service = $this->buildViewService();
        $metricAssignmentReport = $service->showById($teamId, $metricAssignmentReportId);
        return $this->singleQueryResponse($this->arrayDataOfMetricAssignmentReport($metricAssignmentReport));
    }

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        $service = $this->buildViewService();
        $metricAssignmentReports = $service
                ->showAll($teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize());

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

    protected function arrayDataOfFileInfo(?FileInfo2 $attachedFileInfo): ?array
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
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamPrograParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        return new SubmitMetricAssignmentReport(
                $metricAssignmentReportRepository, $teamMembershipRepository, $teamPrograParticipationRepository);
    }

    protected function buildUpdateService()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        return new UpdateMetricAssignmentReport($metricAssignmentReportRepository, $teamMembershipRepository);
    }

}
