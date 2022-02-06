<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\FirmTaskExecutableByManager;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterialData;
use Firm\Domain\Task\Dependency\Firm\FirmFileInfoRepository;
use Firm\Domain\Task\Dependency\Firm\Program\Mission\LearningMaterialRepository;

class UpdateLearningMaterialTask implements FirmTaskExecutableByManager
{

    /**
     * 
     * @var LearningMaterialRepository
     */
    protected $learningMaterialRepository;

    /**
     * 
     * @var FirmFileInfoRepository
     */
    protected $firmFileInfoRepository;

    /**
     * 
     * @var UpdateLearningMaterialPayload
     */
    protected $payload;

    public function __construct(
            LearningMaterialRepository $learningMaterialRepository, FirmFileInfoRepository $firmFileInfoRepository,
            UpdateLearningMaterialPayload $payload)
    {
        $this->learningMaterialRepository = $learningMaterialRepository;
        $this->firmFileInfoRepository = $firmFileInfoRepository;
        $this->payload = $payload;
    }

    public function executeInFirm(Firm $firm): void
    {
        $name = $this->payload->getLearningMaterialRequest()->getName();
        $content = $this->payload->getLearningMaterialRequest()->getContent();
        $data = new LearningMaterialData($name, $content);
        foreach ($this->payload->getLearningMaterialRequest()->getAttachedFirmFileInfoIdList() as $firmFileInfoId) {
            $firmFileInfo = $this->firmFileInfoRepository->ofId($firmFileInfoId);
            $firmFileInfo->assertUsableInFirm($firm);
            $data->addAttachment($firmFileInfo);
        }
        $learningMaterial = $this->learningMaterialRepository->aLearningMaterialOfId($this->payload->getId());
        $learningMaterial->assertAccessibleInFirm($firm);
        $learningMaterial->update($data);
    }

}
