<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use Client\Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId;
use Query\{
    Application\Service\Client\ProgramParticipation\MissionView,
    Application\Service\Client\ProgramParticipation\WorksheetView,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\Program\Participant\Worksheet
};

class MissionController extends ClientBaseController
{

    public function show($programParticipationId, $missionId)
    {
        $viewService = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $mission = $viewService->showById($programParticipationCompositionId, $missionId);
        $missionData = $this->arrayDataOfMission($mission);

        $worksheetView = $this->buildWorksheetViewService();
        $worksheets = $worksheetView->showAllWorksheetsCorrespondWithMission(
                $programParticipationCompositionId, $mission->getId());
        $worksheetData = [];
        foreach ($worksheets as $worksheet) {
            $worksheetData[] = $this->arrayDataOfWorksheet($worksheet);
        }

        $missionData['worksheets'] = $worksheetData;
        return $this->singleQueryResponse($missionData);
    }

    public function showByPosition($programParticipationId, $position)
    {
        $viewService = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $mission = $viewService->showByPosition($programParticipationCompositionId, $this->stripTagsVariable($position));
        $missionData = $this->arrayDataOfMission($mission);

        $worksheetView = $this->buildWorksheetViewService();
        $worksheets = $worksheetView->showAllWorksheetsCorrespondWithMission(
                $programParticipationCompositionId, $mission->getId());
        $worksheetData = [];
        foreach ($worksheets as $worksheet) {
            $worksheetData[] = $this->arrayDataOfWorksheet($worksheet);
        }

        $missionData['worksheets'] = $worksheetData;
        return $this->singleQueryResponse($missionData);
    }

    public function showAll($programParticipationId)
    {
        $viewService = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId(),
                $programParticipationId);
        $missions = $viewService->showAll($programParticipationCompositionId);

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

    protected function arrayDataOfMission(Mission $mission): array
    {
        $parent = empty($mission->getParent()) ? null : $this->arrayDataOfParentMission($mission->getParent());
        return [
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
            "position" => $mission->getPosition(),
            "parent" => $parent,
        ];
    }

    protected function arrayDataOfParentMission(Mission $parentMission): array
    {
        $parent = empty($parentMission->getParent()) ? null : $this->arrayDataOfParentMission($parentMission->getParent());
        return [
            "id" => $parentMission->getId(),
            "name" => $parentMission->getName(),
            "parent" => $parent,
        ];
    }

    protected function arrayDataOfWorksheet(Worksheet $worksheet): array
    {
        $parent = empty($worksheet->getParent()) ? null : $this->arrayDataOfWorksheet($worksheet->getParent());
        return [
            'id' => $worksheet->getId(),
            'name' => $worksheet->getName(),
            'parent' => $parent,
        ];
    }

    protected function buildViewService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        return new MissionView($missionRepository);
    }

    protected function buildWorksheetViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new WorksheetView($worksheetRepository);
    }

}
