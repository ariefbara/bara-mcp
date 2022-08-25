<?php

namespace Tests\src\Firm\Domain\Task\InProgram;

use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Task\Dependency\Firm\Program\RegistrantRepository;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestBase;

class TaskInProgramTestBase extends TestBase
{
    /**
     * 
     * @var MockObject
     */
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
    }
    
    /**
     * 
     * @var MockObject
     */
    protected $registrantRepository;
    /**
     * 
     * @var MockObject
     */
    protected $registrant;
    protected $registrantId = 'registrantId';
    protected function prepareRegistrantDependency()
    {
        $this->registrantRepository = $this->buildMockOfInterface(RegistrantRepository::class);
        $this->registrant = $this->buildMockOfClass(Program\Registrant::class);
        $this->registrantRepository->expects($this->any())
                ->method('aRegistrantOfId')
                ->with($this->registrantId)
                ->willReturn($this->registrant);
    }
}
