<?php

namespace App\Http\Controllers\Manager;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program\Mission;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial as LearningMaterial2;
use Firm\Domain\Task\InFirm\AddLearningMaterialPayload;
use Firm\Domain\Task\InFirm\AddLearningMaterialTask;
use Firm\Domain\Task\InFirm\LearningMaterialRequest;
use Firm\Domain\Task\InFirm\RemoveLearningMaterialTask;
use Firm\Domain\Task\InFirm\UpdateLearningMaterialPayload;
use Firm\Domain\Task\InFirm\UpdateLearningMaterialTask;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Query\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialFilter;
use Query\Domain\Task\InFirm\ViewAllLearningMaterialTask;
use Query\Domain\Task\InFirm\ViewLearningMaterialTask;
use Query\Domain\Task\PaginationPayload;

class LearningMaterialController extends ManagerBaseController
{

    protected function getLearningMaterialRequest()
    {
        $name = $this->request->input('name');
        $content = $this->request->input('content');
        $request = new LearningMaterialRequest($name, $content);
        $attachmentFieldList = $this->request->input('attachmentFileIdList');
        if (is_array($attachmentFieldList)) {
            foreach ($this->request->input('attachmentFileIdList') as $firmFileInfoId) {
                $request->attachFirmFileInfoId($firmFileInfoId);
            }
        }
        return $request;
    }

    public function add($missionId)
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial2::class);
        $missionRepository = $this->em->getRepository(Mission::class);
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        $payload = new AddLearningMaterialPayload($missionId, $this->getLearningMaterialRequest());

        $task = new AddLearningMaterialTask($learningMaterialRepository, $missionRepository, $firmFileInfoRepository,
                $payload);
        $this->executeFirmTaskExecutableByManager($task);
        
        
        $learningMaterialQueryRepository = $this->em->getRepository(LearningMaterial::class);
        $queryTask = new ViewLearningMaterialTask($learningMaterialQueryRepository, $task->addedLearningMaterialId);
        $this->executeFirmQueryTask($queryTask);
        return $this->commandCreatedResponse($this->arrayDataOfLearningMaterial($queryTask->result));
    }

    public function update($id)
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial2::class);
        $firmFileInfoRepository = $this->em->getRepository(FirmFileInfo::class);
        $payload = new UpdateLearningMaterialPayload($id, $this->getLearningMaterialRequest());

        $task = new UpdateLearningMaterialTask($learningMaterialRepository, $firmFileInfoRepository, $payload);
        $this->executeFirmTaskExecutableByManager($task);

        return $this->show($id);
    }

    public function remove($id)
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial2::class);
        $task = new RemoveLearningMaterialTask($learningMaterialRepository, $id);
        $this->executeFirmTaskExecutableByManager($task);

        return $this->show($id);
    }

    public function show($id)
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        $task = new ViewLearningMaterialTask($learningMaterialRepository, $id);
        $this->executeFirmQueryTask($task);

        return $this->singleQueryResponse($this->arrayDataOfLearningMaterial($task->result));
    }

    public function showAll()
    {
        $learningMaterialRepository = $this->em->getRepository(LearningMaterial::class);
        $pagination = new PaginationPayload($this->getPage(), $this->getPageSize());
        $filter = new LearningMaterialFilter($pagination);
        $filter->setMissionId($this->request->query('missionId'));
        $task = new ViewAllLearningMaterialTask($learningMaterialRepository, $filter);
        $this->executeFirmQueryTask($task);

        $result = [];
        $result['total'] = count($task->result);
        foreach ($task->result as $learningMaterial) {
            $result['list'][] = [
                'id' => $learningMaterial->getId(),
                'name' => $learningMaterial->getName(),
                'removed' => $learningMaterial->isRemoved(),
            ];
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfLearningMaterial(LearningMaterial $learningMaterial): array
    {
        $learningAttachments = [];
        foreach ($learningMaterial->iterateAllActiveLearningAttachments() as $learningAttachment) {
            $learningAttachments[] = [
                'id' => $learningAttachment->getId(),
                'disabled' => $learningAttachment->isDisabled(),
                'firmFileInfo' => [
                    'id' => $learningAttachment->getFirmFileInfo()->getId(),
                    'path' => $learningAttachment->getFirmFileInfo()->getFullyQualifiedFileName($this->createGoogleStorage()),
                    'contentType' => $learningAttachment->getFirmFileInfo()->getFileInfo()->getContentType(),
                ],
            ];
        }
        return [
            'id' => $learningMaterial->getId(),
            'name' => $learningMaterial->getName(),
            'content' => $learningMaterial->getContent(),
            'removed' => $learningMaterial->isRemoved(),
            'learningAttachments' => $learningAttachments,
        ];
    }

}
