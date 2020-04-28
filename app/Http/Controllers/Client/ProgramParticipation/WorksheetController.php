<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\ {
    Client\ClientBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter
};
use Client\ {
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\WorksheetAddBranch,
    Application\Service\Client\ProgramParticipation\WorksheetAddRoot,
    Application\Service\Client\ProgramParticipation\WorksheetRemove,
    Application\Service\Client\ProgramParticipation\WorksheetUpdate,
    Application\Service\Client\ProgramParticipation\WorksheetView,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ProgramParticipationFileInfo,
    Domain\Model\Client\ProgramParticipation\Worksheet,
    Domain\Model\Firm\Program\Mission,
    Domain\Service\ProgramParticipationFileInfoFinder
};

class WorksheetController extends ClientBaseController
{

    public function addRoot($programParticipationId)
    {
        $service = $this->buildAddRootService();
        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $worksheet = $service->execute(
                $this->clientId(), $programParticipationId, $missionId, $name,
                $this->getFormRecordData($programParticipationId));

        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function addBranch($programParticipationId, $worksheetId)
    {
        $service = $this->buildAddBranchService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId(),
                $programParticipationId);
        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $worksheet = $service->execute(
                $programParticipationCompositionId, $worksheetId, $missionId, $name,
                $this->getFormRecordData($programParticipationId));

        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function update($programParticipationId, $worksheetId)
    {
        $service = $this->buildUpdateService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId(),
                $programParticipationId);
        $name = $this->stripTagsInputRequest('name');
        $worksheet = $service->execute(
                $programParticipationCompositionId, $worksheetId, $name,
                $this->getFormRecordData($programParticipationId));

        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function remove($programParticipationId, $worksheetId)
    {
        $service = $this->buildRemoveService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId(),
                $programParticipationId);
        $service->execute($programParticipationCompositionId, $worksheetId);

        return $this->commandOkResponse();
    }

    public function show($programParticipationId, $worksheetId)
    {
        $service = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId(),
                $programParticipationId);
        $worksheet = $service->showById($programParticipationCompositionId, $worksheetId);
        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId(),
                $programParticipationId);
        $worksheets = $service->showAll($programParticipationCompositionId, $this->getPage(), $this->getPageSize());
        return $this->commonIdNameListQueryResponse($worksheets);
    }

    protected function arrayDataOfWorksheet(Worksheet $worksheet): array
    {
        $data = (new FormRecordToArrayDataConverter())->convert($worksheet);
        $parent = empty($worksheet->getParent()) ? null :
                [
            "id" => $worksheet->getParent()->getId(),
            "name" => $worksheet->getParent()->getName(),
        ];
        $data['id'] = $worksheet->getId();
        $data['parent'] = $parent;
        $data['name'] = $worksheet->getName();
        $data['mission'] = [
            "id" => $worksheet->getMission()->getId(),
            "name" => $worksheet->getMission()->getName(),
            "worksheetForm" => [
                "id" => $worksheet->getMission()->getWorksheetForm()->getId(),
            ],
        ];
        return $data;
    }

    protected function buildAddRootService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        $missionRepository = $this->em->getRepository(Mission::class);

        return new WorksheetAddRoot($worksheetRepository, $programParticipationRepository, $missionRepository);
    }

    protected function buildAddBranchService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        return new WorksheetAddBranch($worksheetRepository, $missionRepository);
    }

    protected function buildUpdateService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new WorksheetUpdate($worksheetRepository);
    }

    protected function buildRemoveService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new WorksheetRemove($worksheetRepository);
    }

    protected function buildViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new WorksheetView($worksheetRepository);
    }

    protected function getFormRecordData($programParticipationId)
    {
        $programParticipationFileInfoRepository = $this->em->getRepository(ProgramParticipationFileInfo::class);
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $fileInfoFinder = new ProgramParticipationFileInfoFinder(
                $programParticipationFileInfoRepository, $programParticipationCompositionId);
        return (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
    }

}
