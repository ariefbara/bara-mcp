<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\Application\Service\Manager\ChangeMissionWorksheetForm;
use Firm\Application\Service\Manager\CreateBranchMission;
use Firm\Application\Service\Manager\CreateRootMission;
use Firm\Application\Service\Manager\PublishMission;
use Firm\Application\Service\Manager\UpdateMission;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\WorksheetForm;
use Firm\Domain\Task\InFirm\Program\Mission\AssignWorksheetFormToMission;
use Firm\Domain\Task\InFirm\Program\Mission\AssignWorksheetFormToMissionPayload;
use Firm\Domain\Task\InFirm\Program\Mission\CreateBranchMission as CreateBranchMission2;
use Firm\Domain\Task\InFirm\Program\Mission\CreateBranchMissionPayload;
use Firm\Domain\Task\InFirm\Program\Mission\CreateRootMission as CreateRootMission2;
use Firm\Domain\Task\InFirm\Program\Mission\CreateRootMissionPayload;
use Query\Application\Service\Firm\Program\Mission\ViewLearningMaterial;
use Query\Application\Service\Firm\Program\ViewMission;
use Query\Domain\Model\Firm\Program\Mission as Mission2;
use Resources\Infrastructure\Persistence\Doctrine\DoctrineTransactionalSession;

class MissionController extends ManagerBaseController
{

    protected function repository()
    {
        return $this->em->getRepository(Mission::class);
    }

    protected function getMissionData()
    {
        $name = $this->stripTagsInputRequest('name');
        $description = $this->stripTagsInputRequest('description');
        $position = $this->stripTagsInputRequest('position');
        return new Program\MissionData($name, $description, $position);
    }

    public function addRoot($programId)
    {
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $missionData = $this->getMissionData();
        $payload = (new CreateRootMissionPayload())
                ->setMissionData($missionData)
                ->setProgramId($programId);

        $transactionalSession->executeAtomically(function () use ($payload) {
            $programRepository = $this->em->getRepository(Program::class);
            $task = new CreateRootMission2($this->repository(), $programRepository);
            $this->executeTaskInFirm($task, $payload);

            $worksheetFormId = $this->stripTagsInputRequest('worksheetFormId');
            if ($worksheetFormId) {
                $this->executeAssignWorksheetFormTask($payload->getMissionData()->id, $worksheetFormId);
            }
            //
        });

        $mission = $this->buildViewService()->showById($this->firmId(), $programId, $missionData->id);
        return $this->commandCreatedResponse($this->arrayDataOfMission($mission, $programId));
    }

    public function addBranch($programId, $missionId)
    {
        $transactionalSession = new DoctrineTransactionalSession($this->em);
        $missionData = $this->getMissionData();
        $payload = (new CreateBranchMissionPayload())
                ->setMissionData($missionData)
                ->setParentMissionId($missionId);

        $transactionalSession->executeAtomically(function () use ($payload) {
            $task = new CreateBranchMission2($this->repository());
            $this->executeTaskInFirm($task, $payload);

            $worksheetFormId = $this->stripTagsInputRequest('worksheetFormId');
            if ($worksheetFormId) {
                $this->executeAssignWorksheetFormTask($payload->getMissionData()->id, $worksheetFormId);
            }
        });

        $mission = $this->buildViewService()->showById($this->firmId(), $programId, $missionData->id);
        return $this->commandCreatedResponse($this->arrayDataOfMission($mission, $programId));
    }

    public function update($programId, $missionId)
    {
        $this->buildUpdateService()->execute($this->firmId(), $this->managerId(), $missionId, $this->getMissionData());
        return $this->show($programId, $missionId);
    }

    public function assignWorksheetForm($programId, $missionId)
    {
        $worksheetFormId = $this->stripTagsInputRequest("worksheetFormId");
        $this->executeAssignWorksheetFormTask($missionId, $worksheetFormId);
        return $this->show($programId, $missionId);
    }

