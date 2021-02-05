<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Query\Application\Service\ViewMission;
use Query\Domain\Model\Firm\Program\Mission;

class MissionController extends Controller
{
    public function showAll($programId)
    {
        $missions = $this->buildViewService()->showAll($programId);
        $result = [];
        $result['total'] = count($missions);
        foreach ($missions as $mission) {
            $result['list'][] = $this->arrayDataOfMission($mission);
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($id)
    {
        $mission = $this->buildViewService()->showById($id);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }
    
    protected function arrayDataOfMission(Mission $mission): array
    {
        return [
            'id' => $mission->getId(),
            'name' => $mission->getName(),
            'description' => $mission->getDescription(),
            'position' => $mission->getPosition(),
            'parent' => $this->arrayDataOfParentMission($mission->getParent()),
        ];
    }
    protected function arrayDataOfParentMission(?Mission $parentMission): ?array
    {
        return empty($parentMission) ? null : [
            'id' => $parentMission->getId(),
            'name' => $parentMission->getName(),
            'description' => $parentMission->getDescription(),
            'position' => $parentMission->getPosition(),
        ];
    }
    
    protected function buildViewService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        return new ViewMission($missionRepository);
    }
}
