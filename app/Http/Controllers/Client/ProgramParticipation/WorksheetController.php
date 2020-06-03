<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\{
    Client\ClientBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter
};
use Client\{
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\WorksheetAddBranch,
    Application\Service\Client\ProgramParticipation\WorksheetAddRoot,
    Application\Service\Client\ProgramParticipation\WorksheetRemove,
    Application\Service\Client\ProgramParticipation\WorksheetUpdate,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ParticipantFileInfo,
    Domain\Model\Client\ProgramParticipation\Worksheet,
    Domain\Model\Firm\Program\Mission,
    Domain\Service\ParticipantFileInfoFinder
};
use Query\{
    Application\Service\Client\ProgramParticipation\WorksheetView,
    Domain\Model\Firm\Program\Participant\Worksheet as Worksheet2
};

class WorksheetController extends ClientBaseController
{

    public function addRoot($programParticipationId)
    {
        $service = $this->buildAddRootService();
        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $worksheetId = $service->execute(
                $this->clientId(), $programParticipationId, $missionId, $name,
                $this->getFormRecordData($programParticipationId));

        $viewService = $this->buildViewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $worksheet = $viewService->showById($programParticipationCompositionId, $worksheetId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function addBranch($programParticipationId, $worksheetId)
    {
        $service = $this->buildAddBranchService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId(),
                $programParticipationId);
        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $branchId = $service->execute(
                $programParticipationCompositionId, $worksheetId, $missionId, $name,
                $this->getFormRecordData($programParticipationId));

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($programParticipationCompositionId, $branchId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function update($programParticipationId, $worksheetId)
    {
        $service = $this->buildUpdateService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId($this->clientId(),
                $programParticipationId);
        $name = $this->stripTagsInputRequest('name');
        $service->execute(
                $programParticipationCompositionId, $worksheetId, $name,
                $this->getFormRecordData($programParticipationId));

        return $this->show($programParticipationId, $worksheetId);
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

        $missionId = $this->stripTagsVariable($this->request->query('missionId'));
        $parentWorksheetId = $this->stripTagsVariable($this->request->query('parentWorksheetId'));
        $worksheets = $service->showAll(
                $programParticipationCompositionId, $this->getPage(), $this->getPageSize(), $missionId,
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

    protected function arrayDataOfWorksheet(Worksheet2 $worksheet): array
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

    protected function arrayDataOfParentWorksheet(Worksheet2 $parentWorksheet): array
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
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        return new WorksheetView($worksheetRepository);
    }

    protected function getFormRecordData($programParticipationId)
    {
        $participantFileInforRepository = $this->em->getRepository(ParticipantFileInfo::class);
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $fileInfoFinder = new ParticipantFileInfoFinder(
                $participantFileInforRepository, $programParticipationCompositionId);
        return (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
    }

}
