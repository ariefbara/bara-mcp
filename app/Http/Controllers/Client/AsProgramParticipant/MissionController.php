<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use App\Http\Controllers\FormToArrayDataConverter;
use Firm\Application\Service\Firm\Program\ProgramCompositionId;
use Query\{
    Application\Service\Firm\Program\MissionView,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\WorksheetForm
};

class MissionController extends AsProgramParticipantBaseController
{

    public function show($firmId, $programId, $missionId)
    {
        $this->authorizedClientIsActiveProgramParticipant($firmId, $programId);

        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($firmId, $programId);
        $mission = $service->showById($programCompositionId, $missionId);
        return $this->arrayDataOfMission($mission);
    }

    public function showAll($firmId, $programId)
    {
        $this->authorizedClientIsActiveProgramParticipant($firmId, $programId);

        $service = $this->buildViewService();
        $programCompositionId = new ProgramCompositionId($firmId, $programId);
        $position = $this->request->query('position') == null ? null :
                $this->stripTagsVariable($this->request->query('position'));
        
        $missions = $service->showAll($programCompositionId, $this->getPage(), $this->getPageSize(), $position);

        $result = [];
        $result['total'] = count($missions);
        foreach ($missions as $mission) {
            $parentData = empty($mission->getParent()) ? null : $this->arrayDataOfParentMission($mission->getParent());
            $result['list'][] = [
                'id' => $mission->getId(),
                'name' => $mission->getName(),
                'description' => $mission->getDescription(),
                'position' => $mission->getPosition(),
                'parent' => $parentData,
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfMission(Mission $mission): array
    {

        $parentData = empty($mission->getParent()) ? null : $this->arrayDataOfParentMission($mission->getParent());
        return [
            'id' => $mission->getId(),
            'name' => $mission->getName(),
            'description' => $mission->getDescription(),
            'position' => $mission->getPosition(),
            'worksheetForm' => $this->arrayDataOfWorksheetForm($mission->getWorksheetForm()),
            'parent' => $parentData,
        ];
    }

    protected function arrayDataOfParentMission(Mission $mission): array
    {
        return [
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
        return new MissionView($missionRepository);
    }

}
