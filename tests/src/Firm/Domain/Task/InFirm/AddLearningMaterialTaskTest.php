<?php

namespace Firm\Domain\Task\InFirm;

use Firm\Domain\Model\Firm\Program\Mission\LearningMaterialData;
use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class AddLearningMaterialTaskTest extends FirmTaskTestBase
{
    protected $task;
    protected $payload;
    protected $learningMaterialData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setLearningMaterialRelatedDependency();
        $this->setMissionRelatedDependency();
        $this->setFirmFileInfoRelatedDependency();
        
        $learningMaterialRequest = new LearningMaterialRequest('name', 'content');
        $learningMaterialRequest->attachFirmFileInfoId($this->firmFileInfoId);
        $this->payload = new AddLearningMaterialPayload($this->missionId, $learningMaterialRequest);
        
        $this->task = new AddLearningMaterialTask(
                $this->learningMaterialRepository, $this->missionRepository, $this->firmFileInfoRepository, $this->payload);
        
        $this->learningMaterialData = new LearningMaterialData('name', 'content');
        $this->learningMaterialData->addAttachment($this->firmFileInfo);
    }
    
    protected function executeInFirm()
    {
        $this->learningMaterialRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->learningMaterialId);
        $this->task->executeInFirm($this->firm);
    }
    public function test_execute_addLearningMaterialCreatedInMissionToRepository()
    {
        $this->mission->expects($this->once())
                ->method('addLearningMaterial')
                ->with($this->learningMaterialId, $this->learningMaterialData)
                ->willReturn($this->learningMaterial);
        
        $this->learningMaterialRepository->expects($this->once())
                ->method('add')
                ->with($this->learningMaterial);
        
        $this->executeInFirm();
    }
    public function test_execute_setAddedLearningMaterialId()
    {
        $this->executeInFirm();
        $this->assertSame($this->learningMaterialId, $this->task->addedLearningMaterialId);
    }
    public function test_execute_assertMissionAccessibleInFirm()
    {
        $this->mission->expects($this->once())
                ->method('assertAccessibleInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
    public function test_execute_assertFirmFileInfoUsable()
    {
        $this->firmFileInfo->expects($this->once())
                ->method('assertUsableInFirm')
                ->with($this->firm);
        $this->executeInFirm();
    }
}
