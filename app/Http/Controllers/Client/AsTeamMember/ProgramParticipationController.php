<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Participant\ {
    Application\Service\Firm\Client\TeamMembership\QuitProgramParticipation,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\TeamProgramParticipation as TeamProgramParticipation2
};
use Query\ {
    Application\Service\Firm\Team\ViewTeamProgramParticipation,
    Domain\Model\Firm\Program\Participant\MetricAssignment,
    Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField,
    Domain\Model\Firm\Team\TeamProgramParticipation
};

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
        $teamProgramParticipations = $service->showAll($teamId, $this->getPage(), $this->getPageSize());
        
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
        return [
            "id" => $teamProgramParticipation->getId(),
            "enrolledTime" => $teamProgramParticipation->getEnrolledTimeString(),
            "note" => $teamProgramParticipation->getNote(),
            "active" => $teamProgramParticipation->isActive(),
            "program" => [
                "id" => $teamProgramParticipation->getProgram()->getId(),
                "name" => $teamProgramParticipation->getProgram()->getName(),
                "removed" => $teamProgramParticipation->getProgram()->isRemoved(),
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
        foreach ($metricAssignment->iterateNonRemovedAssignmentFields() as $assignmentField) {
            $assignmentFields[] = $this->arrayDataOfAssignmentField($assignmentField);
        }
        return [
            "id" => $metricAssignment->getId(),
            "startDate" => $metricAssignment->getStartDateString(),
            "endDate" => $metricAssignment->getEndDateString(),
            "assignmentFields" => $assignmentFields,
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
