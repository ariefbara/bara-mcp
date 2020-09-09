<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class AcceptRegistrantTest extends TestBase
{
    protected $service;
    protected $programRepository, $program;
    protected $dispatcher;
    
    protected $firmId = 'firmId', $programId = 'programId', $registrantId = 'registrantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId)
                ->willReturn($this->program);
        
        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);
        
        $this->service = new AcceptRegistrant($this->programRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId, $this->registrantId);
    }
    public function test_execute_executeProgramsAcceptRegistrantMethod()
    {
        $this->program->expects($this->once())
                ->method('acceptRegistrant')
                ->with($this->registrantId);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    public function test_execute_dispatchProgramToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->program);
        $this->execute();
    }
}
