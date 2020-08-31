<?php

namespace Firm\Application\Service\Firm\Program;

use Firm\ {
    Application\Service\Firm\ProgramRepository,
    Domain\Model\Firm\Program
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class AcceptUserRegistrationTest extends TestBase
{
    protected $service;
    protected $programRepository, $program;
    protected $dispatcher;
    
    protected $firmId = 'firmId', $programId = 'programId', $userRegistrantId = 'userRegistrantId';
    
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
        
        $this->service = new AcceptUserRegistration($this->programRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->programId, $this->userRegistrantId);
    }
    public function test_execute_executeProgramsAcceptUserRegistrationMethod()
    {
        $this->program->expects($this->once())
                ->method('acceptUserRegistration')
                ->with($this->userRegistrantId);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->programRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    public function test_execute_dispatcheProgramToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->program);
        $this->execute();
    }
    
}
