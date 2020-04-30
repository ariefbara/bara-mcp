<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class RegistrantAcceptTest extends TestBase
{
    protected $service;
    protected $incubatorId = 'incubatorId';
    protected $programRepository, $program, $programId = 'programId';
    protected $dispatcher;
    protected $registrantId = 'registrantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programRepository = $this->buildMockOfInterface(ProgramRepository::class);
        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository->expects($this->any())
                ->method('ofId')
                ->with($this->incubatorId, $this->programId)
                ->willReturn($this->program);
        
        $this->dispatcher = $this->buildMockOfInterface(Dispatcher::class);
        
        $this->service = new RegistrantAccept($this->programRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->incubatorId, $this->programId, $this->registrantId);
    }
    public function test_execute_executeProgramsAcceptRegistrantMethod()
    {
        $this->program->expects($this->once())
                ->method('acceptRegistrant')
                ->with($this->registrantId);
        $this->execute();
    }
    public function test_execute_updateProgramRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    public function test_execute_dispatchDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->program);
        $this->execute();
    }
}
