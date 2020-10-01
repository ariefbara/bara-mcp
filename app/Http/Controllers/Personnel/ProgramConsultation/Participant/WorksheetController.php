<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation\Participant;

use App\Http\Controllers\ {
    FormRecordToArrayDataConverter,
    Personnel\ProgramConsultation\ProgramConsultationBaseController
};
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultation\Participant\ViewWorksheet,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Service\Firm\Program\Participant\WorksheetFinder
};

class WorksheetController extends ProgramConsultationBaseController
{

    public function show($programConsultationId, $participantId, $worksheetId)
    {
        $service = $this->buildViewService();
        $worksheet = $service->showById(
                $this->firmId(), $this->personnelId(), $programConsultationId, $participantId, $worksheetId);
        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function showBranches($programConsultationId, $participantId, $worksheetId)
    {
        $service = $this->buildViewService();
        $worksheets = $service->showAllBranchesOfAWorksheet(
                $this->firmId(), $this->personnelId(), $programConsultationId, $participantId, $worksheetId,
                $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($worksheets);
        foreach ($worksheets as $worksheet) {
            $result["list"][] = $this->arrayDataOfWorksheetForList($worksheet);
        }
        return $this->listQueryResponse($result);
    }

    public function showAll($programConsultationId, $participantId)
    {
        $service = $this->buildViewService();
        $worksheets = $service->showAll(
                $this->firmId(), $this->personnelId(), $programConsultationId, $participantId, $this->getPage(),
                $this->getPageSize());

        $result = [];
        $result["total"] = count($worksheets);
        foreach ($worksheets as $worksheet) {
            $result["list"][] = $this->arrayDataOfWorksheetForList($worksheet);
        }
        return $this->listQueryResponse($result);
    }

    public function showAllRoots($programConsultationId, $participantId)
    {
        $service = $this->buildViewService();
        $worksheets = $service->showAllRoot(
                $this->firmId(), $this->personnelId(), $programConsultationId, $participantId, $this->getPage(),
                $this->getPageSize());

        $result = [];
        $result["total"] = count($worksheets);
        foreach ($worksheets as $worksheet) {
            $result["list"][] = $this->arrayDataOfWorksheetForList($worksheet);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfWorksheetForList(Worksheet $worksheet): array
    {
        $parent = empty($worksheet->getParent()) ? null : [
            "id" => $worksheet->getParent()->getId(),
            "name" => $worksheet->getParent()->getName(),
        ];
        return [
            "id" => $worksheet->getId(),
            "name" => $worksheet->getName(),
            "mission" => [
                "id" => $worksheet->getMission()->getId(),
                "name" => $worksheet->getMission()->getName(),
            ],
            "parent" => $parent,
        ];
    }

    protected function arrayDataOfWorksheet(Worksheet $worksheet): array
    {
        $data = (new FormRecordToArrayDataConverter())->convert($worksheet);
        $parent = empty($worksheet->getParent()) ? null : $this->arrayDataOfParentWorksheet($worksheet->getParent());
        $data['id'] = $worksheet->getId();
        $data['parent'] = $parent;
        $data['name'] = $worksheet->getName();
        $data['mission'] = [
            "id" => $worksheet->getMission()->getId(),
            "name" => $worksheet->getMission()->getName(),
            "description" => $worksheet->getMission()->getDescription(),
        ];
        return $data;
    }

    protected function arrayDataOfParentWorksheet(Worksheet $parentWorksheet): array
    {
        $parent = empty($parentWorksheet->getParent()) ? null :
                $this->arrayDataOfParentWorksheet($parentWorksheet->getParent());
        return [
            'id' => $parentWorksheet->getId(),
            'name' => $parentWorksheet->getName(),
            "parent" => $parent,
        ];
    }

    protected function buildViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $worksheetFinder = new WorksheetFinder($worksheetRepository);
        return new ViewWorksheet($this->programConsultationRepository(), $worksheetFinder);
    }

}
