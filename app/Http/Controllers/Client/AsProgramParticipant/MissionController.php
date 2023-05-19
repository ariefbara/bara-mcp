<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\ {
    Application\Service\Firm\Client\AsProgramParticipant\ViewAllMissionInProgramWithSubmittedWorksheetSummary,
    Application\Service\Firm\Program\ViewMission,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\WorksheetForm,
    Infrastructure\Persistence\Doctrine\Repository\DoctrineMissionWithSubmittedWorksheetSummaryRepository
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
    
    public function showByPosition($programId, $position)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $service = $this->buildViewService();
        $mission = $service->showByPosition($programId, $position);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }
    
    public function showAll($programId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $service = $this->buildViewAllMissionWithSubmittedWorksheetSummary();
        $missions = $service->showAll($programId, $this->clientId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = $service->getTotalMission($programId);
        foreach ($missions as $mission) {
            $result['list'][] = [
                'id' => $mission['id'],
                'name' => $mission['name'],
                'description' => $mission['description'],
                'position' => $mission['position'],
                'submittedWorksheet' => $mission['submittedWorksheet'],
                'worksheetForm' => [
                    'id' => $mission['worksheetFormId'],
                    'name' => $mission['worksheetFormName'],
                ],
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
    protected function arrayDataOfWorksheetForm(?WorksheetForm $worksheetForm): ?array
    {
        if (empty($worksheetForm)) {
            return null;
        }
        $data = (new FormToArrayDataConverter())->convert($worksheetForm);
        $data['id'] = $worksheetForm->getId();
        return $data;
    }
    
    protected function buildViewService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        return new ViewMission($missionRepository);
    }
    protected function buildViewAllMissionWithSubmittedWorksheetSummary()
    {
        $missionWithSubmittedWorksheetSummaryRepository = new DoctrineMissionWithSubmittedWorksheetSummaryRepository($this->em);
        return new ViewAllMissionInProgramWithSubmittedWorksheetSummary($missionWithSubmittedWorksheetSummaryRepository);
    }

}
