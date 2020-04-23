<?php

namespace Firm\Domain\Model\Firm\Program;

use Firm\Domain\Model\Firm\{
    Personnel,
    Program
};
use Tests\TestBase;

class MentorTest extends TestBase
{

    protected $program;
    protected $id = 'mentor-id';
    protected $personnel;
    protected $mentor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->program = $this->buildMockOfClass(Program::class);
        $this->personnel = $this->buildMockOfClass(Personnel::class);
        $this->mentor = new TestableMentor($this->program, 'id', $this->personnel);
    }

    public function test_construct_setProperties()
    {
        $mentor = new TestableMentor($this->program, $this->id, $this->personnel);
        $this->assertEquals($this->program, $mentor->program);
        $this->assertEquals($this->id, $mentor->id);
        $this->assertEquals($this->personnel, $mentor->personnel);
        $this->assertFalse($mentor->removed);
    }

    public function test_remove_setRemovedFlagTrue()
    {
        $this->mentor->remove();
        $this->assertTrue($this->mentor->removed);
    }

    public function test_reassign_setRemovedFlagFalse()
    {
        $this->mentor->removed = true;
        $this->mentor->reassign();
        $this->assertFalse($this->mentor->removed);
    }

}

class TestableMentor extends Mentor
{

    public $program, $id, $personnel, $removed;

}
