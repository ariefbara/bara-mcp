<?php

namespace Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program\SponsorData;
use Tests\src\Firm\Domain\Task\InProgram\SponsorTaskTestBase;

class UpdateSponsorTaskTest extends SponsorTaskTestBase
{
    protected $task;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->task = new UpdateSponsorTask(
                $this->sponsorRepository, $this->firmFileInfoRepository, $this->sponsorId, $this->sponsorRequest);
    }
    
    protected function executeInProgram()
    {
        $this->task->executeInProgram($this->program);
    }
    public function test_executeInProgram_updateSponsor()
    {
        $this->sponsor->expects($this->once())
                ->method('update')
                ->with($this->sponsorData);
        $this->executeInProgram();
    }
    public function test_executeInProgram_assertSponsorIsManageable()
    {
        $this->sponsor->expects($this->once())
                ->method('assertManageableInProgram')
                ->with($this->program);
        $this->executeInProgram();
    }
    public function test_executeInProgram_emptyLogo_setNullSponsorLogo()
    {
        $sponsorRequest = new SponsorRequest('name', 'http://web.sponsor.id', null);
        $task = new UpdateSponsorTask($this->sponsorRepository, $this->firmFileInfoRepository, $this->sponsorId, $sponsorRequest);
        
        $this->firmFileInfoRepository->expects($this->never())
                ->method('ofId');
        
        $sponsorData = new SponsorData($sponsorRequest->getName(), null, $sponsorRequest->getWebsite());
        $this->sponsor->expects($this->once())
                ->method('update')
                ->with($sponsorData);
        $task->executeInProgram($this->program);
    }
}
