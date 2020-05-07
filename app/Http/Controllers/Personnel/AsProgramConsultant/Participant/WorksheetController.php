<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant\Participant;

use App\Http\Controllers\ {
    FormRecordToArrayDataConverter,
    Personnel\AsProgramConsultant\AsProgramConsultantBaseController
};
use Query\ {
    Application\Service\Firm\Program\Participant\ParticipantCompositionId,
    Application\Service\Firm\Program\Participant\WorksheetView,
    Domain\Model\Firm\Program\Mission,
    Domain\Model\Firm\Program\Participant\Worksheet
};

class WorksheetController extends AsProgramConsultantBaseController
{
    public function show($programId, $participantId, $worksheetId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $participantCompositionId = new ParticipantCompositionId($this->firmId(), $programId, $participantId);
        $worksheet = $service->showById($participantCompositionId, $worksheetId);
        
        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }
    public function showAll($programId, $participantId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        $service = $this->buildViewService();
        $participantCompositionId = new ParticipantCompositionId($this->firmId(), $programId, $participantId);
        
        $worksheets = $service->showAll($participantCompositionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($worksheets);
        foreach ($worksheets as $worksheet) {
            $result['list'][] = [
                "id" => $worksheet->getId(),
                "name" => $worksheet->getName(),
                "mission" => $this->arrayDataOfMission($worksheet->getMission()),
                "parent" => $this->arrayDataOfParentWorksheet($worksheet->getParent()),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfWorksheet(Worksheet $worksheet): array
    {
        $data = (new FormRecordToArrayDataConverter())->convert($worksheet);
        $data['id'] = $worksheet->getId();
        $data['name'] = $worksheet->getName();
        $data['mission'] = $this->arrayDataOfMission($worksheet->getMission());
        $data['parent'] = $this->arrayDataOfParentWorksheet($worksheet->getParent());
        return $data;
    }
    protected function arrayDataOfMission(Mission $mission): array
    {
        return [
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
        ];
    }
    protected function arrayDataOfParentWorksheet(?Worksheet $worksheet): ?array
    {
        if (empty($worksheet)) {
            return null;
        }
        return [
            "id" => $worksheet->getId(),
            "name" => $worksheet->getName(),
        ];
    }
    protected function buildViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new WorksheetView($worksheetRepository);
    }
}
