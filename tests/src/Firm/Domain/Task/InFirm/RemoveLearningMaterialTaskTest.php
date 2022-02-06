<?php

namespace Firm\Domain\Task\InFirm;

use Tests\src\Firm\Domain\Task\InFirm\FirmTaskTestBase;

class RemoveLearningMaterialTaskTest extends FirmTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->setLearningMaterialRelatedDependency();
        
        $this->task = new RemoveLearningMaterialTask($this->learningMaterialRepository, $this->learningMaterialId);
    }
    
    protected function execute()
    {
        $this->task->executeInFirm($this->firm);
    }
    public function test_execute_disableLearningMaterial()
    {
        $this->learningMaterial->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_assertLearningMaterialAccessibleInFirm()
    {
        $this->learningMaterial->expects($this->once())
                ->method('assertAccessibleInFirm')
                ->with($this->firm);
        $this->execute();
    }
}
