<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\ {
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter,
    User\UserBaseController
};
use Participant\ {
    Application\Service\UserParticipant\RemoveWorksheet,
    Application\Service\UserParticipant\SubmitBranchWorksheet,
    Application\Service\UserParticipant\SubmitRootWorksheet,
    Application\Service\UserParticipant\UpdateWorksheet,
    Domain\DependencyModel\Firm\Program\Mission,
    Domain\Model\Participant\Worksheet as Worksheet2,
    Domain\Model\UserParticipant,
    Domain\Service\UserFileInfoFinder
};
use Query\ {
    Application\Service\User\ProgramParticipation\ViewWorksheet,
    Domain\Model\Firm\Program\Participant\Worksheet
};
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class WorksheetController extends UserBaseController
{

    public function addRoot($programParticipationId)
    {
        $service = $this->buildAddRootService();

        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $worksheetId = $service->execute($this->userId(), $programParticipationId, $missionId, $name,
                $formRecordData);

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($this->userId(), $programParticipationId, $worksheetId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function addBranch($programParticipationId, $worksheetId)
    {
        $service = $this->buildAddBranchService();

        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $branchId = $service->execute(
                $this->userId(), $programParticipationId, $worksheetId, $missionId, $name, $formRecordData);

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($this->userId(), $programParticipationId, $branchId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function update($programParticipationId, $worksheetId)
    {
        $service = $this->buildUpdateService();

        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $service->execute($this->userId(), $programParticipationId, $worksheetId, $name, $formRecordData);

        return $this->show($programParticipationId, $worksheetId);
    }

    public function remove($programParticipationId, $worksheetId)
    {
        $service = $this->buildRemoveService();
        $service->execute($this->userId(), $programParticipationId, $worksheetId);
        return $this->commandOkResponse();
    }

    public function show($programParticipationId, $worksheetId)
    {
        $service = $this->buildViewService();
        $worksheet = $service->showById($this->userId(), $programParticipationId, $worksheetId);
        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        
        $missionId = $this->stripTagQueryRequest('missionId');
        $parentWorksheetId = $this->stripTagQueryRequest('parentWorksheetId');
        
        $worksheets = $service->showAll(
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize(), $missionId,
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
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        
        return new SubmitRootWorksheet($worksheetRepository, $userParticipantRepository, $missionRepository);
    }

    protected function buildAddBranchService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        
        return new SubmitBranchWorksheet($worksheetRepository, $userParticipantRepository, $missionRepository);
    }

    protected function buildUpdateService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        return new UpdateWorksheet($worksheetRepository);
    }

    protected function buildRemoveService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        return new RemoveWorksheet($worksheetRepository);
    }

    protected function buildViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new ViewWorksheet($worksheetRepository);
    }

    protected function getFormRecordData()
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new UserFileInfoFinder($fileInfoRepository, $this->userId());
        return (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
    }

}
