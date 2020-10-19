<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\ {
    Application\Service\Firm\Program\ViewMission,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\WorksheetForm
};

class MissionController extends AsProgramParticipantBaseController
{

    public function show($programId, $missionId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $viewService = $this->buildViewService();
        $mission = $viewService->showById($this->firmId(), $programId, $missionId);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }
    
    public function showAll($programId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $viewService = $this->buildViewService();
        $position = $this->stripTagQueryRequest("position");
        $missions = $viewService->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $position);
        
        $result = [];
        $result['total'] = count($missions);
        foreach ($missions as $mission) {
            $result['list'][] = [
                'id' => $mission->getId(),
                'name' => $mission->getName(),
                'description' => $mission->getDescription(),
                'position' => $mission->getPosition(),
                'parent' => $this->arrayDataOfParentMission($mission->getParent()),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfMission(Mission $mission): array
    {
        return [
            'id' => $mission->getId(),
            'name' => $mission->getName(),
            'description' => $mission->getDescription(),
            'position' => $mission->getPosition(),
            'worksheetForm' => $this->arrayDataOfWorksheetForm($mission->getWorksheetForm()),
            'parent' => $this->arrayDataOfParentMission($mission->getParent()),
        ];
    }

    protected function arrayDataOfParentMission(?Mission $mission): ?array
    {
        return !isset($mission)? null: [
            'id' => $mission->getId(),
            'name' => $mission->getName(),
            'description' => $mission->getDescription(),
            'position' => $mission->getPosition(),
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
