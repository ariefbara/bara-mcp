<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use Firm\ {
    Application\Service\Firm\Program\Participant\AssignMetrics,
    Domain\Model\Firm\Program\Coordinator,
    Domain\Model\Firm\Program\Metric,
    Domain\Model\Firm\Program\Participant as Participant2,
    Domain\Service\MetricAssignmentDataProvider
};
use Query\ {
    Application\Service\Firm\Program\ViewParticipant,
    Domain\Model\Firm\Client\ClientParticipant,
    Domain\Model\Firm\Program\Participant,
    Domain\Model\Firm\Program\Participant\MetricAssignment,
    Domain\Model\Firm\Program\Participant\MetricAssignment\AssignmentField,
    Domain\Model\Firm\Team\TeamProgramParticipation,
    Domain\Model\User\UserParticipant
};

class ParticipantController extends AsProgramCoordinatorBaseController
{
    
    public function assignMetric($programId, $participantId)
    {
        $service = $this->buildAssignMetricService();
        $service->execute($programId, $this->personnelId(), $participantId, $this->getMetricAssignmentDataProvider());
        
        return $this->show($programId, $participantId);
    }
    
    protected function getMetricAssignmentDataProvider()
    {
        $metricRepositoy = $this->em->getRepository(Metric::class);
        $startDate = $this->dateTimeImmutableOfInputRequest("startDate");
        $endDate = $this->dateTimeImmutableOfInputRequest("endDate");
        $dataProvider = new MetricAssignmentDataProvider($metricRepositoy, $startDate, $endDate);
        foreach ($this->request->input("assignmentFields") as $assignmentField) {
            $metricId = $this->stripTagsVariable($assignmentField["metricId"]);
            $target = $this->stripTagsVariable($assignmentField["target"]);
            $dataProvider->add($metricId, $target);
        }
        return $dataProvider;
    }

    public function showAll($programId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);

        $service = $this->buildViewService();
        $activeStatus = $this->filterBooleanOfQueryRequest("activeStatus");

        $participants = $service->showAll(
                $this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $activeStatus);
        
        $result = [];
        $result["total"] = count($participants);
        foreach ($participants as $participant) {
            $result["list"][] = [
                "id" => $participant->getId(),
                "enrolledTime" => $participant->getEnrolledTimeString(),
                "active" => $participant->isActive(),
                "note" => $participant->getNote(),
                "client" => $this->arrayDataOfClient($participant->getClientParticipant()),
                "user" => $this->arrayDataOfUser($participant->getUserParticipant()),
                "team" => $this->arrayDataOfUser($participant->getTeamParticipant()),
                "hasMetricAssignment" => empty($participant->getMetricAssignment())? false: true,
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $participantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        
        $service = $this->buildViewService();
        $participant = $service->showById($this->firmId(), $programId, $participantId);
        return $this->singleQueryResponse($this->arrayDataOfParticipant($participant));
    }

    protected function arrayDataOfParticipant(Participant $participant): array
    {
        return [
            "id" => $participant->getId(),
            "enrolledTime" => $participant->getEnrolledTimeString(),
            "active" => $participant->isActive(),
            "note" => $participant->getNote(),
            "client" => $this->arrayDataOfClient($participant->getClientParticipant()),
            "user" => $this->arrayDataOfUser($participant->getUserParticipant()),
            "team" => $this->arrayDataOfUser($participant->getTeamParticipant()),
            "metricAssignment" => $this->arrayDataOfMetricAssignment($participant->getMetricAssignment()),
        ];
    }

    protected function arrayDataOfClient(?ClientParticipant $clientParticipant): ?array
    {
        return empty($clientParticipant) ? null : [
            "id" => $clientParticipant->getClient()->getId(),
            "name" => $clientParticipant->getClient()->getFullName(),
        ];
    }

    protected function arrayDataOfUser(?UserParticipant $userParticipant): ?array
    {
        return empty($userParticipant) ? null : [
            "id" => $userParticipant->getUser()->getId(),
            "name" => $userParticipant->getUser()->getFullName(),
        ];
    }

    protected function arrayDataOfTeam(?TeamProgramParticipation $teamParticipant): ?array
    {
        return empty($teamParticipant) ? null : [
            "id" => $teamParticipant->getTeam()->getId(),
            "name" => $teamParticipant->getTeam()->getName(),
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
        return empty($metricAssignment)? null: [
            "startDate" => $metricAssignment->getStartDateString(),
            "endDate" => $metricAssignment->getEndDateString(),
            "assignmentFields" => $assignmentFields,
        ];
    }
    protected function arrayDataOfAssignmentField(?AssignmentField $assignmentField): array
    {
        return [
            "id" => $assignmentField->getId(),
            "target" => $assignmentField->getTarget(),
            "metric" => [
                "id" => $assignmentField->getMetric()->getId(),
                "name" => $assignmentField->getMetric()->getName(),
            ],
        ];
    }

    protected function buildViewService()
    {
        $participantRepository = $this->em->getRepository(Participant::class);
        return new ViewParticipant($participantRepository);
    }
    
    protected function buildAssignMetricService()
    {
        $participantRepository = $this->em->getRepository(Participant2::class);
        $coordinatorRepository = $this->em->getRepository(Coordinator::class);
        return new AssignMetrics($participantRepository, $coordinatorRepository);
    }

}
