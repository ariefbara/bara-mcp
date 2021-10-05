<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Participant\Application\Service\Firm\Client\TeamMembership\QuitProgramParticipation;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\TeamProgramParticipation as TeamProgramParticipation2;
use Query\Application\Service\Firm\Team\ViewTeamProgramParticipation;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;
use Query\Domain\Model\Firm\Team\TeamProgramParticipation;

class ProgramParticipationController extends AsTeamMemberBaseController
{

    public function quit($teamId, $teamProgramParticipationId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId);
        
        return $this->commandOkResponse();
    }

    public function show($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $teamProgramParticipation = $service->showById($teamId, $teamProgramParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfTeamProgramParticipation($teamProgramParticipation));
    }

    public function showAll($teamId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");
        $teamProgramParticipations = $service->showAll($teamId, $this->getPage(), $this->getPageSize(), $activeStatus);
        
        $result = [];
        $result["total"] = count($teamProgramParticipations);
        foreach ($teamProgramParticipations as $teamProgramParticipation) {
            $result["list"][] = [
                "id" => $teamProgramParticipation->getId(),
                "enrolledTime" => $teamProgramParticipation->getEnrolledTimeString(),
                "note" => $teamProgramParticipation->getNote(),
                "active" => $teamProgramParticipation->isActive(),
                "program" => [
                    "id" => $teamProgramParticipation->getProgram()->getId(),
                    "name" => $teamProgramParticipation->getProgram()->getName(),
                    "removed" => $teamProgramParticipation->getProgram()->isRemoved(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfTeamProgramParticipation(TeamProgramParticipation $teamProgramParticipation): array
    {
        $sponsors = [];
        foreach ($teamProgramParticipation->getProgram()->iterateActiveSponsort() as $sponsor) {
            $logo = empty($sponsor->getLogo()) ? null : [
                "id" => $sponsor->getLogo()->getId(),
                "url" => $sponsor->getLogo()->getFullyQualifiedFileName(),
            ];
            $sponsors[] = [
                "id" => $sponsor->getId(),
                "name" => $sponsor->getName(),
                "website" => $sponsor->getWebsite(),
                "logo" => $logo,
            ];
        }
        return [
            "id" => $teamProgramParticipation->getId(),
            "enrolledTime" => $teamProgramParticipation->getEnrolledTimeString(),
            "note" => $teamProgramParticipation->getNote(),
            "active" => $teamProgramParticipation->isActive(),
            "program" => [
                "id" => $teamProgramParticipation->getProgram()->getId(),
                "name" => $teamProgramParticipation->getProgram()->getName(),
                "removed" => $teamProgramParticipation->getProgram()->isRemoved(),
                "sponsors" => $sponsors,
            ],
            "metricAssignment" => $this->arrayDataOfMetricAssignment($teamProgramParticipation->getMetricAssignment()),
        ];
    }
    protected function arrayDataOfMetricAssignment(?MetricAssignment $metricAssignment): ?array
    {
        if (empty($metricAssignment)) {
            return null;
        }
        $assignmentFields = [];
        foreach ($metricAssignment->iterateActiveAssignmentFields() as $assignmentField) {
            $assignmentFields[] = $this->arrayDataOfAssignmentField($assignmentField);
        }
        return [
            "id" => $metricAssignment->getId(),
            "startDate" => $metricAssignment->getStartDateString(),
            "endDate" => $metricAssignment->getEndDateString(),
            "assignmentFields" => $assignmentFields,
            'lastMetricAssignmentReport' => $this->arrayDataOfMetricAssignmentReport(
                    $metricAssignment->getLastApprovedMetricAssignmentReports()),
        ];
    }
    protected function arrayDataOfAssignmentField(AssignmentField $assignmentField): array
    {
        return [
            "id" => $assignmentField->getId(),
            "target" => $assignmentField->getTarget(),
            "metric" => [
                "id" => $assignmentField->getMetric()->getId(),
                "name" => $assignmentField->getMetric()->getName(),
                "minValue" => $assignmentField->getMetric()->getMinValue(),
                "maxValue" => $assignmentField->getMetric()->getMaxValue(),
                "higherIsBetter" => $assignmentField->getMetric()->getHigherIsBetter(),
            ],
        ];
    }
    protected function arrayDataOfMetricAssignmentReport(?MetricAssignmentReport $metricAssignmentReport): ?array
    {
        if (empty($metricAssignmentReport)) {
            return null;
        }
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
            "assignmentFieldId" => $assignmentFieldValue->getAssignmentField()->getId(),
        ];
    }
    
    protected function buildViewService()
    {
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        return new ViewTeamProgramParticipation($teamProgramParticipationRepository);
    }
    protected function buildQuitService()
    {
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation2::class);
        return new QuitProgramParticipation($teamMembershipRepository, $teamProgramParticipationRepository);
    }

}
