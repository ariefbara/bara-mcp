<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\{
    Client\ClientBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter
};
use Participant\{
    Application\Service\Participant\WorksheetAddBranch,
    Application\Service\Participant\WorksheetAddRoot,
    Application\Service\Participant\WorksheetRemove,
    Application\Service\Participant\WorksheetUpdate,
    Domain\Model\ClientParticipant,
    Domain\Model\DependencyEntity\Firm\Program\Mission,
    Domain\Model\Participant\Worksheet as Worksheet2,
    Domain\Service\ClientFileInfoFinder
};
use Query\{
    Application\Service\Firm\Program\ClientParticipant\ViewWorksheet,
    Domain\Model\Firm\Program\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class WorksheetController extends ClientBaseController
{

    public function addRoot($programId)
    {
        $service = $this->buildAddRootService();

        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $worksheetId = $service->execute($this->firmId(), $this->clientId(), $programId, $missionId, $name,
                $formRecordData);

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($this->firmId(), $this->clientId(), $programId, $worksheetId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function addBranch($programId, $worksheetId)
    {
        $service = $this->buildAddBranchService();

        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $branchId = $service->execute(
                $this->firmId(), $this->clientId(), $programId, $worksheetId, $missionId, $name, $formRecordData);

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($this->firmId(), $this->clientId(), $programId, $branchId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function update($programId, $worksheetId)
    {
        $service = $this->buildUpdateService();

        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $service->execute($this->firmId(), $this->clientId(), $programId, $worksheetId, $name, $formRecordData);

        return $this->show($programId, $worksheetId);
    }

    public function remove($programId, $worksheetId)
    {
        $service = $this->buildRemoveService();
        $service->execute($this->firmId(), $this->clientId(), $programId, $worksheetId);
        return $this->commandOkResponse();
    }

    public function show($programId, $worksheetId)
    {
        $service = $this->buildViewService();
        $worksheet = $service->showById($this->firmId(), $this->clientId(), $programId, $worksheetId);
        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        
        $missionId = $this->stripTagQueryRequest('missionId');
        $parentWorksheetId = $this->stripTagQueryRequest('parentWorksheetId');
        
        $worksheets = $service->showAll(
                $this->firmId(), $this->clientId(), $programId, $this->getPage(), $this->getPageSize(), $missionId,
                $parentWorksheetId);

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
                ],
                "parent" => $parent,
            ];
        }
        return $this->listQueryResponse($result);
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
            "worksheetForm" => [
                "id" => $worksheet->getMission()->getWorksheetForm()->getId(),
                "name" => $worksheet->getMission()->getWorksheetForm()->getName(),
                "description" => $worksheet->getMission()->getWorksheetForm()->getDescription(),
            ],
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

    protected function buildAddRootService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $missionRepository = $this->em->getRepository(Mission::class);

        return new WorksheetAddRoot($worksheetRepository, $clientParticipantRepository, $missionRepository);
    }

    protected function buildAddBranchService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        $missionRepository = $this->em->getRepository(Mission::class);

        return new WorksheetAddBranch($worksheetRepository, $missionRepository);
    }

    protected function buildUpdateService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        return new WorksheetUpdate($worksheetRepository);
    }

    protected function buildRemoveService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        return new WorksheetRemove($worksheetRepository);
    }

    protected function buildViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new ViewWorksheet($worksheetRepository);
    }

    protected function getFormRecordData()
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new ClientFileInfoFinder($fileInfoRepository, $this->firmId(), $this->clientId());
        return (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
    }

}
