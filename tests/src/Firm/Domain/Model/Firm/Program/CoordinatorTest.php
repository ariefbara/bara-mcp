<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\{
    Personnel,
    Program
};
use Tests\TestBase;

class CoordinatorTest extends TestBase
{

    protected $program;
    protected $id = 'coordinator-id';
    protected $personnel;
    protected $coordinator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);

        $this->coordinator = new TestableCoordinator($this->program, 'id', $this->personnel);
    }

    public function test_construct_setProperties()
    {
        $coordinator = new TestableCoordinator($this->program, $this->id, $this->personnel);
        $this->assertEquals($this->program, $coordinator->program);
        $this->assertEquals($this->id, $coordinator->id);
        $this->assertEquals($this->personnel, $coordinator->personnel);
        $this->assertFalse($coordinator->removed);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->coordinator->remove();
        $this->assertTrue($this->coordinator->removed);
    }

    public function test_reassign_setRemovedFlagFalse()
    {
        $this->coordinator->removed = true;
        $this->coordinator->reassign();
        $this->assertFalse($this->coordinator->removed);
    }

}

class TestableCoordinator extends Coordinator
{

    public $program, $id, $personnel, $removed;

}
