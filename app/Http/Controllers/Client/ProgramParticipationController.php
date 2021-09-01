<?php

namespace App\Http\Controllers\Client;

use Participant\Application\Service\ClientQuitParticipation;
use Participant\Domain\Model\ClientParticipant as ClientParticipant2;
use Query\Application\Service\Firm\Client\ViewProgramParticipation;
use Query\Domain\Model\Firm\Client\ClientParticipant;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport;
use Query\Domain\Model\Firm\Program\Participant\MetricAssignment\MetricAssignmentReport\AssignmentFieldValue;

class ProgramParticipationController extends ClientBaseController
{
    public function quit($programParticipationId)
    {
        $service = $this->buildQuitService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId);
        return $this->commandOkResponse();
    }

    public function show($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipation = $service->showById($this->firmId(), $this->clientId(), $programParticipationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramParticipation($programParticipation));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");
        $programParticipations = $service->showAll(
                $this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize(), $activeStatus);
        
        $result = [];
        $result['total'] = count($programParticipations);
        foreach ($programParticipations as $programParticipation) {
            $result['list'][] = [
                "id" => $programParticipation->getId(),
                "program" => [
                    "id" => $programParticipation->getProgram()->getId(),
                    "name" => $programParticipation->getProgram()->getName(),
                    "removed" => $programParticipation->getProgram()->isRemoved(),
                ],
                "enrolledTime" => $programParticipation->getEnrolledTimeString(),
                "active" => $programParticipation->isActive(),
                "note" => $programParticipation->getNote(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfProgramParticipation(ClientParticipant $programParticipation): array
    {
        return [
            "id" => $programParticipation->getId(),
            "program" => [
                "id" => $programParticipation->getProgram()->getId(),
                "name" => $programParticipation->getProgram()->getName(),
                "removed" => $programParticipation->getProgram()->isRemoved(),
            ],
            "enrolledTime" => $programParticipation->getEnrolledTimeString(),
            "active" => $programParticipation->isActive(),
            "note" => $programParticipation->getNote(),
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

    protected function buildQuitService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant2::class);
        return new ClientQuitParticipation($clientParticipantRepository);
    }

    protected function buildViewService()
    {
        $programParticipationRepository = $this->em->getRepository(ClientParticipant::class);
        return new ViewProgramParticipation($programParticipationRepository);
    }

}
