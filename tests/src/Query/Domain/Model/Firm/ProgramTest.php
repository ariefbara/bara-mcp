<?php

namespace Query\Domain\Model\Firm;

use Query\Domain\Model\Firm;
use Tests\TestBase;

class ProgramTest extends TestBase
{
    protected $firm;
    protected $program;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->firm = $this->buildMockOfClass(Firm::class);
        $this->program = new TestableProgram();
        $this->program->firm = $this->firm;
    }
    
    protected function firmEquals()
    {
        return $this->program->firmEquals($this->firm);
    }
    public function test_firmEquals_sameFirm_returnTrue()
    {
        $this->assertTrue($this->firmEquals());
    }
    public function test_firmEquals_differentFirm_returnFalse()
    {
        $this->program->firm = $this->buildMockOfClass(Firm::class);
        $this->assertFalse($this->firmEquals());
    }
}

class TestableProgram extends Program
{
    public $firm;
    public $id;
    public $name;
    public $description = null;
    public $participantTypes;
    public $published = false;
    public $strictMissionOrder;
    public $removed = false;
    public $profileForms;
    public $sponsors;
    
    function __construct()
    {
        parent::__construct();
    }
}
