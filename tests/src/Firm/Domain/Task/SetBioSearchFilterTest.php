<?php

namespace Firm\Domain\Task;

use Firm\Domain\Model\Firm;
use Tests\TestBase;

class SetBioSearchFilterTest extends TestBase
{
    protected $bioSearchFilterDataBuilder;
    protected $task;
    protected $firm;
    protected $bioSearchFilterData;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->bioSearchFilterDataBuilder = $this->buildMockOfClass(BioSearchFilterDataBuilder::class);
        $this->task = new SetBioSearchFilter($this->bioSearchFilterDataBuilder);
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->bioSearchFilterData = $this->buildMockOfClass(Firm\BioSearchFilterData::class);
    }
    
    protected function execute()
    {
        $this->task->execute($this->firm);
    }
    public function test_execute_setFirmsBioSearchFilter()
    {
        $this->bioSearchFilterDataBuilder->expects($this->once())
                ->method('build')
                ->with($this->firm)
                ->willReturn($this->bioSearchFilterData);
        
        $this->firm->expects($this->once())
                ->method('setBioSearchFilter')
                ->with($this->bioSearchFilterData);
        $this->execute();
    }
    
}
