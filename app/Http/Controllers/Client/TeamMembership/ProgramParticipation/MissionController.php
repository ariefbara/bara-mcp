<?php

namespace App\Http\Controllers\Client\TeamMembership\ProgramParticipation;

use App\Http\Controllers\{
    Client\TeamMembership\TeamMembershipBaseController,
    FormToArrayDataConverter
};
use Query\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ViewMission,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Model\Firm\WorksheetForm
};

class MissionController extends TeamMembershipBaseController
{

    public function showAll($teamMembershipId, $teamProgramParticipationId)
    {
        $this->authorizeActiveTeamMember($teamMembershipId);

        $service = $this->buildViewService();
        $missions = $service->showAll(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId);

        $result = [];
        foreach ($missions as $mission) {
            $result['list'][] = [
                'id' => $mission['id'],
                'name' => $mission['name'],
                'description' => $mission['description'],
                'position' => $mission['position'],
                'submittedWorksheet' => $mission['submittedWorksheet'],
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show($teamMembershipId, $teamProgramParticipationId, $missionId)
    {
        $this->authorizeActiveTeamMember($teamMembershipId);
        $service = $this->buildViewService();
        $mission = $service->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $missionId);

        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }

    public function showByPosition($teamMembershipId, $teamProgramParticipationId, $position)
    {
        $this->authorizeActiveTeamMember($teamMembershipId);
        $service = $this->buildViewService();

        $mission = $service->showByPosition(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $position);
        
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }

    protected function arrayDataOfMission(Mission $mission): array
    {
        $parent = empty($mission->getParent()) ? null : $this->arrayDataOfParentMission($mission->getParent());
        return [
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
            "position" => $mission->getPosition(),
            'worksheetForm' => $this->arrayDataOfWorksheetForm($mission->getWorksheetForm()),
            "parent" => $parent,
        ];
    }

    protected function arrayDataOfParentMission(Mission $parentMission): array
    {
        $parent = empty($parentMission->getParent()) ? null : $this->arrayDataOfParentMission($parentMission->getParent());
        return [
            "id" => $parentMission->getId(),
            "name" => $parentMission->getName(),
            "description" => $parentMission->getDescription(),
            "position" => $parentMission->getPosition(),
            "parent" => $parent,
        ];
    }

    protected function arrayDataOfWorksheetForm(WorksheetForm $worksheetForm): array
    {
        $data = (new FormToArrayDataConverter())->convert($worksheetForm);
        $data['id'] = $worksheetForm->getId();
        return $data;
    }

    protected function buildViewService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        return new ViewMission($missionRepository);
    }

}
