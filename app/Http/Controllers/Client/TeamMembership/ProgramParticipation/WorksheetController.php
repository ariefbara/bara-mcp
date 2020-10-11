<?php

namespace App\Http\Controllers\Client\TeamMembership\ProgramParticipation;

use App\Http\Controllers\{
    Client\TeamMembership\TeamProgramParticipationBaseController,
    FormRecordDataBuilder,
    FormRecordToArrayDataConverter
};
use Participant\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\SubmitBranchWorksheet,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\SubmitRootWorksheet,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\UpdateWorksheet,
    Application\Service\Firm\Client\TeamMembershipRepository,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\DependencyModel\Firm\Program\Mission,
    Domain\Model\Participant,
    Domain\Model\Participant\Worksheet as Worksheet2,
    Domain\Model\TeamProgramParticipation,
    Domain\Service\TeamFileInfoFinder
};
use Query\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\ViewWorksheet,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Service\Firm\Program\Participant\WorksheetFinder
};
use SharedContext\Domain\Model\SharedEntity\FileInfo;

class WorksheetController extends TeamProgramParticipationBaseController
{

    public function submitRoot($teamMembershipId, $teamProgramParticipationId)
    {
        $service = $this->buildSubmitRootService();
        $missionId = $this->stripTagsInputRequest("missionId");
        $name = $this->stripTagsInputRequest("name");
        $worksheetId = $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $missionId, $name,
                $this->getFormRecordData($teamMembershipId));

        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function submitBranch($teamMembershipId, $teamProgramParticipationId, $worksheetId)
    {
        $service = $this->buildSubmitBranchService();
        $missionId = $this->stripTagsInputRequest("missionId");
        $name = $this->stripTagsInputRequest("name");
        $branchId = $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $worksheetId, $missionId, $name,
                $this->getFormRecordData($teamMembershipId));

        $viewService = $this->buildViewService();
        $branch = $viewService->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $branchId);
        return $this->commandCreatedResponse($this->arrayDataOfWorksheet($branch));
    }

    public function update($teamMembershipId, $teamProgramParticipationId, $worksheetId)
    {
        $service = $this->buildUpdateService();
        $name = $this->stripTagsInputRequest("name");
        $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $worksheetId, $name,
                $this->getFormRecordData($teamMembershipId));

        return $this->show($teamMembershipId, $teamProgramParticipationId, $worksheetId);
    }

    public function show($teamMembershipId, $teamProgramParticipationId, $worksheetId)
    {
        $viewService = $this->buildViewService();
        $worksheet = $viewService->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId);

        return $this->singleQueryResponse($this->arrayDataOfWorksheet($worksheet));
    }

    public function showAll($teamMembershipId, $teamProgramParticipationId)
    {
        $service = $this->buildViewService();
        $worksheets = $service->showAll(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $this->getPage(),
                $this->getPageSize());

        $result = [];
        $result["total"] = count($worksheets);
        foreach ($worksheets as $worksheet) {
            $result["list"][] = $this->arrayDataOfWorksheetForList($worksheet);
        }
        return $this->listQueryResponse($result);
    }

    public function showAllRoots($teamMembershipId, $teamProgramParticipationId)
    {
        $service = $this->buildViewService();
        $worksheets = $service->showAllRoots(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $this->getPage(),
                $this->getPageSize());

        $result = [];
        $result["total"] = count($worksheets);
        foreach ($worksheets as $worksheet) {
            $result["list"][] = $this->arrayDataOfWorksheetForList($worksheet);
        }
        return $this->listQueryResponse($result);
    }

    public function showAllBranches($teamMembershipId, $teamProgramParticipationId, $worksheetId)
    {
        $service = $this->buildViewService();
        $worksheets = $service->showAllBranches(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId,
                $this->getPage(), $this->getPageSize());

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

    protected function buildViewService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $worksheetFinder = new WorksheetFinder($worksheetRepository);
        return new ViewWorksheet($this->teamMembershipRepository(), $this->teamProgramParticipationFinder(),
                $worksheetFinder);
    }

    protected function getFormRecordData(string $teamMembershipId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new TeamFileInfoFinder($fileInfoRepository, $this->firmId(), $this->clientId(),
                $teamMembershipId);
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
        $missionRepository = $this->em->getRepository(Mission::class);

        return new SubmitBranchWorksheet($worksheetRepository, $teamMembershipRepository, $missionRepository);
    }

    protected function buildUpdateService()
    {
        $worksheetRepository = $this->em->getRepository(Worksheet2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);

        return new UpdateWorksheet($worksheetRepository, $teamMembershipRepository);
    }

}
