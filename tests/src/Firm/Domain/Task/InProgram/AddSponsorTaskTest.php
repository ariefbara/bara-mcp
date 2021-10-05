<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program\SponsorData;
use Tests\src\Firm\Domain\Task\InProgram\SponsorTaskTestBase;

class AddSponsorTaskTest extends SponsorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new AddSponsorTask($this->sponsorRepository, $this->firmFileInfoRepository, $this->sponsorRequest);
    }
    
    protected function executeInProgram()
    {
        $this->task->executeInProgram($this->program);
    }
    public function test_executeInProgram_addSponsorCreatedInProgramToRepository()
    {
        $this->program->expects($this->once())
                ->method('createSponsor')
                ->with($this->sponsorId, $this->sponsorData)
                ->willReturn($this->sponsor);
        $this->sponsorRepository->expects($this->once())
                ->method('add')
                ->with($this->sponsor);
        $this->executeInProgram();
    }
    public function test_executeInProgram_setCreatedSponsorId()
    {
        $this->executeInProgram();
        $this->assertEquals($this->sponsorId, $this->task->createdSponsorId);
    }
    public function test_executeInProgram_emptyLogo_setNullSponsorLogo()
    {
        $sponsorRequest = new SponsorRequest('name', 'http://web.sponsor.id', null);
        $task = new AddSponsorTask($this->sponsorRepository, $this->firmFileInfoRepository, $sponsorRequest);
        
        $this->firmFileInfoRepository->expects($this->never())
                ->method('ofId');
        
        $sponsorData = new SponsorData($sponsorRequest->getName(), null, $sponsorRequest->getWebsite());
        $this->program->expects($this->once())
                ->method('createSponsor')
                ->with($this->sponsorId, $sponsorData);
        
        $task->executeInProgram($this->program);
    }
}
