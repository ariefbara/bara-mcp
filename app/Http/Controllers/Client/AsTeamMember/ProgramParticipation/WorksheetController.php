<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\FormToArrayDataConverter;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\SubmitBranchWorksheet;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\SubmitRootWorksheet;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\UpdateWorksheet;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\Mission;
use Participant\Domain\Model\Participant\Worksheet as Worksheet2;
use Participant\Domain\Model\TeamProgramParticipation;
use Participant\Domain\Service\TeamFileInfoFinder;
use Query\Application\Service\Firm\Team\ProgramParticipation\ViewWorksheetRepository;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Infrastructure\QueryFilter\WorksheetFilter;
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class WorksheetController extends AsTeamMemberBaseController
{

    public function submitRoot($teamId, $teamProgramParticipationId)
    {
        $service = $this->buildSubmitRootService();
        $missionId = $this->stripTagsInputRequest("missionId");
        $name = $this->stripTagsInputRequest("name");
        $worksheetId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $missionId, $name,
                $this->getFormRecordData($teamId));

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($teamId, $worksheetId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function submitBranch($teamId, $teamProgramParticipationId, $worksheetId)
    {
        $service = $this->buildSubmitBranchService();
        $missionId = $this->stripTagsInputRequest("missionId");
        $name = $this->stripTagsInputRequest("name");
        $branchId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $worksheetId, $missionId,
                $name, $this->getFormRecordData($teamId));

        $viewService = $this->buildViewService();
        $branch = $viewService->showById($teamId, $branchId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($branch));
    }

    public function update($teamId, $teamProgramParticipationId, $worksheetId)
    {
        $service = $this->buildUpdateService();
        $name = $this->stripTagsInputRequest("name");
        $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $worksheetId, $name, $this->getFormRecordData($teamId));

        return $this->show($teamId, $teamProgramParticipationId, $worksheetId);
    }

    public function show($teamId, $teamProgramParticipationId, $worksheetId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById($teamId, $worksheetId);

        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $worksheetFilter = (new WorksheetFilter())
                ->setHasParent($this->filterBooleanOfQueryRequest("hasParent"))
                ->setMissionId($this->stripTagQueryRequest("missionId"))
                ->setParentId($this->stripTagQueryRequest("parentId"));

        $worksheets = $service->showAll(
                $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize(), $worksheetFilter);

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
                "position" => $worksheet->getMission()->getPosition(),
            ],
            "parent" => $parent,
        ];
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

    protected function buildViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new ViewWorksheetRepository($worksheetRepository);
    }

    protected function getFormRecordData(string $teamId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new TeamFileInfoFinder($fileInfoRepository, $teamId);
        return (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
    }

    protected function buildSubmitRootService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $missionRepository = $this->em->getRepository(Mission::class);

        return new SubmitRootWorksheet(
                $worksheetRepository, $teamMembershipRepository, $teamProgramParticipationRepository, $missionRepository);
    }

    protected function buildSubmitBranchService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $teamProgramParticipationRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $missionRepository = $this->em->getRepository(Mission::class);

        return new SubmitBranchWorksheet(
                $worksheetRepository, $teamMembershipRepository, $teamProgramParticipationRepository, $missionRepository);
    }

    protected function buildUpdateService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);

        return new UpdateWorksheet($worksheetRepository, $teamMembershipRepository);
    }

}
