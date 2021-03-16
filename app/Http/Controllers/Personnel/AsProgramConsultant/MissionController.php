<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant;

use Query\Application\Service\Firm\Program\ViewMission;
use Query\Domain\Model\Firm\Program\Mission;

class MissionController extends AsProgramConsultantBaseController
{

    public function showAll($programId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        $missions = $this->buildViewService()->showAll($this->firmId(), $programId, $this->getPage(),
                $this->getPageSize());

        $result = [];
        $result["total"] = count($missions);
        foreach ($missions as $mission) {
            $result["list"][] = [
                "id" => $mission->getId(),
                "name" => $mission->getName(),
                "position" => $mission->getPosition(),
                "worksheetForm" => [
                    "id" => $mission->getWorksheetForm()->getId(),
                    "name" => $mission->getWorksheetForm()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show($programId, $missionId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        $mission = $this->buildViewService()->showById($this->firmId(), $programId, $missionId);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission));
    }

    protected function arrayDataOfMission(Mission $mission): array
    {
        return [
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
            "position" => $mission->getPosition(),
            "worksheetForm" => [
                "id" => $mission->getWorksheetForm()->getId(),
                "name" => $mission->getWorksheetForm()->getName(),
            ],
            "parent" => $this->arrayDataOfParentMission($mission->getParent()),
        ];
    }

    protected function arrayDataOfParentMission(?Mission $parentMission): ?array
    {
        return isset($parentMission) ? [
            "id" => $parentMission->getId(),
            "name" => $parentMission->getName(),
        ] : null;
    }

    protected function buildViewService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        return new ViewMission($missionRepository);
    }

}
