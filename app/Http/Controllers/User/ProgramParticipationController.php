<?php

namespace App\Http\Controllers\User;

use Participant\Application\Service\UserQuitParticipation;
use Participant\Domain\Model\UserParticipant as UserParticipant2;
use Query\Application\Service\User\ViewProgramParticipation;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;
use Query\Domain\Model\User\UserParticipant;

class ProgramParticipationController extends UserBaseController
{

    public function quit($programParticipationId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->userId(), $programParticipationId);
        return $this->commandOkResponse();
    }

    public function show($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipation = $service->showById($this->userId(), $programParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramParticipation($programParticipation));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");
        $programParticipations = $service->showAll($this->userId(), $this->getPage(), $this->getPageSize(), $activeStatus);
        
        $result = [];
        $result['total'] = count($programParticipations);
        foreach ($programParticipations as $programParticipation) {
            $result['list'][] = [
                "id" => $programParticipation->getId(),
                'program' => [
                    'id' => $programParticipation->getProgram()->getId(),
                    'name' => $programParticipation->getProgram()->getName(),
                    'removed' => $programParticipation->getProgram()->isRemoved(),
                    'firm' => [
                        'id' => $programParticipation->getProgram()->getFirm()->getId(),
                        'name' => $programParticipation->getProgram()->getFirm()->getName(),
                    ],
                ],
                'enrolledTime' => $programParticipation->getEnrolledTimeString(),
                'active' => $programParticipation->isActive(),
                'note' => $programParticipation->getNote(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfProgramParticipation(UserParticipant $programParticipation): array
    {
        return [
            "id" => $programParticipation->getId(),
            'program' => [
                'id' => $programParticipation->getProgram()->getId(),
                'name' => $programParticipation->getProgram()->getName(),
                'removed' => $programParticipation->getProgram()->isRemoved(),
                'firm' => [
                    'id' => $programParticipation->getProgram()->getFirm()->getId(),
                    'name' => $programParticipation->getProgram()->getFirm()->getName(),
                ],
            ],
            'enrolledTime' => $programParticipation->getEnrolledTimeString(),
            'active' => $programParticipation->isActive(),
            'note' => $programParticipation->getNote(),
            "metricAssignment" => $this->arrayDataOfMetricAssignment($programParticipation->getMetricAssignment()),
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
        $programParticipationRepository = $this->em->getRepository(UserParticipant::class);
        return new ViewProgramParticipation($programParticipationRepository);
    }
    protected function buildQuitService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant2::class);
        return new UserQuitParticipation($userParticipantRepository);
    }

}
 