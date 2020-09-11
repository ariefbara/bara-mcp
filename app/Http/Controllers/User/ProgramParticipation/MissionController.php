<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\ {
    FormToArrayDataConverter,
    User\UserBaseController
};
use Query\ {
    Application\Service\User\ProgramParticipation\ViewMission,
    Application\Service\User\ProgramParticipation\ViewWorksheet,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Model\Firm\WorksheetForm
};

class MissionController extends UserBaseController
{

    public function show($programParticipationId, $missionId)
    {
        $viewService = $this->buildViewService();
        $mission = $viewService->showById($this->userId(), $programParticipationId, $missionId);
        $missionData = $this->arrayDataOfMission($mission);

        $worksheetView = $this->buildWorksheetViewService();
        $worksheets = $worksheetView->showAll($this->userId(), $programParticipationId, 1, 100, $missionId, null);
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
        $mission = $viewService->showByPosition($this->userId(), $programParticipationId, $position);
        $missionData = $this->arrayDataOfMission($mission);

        $worksheetView = $this->buildWorksheetViewService();
        $worksheets = $worksheetView->showAll($this->userId(), $programParticipationId, 1, 100, $mission->getId(), null);
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
        $missions = $viewService->showAll($this->userId(), $programParticipationId);

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
        return new ViewMission($missionRepository);
    }

    protected function buildWorksheetViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new ViewWorksheet($worksheetRepository);
    }

}
