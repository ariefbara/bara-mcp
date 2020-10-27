<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\ {
    Personnel,
    Program
};
use Resources\Domain\Model\Mail\Recipient;
use Tests\TestBase;

class ConsultantTest extends TestBase
{

    protected $program;
    protected $id = 'consultant-id';
    protected $personnel;
    protected $consultant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->consultant = new TestableConsultant($this->program, 'id', $this->personnel);
    }

    public function test_construct_setProperties()
    {
        $consultant = new TestableConsultant($this->program, $this->id, $this->personnel);
        $this->assertEquals($this->program, $consultant->program);
        $this->assertEquals($this->id, $consultant->id);
        $this->assertEquals($this->personnel, $consultant->personnel);
        $this->assertFalse($consultant->removed);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->consultant->remove();
        $this->assertTrue($this->consultant->removed);
    }

    public function test_reassign_setRemovedFlagFalse()
    {
        $this->consultant->removed = true;
        $this->consultant->reassign();
        $this->assertFalse($this->consultant->removed);
    }
    
    public function test_getPersonnelName_returnPersonnelsGetNameResult()
    {
        $this->personnel->expects($this->once())
                ->method('getName')
                ->willReturn($name = 'hadi pranoto');
        $this->assertEquals($name, $this->consultant->getPersonnelName());
    }

}

class TestableConsultant extends Consultant
{

    public $program, $id, $personnel, $removed;

}
