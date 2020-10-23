<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Participant\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\SubmitMetricAssignmentReport,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\UpdateMetricAssignmentReport,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\MetricAssignment,
    Domain\Model\Participant\MetricAssignment\MetricAssignmentReport as MetricAssignmentReport2
};
use Query\{
    Application\Service\Firm\Team\ProgramParticipation\ViewMetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport,
    Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue
};

class MetricAssignmentReportController extends AsTeamMemberBaseController
{

    public function submit($teamId, $teamProgramParticipationId)
    {
        $service = $this->buildSubmitService();
        $metricAssignmentId = $this->stripTagsInputRequest("metricAssignmentId");
        $observeTime = $this->dateTimeImmutableOfInputRequest("observeTime");

        $metricAssignmentReportId = $service->execute(
                $this->firmId(), $teamId, $this->clientId(), $metricAssignmentId, $observeTime,
                $this->getMetricAssignmentReportData());

        $viewService = $this->buildViewService();
        $metricAssignmentReport = $viewService->showById($teamId, $metricAssignmentReportId);
        return $this->commandCreatedResponse($this->arrayDataOfMetricAssignmentReport($metricAssignmentReport));
    }

    public function update($teamId, $teamProgramParticipationId, $metricAssignmentReportId)
    {
        $service = $this->buildUpdateService();
        $service->execute(
                $this->firmId(), $teamId, $this->clientId(), $metricAssignmentReportId,
                $this->getMetricAssignmentReportData());
        
        return $this->show($teamId, $teamProgramParticipationId, $metricAssignmentReportId);
    }

    protected function getMetricAssignmentReportData()
    {
        $metricAssignmentReportData = new MetricAssignment\MetricAssignmentReportData();
        foreach ($this->request->input("assignmentFieldValues") as $assignmentFieldValue) {
            $assignmentFieldId = $this->stripTagsVariable($assignmentFieldValue["assignmentFieldId"]);
            $value = $this->floatOfVariable($assignmentFieldValue["value"]);
            $metricAssignmentReportData->addValueCorrespondWithAssignmentField($assignmentFieldId, $value);
        }
        return $metricAssignmentReportData;
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
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $metricAssignmentRepository = $this->em->getRepository(MetricAssignment::class);
        return new SubmitMetricAssignmentReport(
                $metricAssignmentReportRepository, $teamMembershipRepository, $metricAssignmentRepository);
    }

    protected function buildUpdateService()
    {
        $metricAssignmentReportRepository = $this->em->getRepository(MetricAssignmentReport2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        return new UpdateMetricAssignmentReport($metricAssignmentReportRepository, $teamMembershipRepository);
    }

}
