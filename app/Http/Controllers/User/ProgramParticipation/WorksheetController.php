<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use App\Http\Controllers\User\UserBaseController;
use Participant\Application\Service\UserParticipant\RemoveWorksheet;
use Participant\Application\Service\UserParticipant\SubmitBranchWorksheet;
use Participant\Application\Service\UserParticipant\SubmitRootWorksheet;
use Participant\Application\Service\UserParticipant\UpdateWorksheet;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\Model\Participant\Worksheet as Worksheet2;
use Participant\Domain\Model\UserParticipant;
use Participant\Domain\Service\UserFileInfoFinder;
use Query\Application\Service\User\ProgramParticipation\ViewWorksheet;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Infrastructure\QueryFilter\WorksheetFilter;
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
        $worksheet = $viewService->showById($this->userId(), $worksheetId);
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
        $worksheet = $viewService->showById($this->userId(), $branchId);
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
        $worksheet = $service->showById($this->userId(), $worksheetId);
        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();
        $worksheetFilter = (new WorksheetFilter())
                ->setMissionId($this->stripTagQueryRequest("missionId"))
                ->setParentId($this->stripTagQueryRequest("parentId"))
                ->setHasParent($this->filterBooleanOfQueryRequest("hasParent"));
        
        $worksheets = $service->showAll(
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize(), $worksheetFilter);

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
                    "position" => $worksheet->getMission()->getPosition(),
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
            "position" => $worksheet->getMission()->getPosition(),
        ];
        $data['mission']['worksheetForm'] = (new FormToArrayDataConverter())->convert($worksheet->getMission()->getWorksheetForm());
        $data['mission']['worksheetForm']['id'] = $worksheet->getMission()->getWorksheetForm()->getId();
        foreach ($worksheet->getActiveChildren() as $childWorksheet) {
            $data["children"][] = $this->arrayDataOfChildWorksheet($childWorksheet);
        }
        
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
    protected function arrayDataOfChildWorksheet(Worksheet $childWorksheet): array
    {
        return [
            "id" => $childWorksheet->getId(),
            "name" => $childWorksheet->getName(),
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
