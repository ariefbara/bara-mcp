<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\Program\Mission\LearningMaterialData;
use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class UpdateLearningMaterialTaskTest extends FirmTaskTestBase
{
    protected $task;
    protected $payload;
    protected $learningMaterialData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setLearningMaterialRelatedDependency();
        $this->setFirmFileInfoRelatedDependency();
        
        $learningMaterialRequest = new LearningMaterialRequest('name', 'content');
        $learningMaterialRequest->attachFirmFileInfoId($this->firmFileInfoId);
        $this->payload = new UpdateLearningMaterialPayload($this->learningMaterialId, $learningMaterialRequest);
        $this->task = new UpdateLearningMaterialTask($this->learningMaterialRepository, $this->firmFileInfoRepository, $this->payload);
        
        $this->learningMaterialData = new LearningMaterialData('name', 'content');
        $this->learningMaterialData->addAttachment($this->firmFileInfo);
    }
    
    protected function executeInFirm()
    {
        $this->task->executeInFirm($this->firm);
    }
    public function test_execute_updateLearningMaterial()
    {
        $this->learningMaterial->expects($this->once())
                ->method('update')
                ->with($this->learningMaterialData);
        $this->executeInFirm();
    }
    public function test_execute_assertLearningMaterialAccessibleInFirm()
    {
        $this->learningMaterial->expects($this->once())
                ->method('assertAccessibleInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
    public function test_execute_assertFirmFileInfOUsable()
    {
        $this->firmFileInfo->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
}
