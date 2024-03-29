<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Task\Personnel\PersonnelTask;
use Resources\Domain\ValueObject\Password;
use Tests\TestBase;

class PersonnelTest extends TestBase
{
    protected $password;
    protected $personnel;
    protected $task;
    protected $personnelTask, $payload = 'string represent task payload';

    protected function setUp(): void
    {
        parent::setUp();
        $this->password = $this->buildMockOfClass(Password::class);
        $this->personnel = new TestablePersonnel();
        $this->personnel->password = $this->password;
        
        $this->task = $this->buildMockOfInterface(TaskExecutableByPersonnel::class);
        
        $this->personnelTask = $this->buildMockOfInterface(PersonnelTask::class);
    }
    
    public function test_passwordMatches_returnPasswordMatchComparisonResult()
    {
        $this->password->expects($this->once())
                ->method('match')
                ->with($password = 'password')
                ->willReturn(true);
        $this->assertTrue($this->personnel->passwordMatches($password));
    }
    
    protected function executeTask()
    {
        $this->personnel->executeTask($this->task);
    }
    public function test_executeTask_executeTask()
    {
        $this->task->expects($this->once())
                ->method('execute')
                ->with($this->personnel->id);
        $this->executeTask();
    }
    public function test_executeTask_inactivePersonnel_forbidden()
    {
        $this->personnel->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->executeTask();
        }, 'Forbidden', 'forbidden: only active personnel can make this request');
    }
    
    protected function executePersonnelTask()
    {
        $this->personnel->executePersonnelTask($this->personnelTask, $this->payload);
    }
    public function test_executePersonnelTask_executeTask()
    {
        $this->personnelTask->expects($this->once())
                ->method('execute')
                ->with($this->personnel->id, $this->payload);
        $this->executePersonnelTask();
    }
    public function test_executePersonnelTask_inactivePersonnel_forbidden()
    {
        $this->personnel->active = false;
        $this->assertRegularExceptionThrowed(function() {
            $this->executePersonnelTask();
        }, 'Forbidden', 'forbidden: only active personnel can make this request');
    }
}

class TestablePersonnel extends Personnel
{
    public $id = 'personnelId';
    public $password;
    public $active = true;
    
    function __construct()
    {
        parent::__construct();
    }
}
