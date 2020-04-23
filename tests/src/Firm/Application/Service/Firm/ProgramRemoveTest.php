<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\Program;
use Tests\TestBase;

class ProgramRemoveTest extends TestBase
{
    protected $service;
    protected $firmId = 'firmId';
    protected $programRepository, $program, $programId = 'programId';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
            ->method('ofId')
            ->with($this->firmId, $this->programId)
            ->willReturn($this->program);
        
        $this->service = new ProgramRemove($this->programRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId);
    }
    
    public function test_remove_removeProgram()
    {
        $this->program->expects($this->once())
            ->method('remove');
        $this->execute();
    }
    public function test_remove_updateRepository()
    {
        $this->programRepository->expects($this->once())
            ->method('update');
        $this->execute();
    }
    
}
