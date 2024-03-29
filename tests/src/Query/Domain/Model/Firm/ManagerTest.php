<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm;
use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class ManagerTest extends TestBase
{
    protected $password;
    protected $manager;
    protected $firm;
    
    protected $task;
    protected $program, $taskInProgram;

    protected function setUp(): void
    {
        parent::setUp();
        $this->password = $this->buildMockOfClass(Password::class);
        $this->manager = new TestableManager();
        $this->manager->password = $this->password;
        
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->manager->firm = $this->firm;
        
        $this->task = $this->buildMockOfInterface(ITaskInFirmExecutableByManager::class);
        
        $this->program = $this->buildMockOfClass(Program::class);
        $this->taskInProgram = $this->buildMockOfInterface(ITaskInProgramExecutableByManager::class);
    }
    
    public function test_passwordMatcher_returnPasswordMatchComparisonResult()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = "password")
                ->willReturn(true);
        $this->assertTrue($this->manager->passwordMatches($password));
    }
    
    protected function executeTaskInFirm()
    {
        $this->manager->executeTaskInFirm($this->task);
    }
    public function test_executeTaskInFirm_executeTask()
    {
        $this->task->expects($this->once())
                ->method('executeTaskInFirm')
                ->with($this->firm);
        $this->executeTaskInFirm();
    }
    public function test_executeTaskInFirm_removedManager_forbidden()
    {
        $this->manager->removed = true;
        $this->assertRegularExceptionThrowed(function (){
            $this->executeTaskInFirm();
        }, 'Forbidden', 'forbidden: only active manager can make this request');
    }
    
    protected function executeTaskInProgram()
    {
        $this->program->expects($this->any())
                ->method('firmEquals')
                ->with($this->manager->firm)
                ->willReturn(true);
        $this->manager->executeTaskInProgram($this->program, $this->taskInProgram);
    }
    public function test_executeTaskInProgram_executeTask()
    {
        $this->taskInProgram->expects($this->once())
                ->method('executeInProgram')
                ->with($this->program);
        $this->executeTaskInProgram();
    }
    public function test_executeTaskInProgram_assertProgramFromDifferentFirm_forbidden()
    {
        $this->program->expects($this->once())
                ->method('firmEquals')
                ->with($this->manager->firm)
                ->willReturn(false);
        $this->assertRegularExceptionThrowed(function (){
            $this->executeTaskInProgram();
        }, 'Forbidden', 'forbidden: unable to manage program, probably belongs to other firm');
    }
}

class TestableManager extends Manager
{
    public $firm;
    public $password;
    public $removed = false;
    
    public function __construct()
    {
        parent::__construct();
    }
}
