<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterialData;
use Firm\Domain\Task\Dependency\Firm\FirmFileInfoRepository;
use Firm\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository;
use Firm\Domain\Task\Dependency\Firm\Program\MissionRepository;

class AddLearningMaterialTask implements FirmTaskExecutableByManager
{

    /**
     * 
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;

    /**
     * 
     * @var MissionRepository
     */
    protected $missionRepository;

    /**
     * 
     * @var FirmFileInfoRepository
     */
    protected $firmFileInfoRepository;

    /**
     * 
     * @var AddLearningMaterialPayload
     */
    protected $payload;

    /**
     * 
     * @var string|null
     */
    public $addedLearningMaterialId;

    public function __construct(
            LearningMaterialRepository $learningMaterialRepository, MissionRepository $missionRepository,
            FirmFileInfoRepository $firmFileInfoRepository, AddLearningMaterialPayload $payload)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
        $this->missionRepository = $missionRepository;
        $this->firmFileInfoRepository = $firmFileInfoRepository;
        $this->payload = $payload;
    }

    public function executeInFirm(Firm $firm): void
    {
        $this->addedLearningMaterialId = $this->learningMaterialRepository->nextIdentity();
        
        $name = $this->payload->getLearningMaterialRequest()->getName();
        $content = $this->payload->getLearningMaterialRequest()->getContent();
        $learningMaterialData = new LearningMaterialData($name, $content);
        foreach ($this->payload->getLearningMaterialRequest()->getAttachedFirmFileInfoIdList() as $firmFileInfoId) {
            $firmFileInfo = $this->firmFileInfoRepository->ofId($firmFileInfoId);
            $firmFileInfo->assertUsableInFirm($firm);
            $learningMaterialData->addAttachment($firmFileInfo);
        }
        
        $mission = $this->missionRepository->aMissionOfId($this->payload->getMissionId());
        $mission->assertAccessibleInFirm($firm);
        $learningMaterial = $mission->addLearningMaterial($this->addedLearningMaterialId, $learningMaterialData);
        $this->learningMaterialRepository->add($learningMaterial);
    }

}
