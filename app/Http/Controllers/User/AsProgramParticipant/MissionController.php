<?php

namespace App\Http\Controllers\User\AsProgramParticipant;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\ {
    Application\Service\Firm\Program\ViewMission,
    Application\Service\User\AsProgramParticipant\ViewAllMissionInProgramWithSubmittedWorksheetSummary,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\WorksheetForm,
    Infrastructure\Persistence\Doctrine\Repository\DoctrineMissionWithSubmittedWorksheetSummaryRepository
};

class MissionController extends AsProgramParticipantBaseController
{

    public function show($firmId, $programId, $missionId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewService();
        $mission = $service->showById($firmId, $programId, $missionId);
        
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }
    
    public function showByPosition($firmId, $programId, $position)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewService();
        $mission = $service->showByPosition($programId, $position);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }
    
    public function showAll($firmId, $programId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $service = $this->buildViewAllMissionWithSubmittedWorksheetSummary();
        $missions = $service->showAll($programId, $this->userId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = $service->getTotalMission($programId);
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
