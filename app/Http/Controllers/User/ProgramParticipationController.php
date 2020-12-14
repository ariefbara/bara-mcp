<?php

namespace App\Http\Controllers\User;

use Participant\ {
    Application\Service\UserQuitParticipation,
    Domain\Model\UserParticipant as UserParticipant2
};
use Query\ {
    Application\Service\User\ViewProgramParticipation,
    Domain\Model\Firm\Program\Participant\MetricAssignment,
    Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField,
    Domain\Model\User\UserParticipant
};

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
        $activeStatus = $this->stripTagQueryRequest("activeStatus");
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
        $programParticipationRepository = $this->em->getRepository(UserParticipant::class);
        return new ViewProgramParticipation($programParticipationRepository);
    }
    protected function buildQuitService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant2::class);
        return new UserQuitParticipation($userParticipantRepository);
    }

}
 