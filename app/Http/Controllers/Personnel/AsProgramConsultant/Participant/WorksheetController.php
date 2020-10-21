<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant\Participant;

use App\Http\Controllers\ {
    FormRecordToArrayDataConverter,
    Personnel\AsProgramConsultant\AsProgramConsultantBaseController
};
use Query\ {
    Application\Service\Firm\Program\Participant\ViewWorksheet,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Infrastructure\QueryFilter\WorksheetFilter
};

class WorksheetController extends AsProgramConsultantBaseController
{
    public function show($programId, $participantId, $worksheetId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $worksheet = $service->showById($programId, $worksheetId);
        
        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }
    
    public function showAll($programId, $participantId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);

        $service = $this->buildViewService();
        $worksheetFilter = (new WorksheetFilter())
                ->setMissionId($this->stripTagQueryRequest("missionId"))
                ->setParentId($this->stripTagQueryRequest("parentId"))
                ->setHasParent($this->filterBooleanOfQueryRequest("hasParent"));

        $worksheets = $service->showAll(
                $programId, $participantId, $this->getPage(), $this->getPageSize(), $worksheetFilter);

        $result = [];
        $result['total'] = count($worksheets);
        foreach ($worksheets as $worksheet) {
            $parent = empty($worksheet->getParent()) ? null : [
                "id" => $worksheet->getParent()->getId(),
                "name" => $worksheet->getParent()->getName(),
            ];
            $result['list'][] = [
                "id" => $worksheet->getId(),
                "name" => $worksheet->getName(),
                "mission" => [
                    "id" => $worksheet->getMission()->getId(),
                    "name" => $worksheet->getMission()->getName(),
                    "description" => $worksheet->getMission()->getDescription(),
                ],
                "parent" => $parent,
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfWorksheet(Worksheet $worksheet): array
    {
        $data = (new FormRecordToArrayDataConverter())->convert($worksheet);
        $data['id'] = $worksheet->getId();
        $data['name'] = $worksheet->getName();
        $data['parent'] = $this->arrayDataOfParentWorksheet($worksheet->getParent());
        $data['mission'] = $this->arrayDataOfMission($worksheet->getMission());
        return $data;
    }
    protected function arrayDataOfParentWorksheet(?Worksheet $parentWorksheet): ?array
    {
        return empty($parentWorksheet) ? null : [
            "id" => $parentWorksheet->getId(),
            "name" => $parentWorksheet->getName(),
            "parent" => $this->arrayDataOfParentWorksheet($parentWorksheet->getParent()),
        ];
    }
    protected function arrayDataOfMission(Mission $mission): array
    {
        return [
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
            "worksheetForm" => [
                "id" => $mission->getWorksheetForm()->getId(),
                "name" => $mission->getWorksheetForm()->getName(),
                "description" => $mission->getWorksheetForm()->getDescription(),
            ],
        ];
    }
    
    protected function buildViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new ViewWorksheet($worksheetRepository);
    }
}