    protected function executeAssignWorksheetFormTask($missionId, $worksheetFormId)
    {
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        $task = new AssignWorksheetFormToMission($this->repository(), $worksheetFormRepository);

        $payload = (new AssignWorksheetFormToMissionPayload())
                ->setMissionId($missionId)
                ->setWorksheetFormId($worksheetFormId);
        $this->executeTaskInFirm($task, $payload);
    }

//    public function changeWorksheetForm($programId, $missionId)
//    {
//        $worksheetFormId = $this->stripTagsInputRequest("worksheetFormId");
//        $this->buildChangeChangeWorksheetFormService()
//                ->execute($this->firmId(), $this->managerId(), $missionId, $worksheetFormId);
//
//        return $this->show($programId, $missionId);
//    }

    public function publish($programId, $missionId)
    {
        $this->buildPublishService()->execute($this->firmId(), $this->managerId(), $missionId);
        return $this->show($programId, $missionId);
    }

    public function show($programId, $missionId)
    {
        $service = $this->buildViewService();
        $mission = $service->showById($this->firmId(), $programId, $missionId);
        return $this->singleQueryResponse($this->arrayDataOfMission($mission, $programId));
    }

    public function showAll($programId)
    {
        $service = $this->buildViewService();
        $missions = $service->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize(), false);

        $result = [];
        $result['total'] = count($missions);
        foreach ($missions as $mission) {
            $result['list'][] = $this->arrayDataOfMission($mission, $programId);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfMission(Mission2 $mission, string $programId): array
    {
        $learningMaterialRepository = $this->em->getRepository(Mission2\LearningMaterial::class);
        $learningMaterials = (new ViewLearningMaterial($learningMaterialRepository))
                ->showAll($this->firmId(), $programId, $mission->getId(), 1, 100);
        $learningMaterialResult = [];
        foreach ($learningMaterials as $learningMaterial) {
            $learningMaterialResult[] = $this->arrayDataOfLearningMaterial($learningMaterial);
        }

        $parentData = empty($mission->getParent()) ? null :
                [
            "id" => $mission->getParent()->getId(),
            "name" => $mission->getParent()->getName(),
        ];
        return [
            "parent" => $parentData,
            "id" => $mission->getId(),
            "name" => $mission->getName(),
            "description" => $mission->getDescription(),
            "position" => $mission->getPosition(),
            "published" => $mission->isPublished(),
            "worksheetForm" => $this->arrayDataOfWorksheetForm($mission->getWorksheetForm()),
            'learningMaterials' => $learningMaterialResult,
        ];
    }

    protected function arrayDataOfWorksheetForm(?\Query\Domain\Model\Firm\WorksheetForm $worksheetForm): ?array
    {
        return empty($worksheetForm) ? null : [
            "id" => $worksheetForm->getId(),
            "name" => $worksheetForm->getName(),
        ];
    }

    protected function arrayDataOfLearningMaterial(Mission2\LearningMaterial $learningMaterial): array
    {
        return [
            'id' => $learningMaterial->getId(),
            'name' => $learningMaterial->getName(),
        ];
    }

    protected function buildAddRootService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $programRepository = $this->em->getRepository(Program::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        return new CreateRootMission($managerRepository, $programRepository, $worksheetFormRepository,
                $missionRepository);
    }

    protected function buildAddBranchService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        return new CreateBranchMission($managerRepository, $missionRepository, $worksheetFormRepository);
    }

    protected function buildUpdateService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        return new UpdateMission($managerRepository, $missionRepository);
    }

    protected function buildPublishService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        return new PublishMission($managerRepository, $missionRepository);
    }

    protected function buildViewService()
    {
        $missionRepository = $this->em->getRepository(Mission2::class);
        return new ViewMission($missionRepository);
    }

    protected function buildChangeChangeWorksheetFormService()
    {
        $missionRepository = $this->em->getRepository(Mission::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        $worksheetFormRepository = $this->em->getRepository(WorksheetForm::class);
        return new ChangeMissionWorksheetForm($missionRepository, $managerRepository, $worksheetFormRepository);
    }

}
