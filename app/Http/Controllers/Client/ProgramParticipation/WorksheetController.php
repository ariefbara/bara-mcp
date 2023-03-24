<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Participant\Application\Service\ClientParticipant\WorksheetAddBranch;
use Participant\Application\Service\ClientParticipant\WorksheetAddRoot;
use Participant\Application\Service\ClientParticipant\WorksheetRemove;
use Participant\Application\Service\ClientParticipant\WorksheetUpdate;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Model\Participant\Worksheet as Worksheet2;
use Participant\Domain\Service\ClientFileInfoFinder;
use Query\Application\Service\Firm\Client\ProgramParticipation\ViewWorksheet;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Infrastructure\QueryFilter\WorksheetFilter;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class WorksheetController extends ClientBaseController
{

    public function addRoot($programParticipationId)
    {
        $service = $this->buildAddRootService();

        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $worksheetId = $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $missionId, $name,
                $formRecordData);

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($this->clientId(), $worksheetId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function addBranch($programParticipationId, $worksheetId)
    {
        $service = $this->buildAddBranchService();

        $missionId = $this->stripTagsInputRequest('missionId');
        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $branchId = $service->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $worksheetId, $missionId, $name,
                $formRecordData);

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($this->clientId(), $branchId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function update($programParticipationId, $worksheetId)
    {
        $service = $this->buildUpdateService();

        $name = $this->stripTagsInputRequest('name');
        $formRecordData = $this->getFormRecordData();

        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $worksheetId, $name,
                $formRecordData);

        return $this->show($programParticipationId, $worksheetId);
    }

    public function remove($programParticipationId, $worksheetId)
    {
        $service = $this->buildRemoveService();
        $service->execute($this->firmId(), $this->clientId(), $programParticipationId, $worksheetId);
        return $this->commandOkResponse();
    }

    public function show($programParticipationId, $worksheetId)
    {
        $service = $this->buildViewService();
        $worksheet = $service->showById($this->clientId(), $worksheetId);
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
                $this->clientId(), $programParticipationId, $this->getPage(), $this->getPageSize(), $worksheetFilter);

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
        if ($worksheet->getFormRecord()) {
            $data = (new FormRecordToArrayDataConverter())->convert($worksheet);
        } else {
            $data = [];
        }
        $parent = empty($worksheet->getParent()) ? null : $this->arrayDataOfParentWorksheet($worksheet->getParent());
        $data['id'] = $worksheet->getId();
        $data['parent'] = $parent;
        $data['name'] = $worksheet->getName();
        $data['mission'] = [
            "id" => $worksheet->getMission()->getId(),
            "name" => $worksheet->getMission()->getName(),
            "position" => $worksheet->getMission()->getPosition(),
        ];
        if ($worksheet->getMission()->getWorksheetForm()) {
            $data['mission']['worksheetForm'] = (new FormToArrayDataConverter())->convert($worksheet->getMission()->getWorksheetForm());
            $data['mission']['worksheetForm']['id'] = $worksheet->getMission()->getWorksheetForm()->getId();
        } else {
            $data['mission']['worksheetForm'] = null;
        }
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
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $missionRepository = $this->em->getRepository(Mission::class);

        return new WorksheetAddRoot($worksheetRepository, $clientParticipantRepository, $missionRepository);
    }

    protected function buildAddBranchService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $missionRepository = $this->em->getRepository(Mission::class);

        return new WorksheetAddBranch($worksheetRepository, $clientParticipantRepository, $missionRepository);
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
